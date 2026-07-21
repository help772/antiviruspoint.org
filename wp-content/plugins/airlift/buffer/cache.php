<?php

if (!defined('ABSPATH')) exit;
require_once dirname(__FILE__) . '/optimizer.php';
require_once dirname(__FILE__) . '/validator.php';
require_once dirname(__FILE__) . '/helper.php';
require_once dirname(__FILE__) . '/../wp_settings.php';
require_once dirname(__FILE__) . '/../wp_file_system.php';

if (!class_exists('ALCache')) :

	class ALCache {
		public $optimizer;
		public $validator;
		public $cache_filepath;
		public $cache_filepath_gzip;
		public $alsettings;
		public $airlift_print_buffer;
		public $airlift_fname;
		public $alinfo;
		public static $cacheconfig = "alcacheconfig";
		public static $airlift_optimization_option = "apply_airlift_optimizations";

		public function __construct() {
			$this->cache_filepath = $this->getCachePath();
			$this->cache_filepath_gzip = $this->cache_filepath . '_gzip';
			$this->alsettings = new ALWPSettings();
		}

		public function resetLowerCase($matches) {
			return strtolower($matches[0]);
		}

		public function getCachePath() {
			$request_uri_path = ALCacheHelper::getRequestCachePath();
			$filename = 'index';
			if (function_exists('is_ssl') && is_ssl()) {
				$filename .= '-https';
			}
			$request_uri_path = preg_replace_callback('/%[0-9A-F]{2}/', array($this, 'resetLowerCase'), $request_uri_path);
			$request_uri_path = str_replace('?', '#', $request_uri_path);
			$request_uri_path .= '/' . $filename . '.html';
			return $request_uri_path;
		}

		public function getIfModifiedSince() {
			if (function_exists('apache_request_headers')) {
				$headers = apache_request_headers();
				return isset($headers['If-Modified-Since']) ? $headers['If-Modified-Since'] : '';
			}
			$modified_since = ALHelper::getRawParam('SERVER', 'HTTP_IF_MODIFIED_SINCE');
			return isset($modified_since) ? $modified_since : '';
		}

		public function serveCacheFile($read_from_gzip) {
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($this->cache_filepath)) . ' GMT');
			$if_modified_since = $this->getIfModifiedSince();
			if ($if_modified_since && (strtotime($if_modified_since) === @filemtime($this->cache_filepath))) {
				header(ALHelper::getRawParam('SERVER', 'SERVER_PROTOCOL') . ' 304 Not Modified', true, 304);
				header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
				header('Cache-Control: no-cache, must-revalidate');
				exit;
			}
			$read_from_gzip ? readgzfile($this->cache_filepath_gzip) : readfile($this->cache_filepath);
			exit;
		}

		public function sanitizeBuffer($buffer, $is_gzip_buffer) {
			$sanitized_buffer = $buffer;
			if ($is_gzip_buffer) {
				$sanitized_buffer = gzdecode($sanitized_buffer);
			}
			return $sanitized_buffer;
		}

		public function parseAirliftParamsHeader() {
			if (isset($_SERVER['HTTP_AIRLIFT_PARAMS_HEADER'])) {
				$airlift_params_header = $_SERVER['HTTP_AIRLIFT_PARAMS_HEADER'];
				$parsed_airlift_headers = json_decode($airlift_params_header, true);
				if ($parsed_airlift_headers !== null && is_array($parsed_airlift_headers)) {
					if (isset($parsed_airlift_headers['airlift_print_buffer'])) {
						$this->airlift_print_buffer = $parsed_airlift_headers['airlift_print_buffer'];
					}
					if (isset($parsed_airlift_headers['airlift_fname'])) {
						$this->airlift_fname = $parsed_airlift_headers['airlift_fname'];
					}
				}
			} else {
				$airlift_print_buffer = ALHelper::getRawParam('GET', 'bv_print_buffer');
				if (!empty($airlift_print_buffer)) {
					$this->airlift_print_buffer = $airlift_print_buffer;
				}
				$airlift_fname = ALHelper::getRawParam('GET', 'fname');
				if (!empty($airlift_fname)) {
					$this->airlift_fname = $airlift_fname;
				}
			}
		}

		private function canServeCachedPage() {
			if (array_key_exists('al_cache_skip_cookies', $GLOBALS) && is_array($GLOBALS['al_cache_skip_cookies']) && is_array($_COOKIE)) {
				foreach ($_COOKIE as $cookie => $value) {
					if (in_array($cookie, $GLOBALS['al_cache_skip_cookies'], true)) {
						return false;
					}
				}
			}
			return true;
		}

		public function startCaching() {
			$al_debug_mode = ALHelper::getRawParam('GET', 'al_debug_mode');
			if (!empty($al_debug_mode)) {
				ob_start([$this, 'optimizePage']);
				return;
			}

			$this->parseAirliftParamsHeader();
			if (isset($this->airlift_print_buffer) && !empty($this->airlift_print_buffer)) {
				ob_start([$this, 'serveBuffer']);
				return;
			}

			if ($this->canServeCachedPage()) {
				$accept_encoding = ALHelper::getRawParam('SERVER', 'HTTP_ACCEPT_ENCODING');
				$read_from_gzip = $accept_encoding && false !== strpos($accept_encoding, 'gzip');
				if ($read_from_gzip && is_readable($this->cache_filepath_gzip)) {
					$this->serveCacheFile($read_from_gzip);
				}
				if (is_readable($this->cache_filepath)) {
					$this->serveCacheFile($read_from_gzip);
				}
			}
			ob_start([$this, 'optimizePage']);
		}

		public function isBufferInGzipFormat($buffer) {
			$magic_number = substr($buffer, 0, 2);
			return ($magic_number === "\x1f\x8b") ? true : false;
		}


		private function putContentsAndCreateDir($file, $contents, $file_perm = 0644, $dir_perm = 0755) {
			$dir = dirname($file);

			if (ALWPFileSystem::getInstance()->isDir($dir) === false) {
				if (mkdir($dir, $dir_perm, true) === false) {
					return false;
				}
			}

			if ((ALWPFileSystem::getInstance()->exists($file) === true) &&
					(ALWPFileSystem::getInstance()->isWritable($file) === false)) {
				return false;
			}

			return ALWPFileSystem::getInstance()->putContents($file, $contents, $file_perm);
		}

		public function serveBuffer($buffer) {
			$original_buffer = $buffer;
			$is_gzip_buffer = $this->isBufferInGzipFormat($buffer);
			if ($is_gzip_buffer) {
				$buffer = $this->sanitizeBuffer($buffer, $is_gzip_buffer);
				if ($buffer === false) {
					return $original_buffer;
				}
			}
			if (isset($this->airlift_fname) && !empty($this->airlift_fname)) {
				$fname = md5($this->airlift_fname);
				$fullpath = ALCacheHelper::getCacheBasePath() . 'buffer/';
				$this->putContentsAndCreateDir($fullpath . $fname, $buffer);
			}
			if ($is_gzip_buffer) {
				$buffer = gzencode($buffer, 6);
			}
			return $buffer;
		}

		public function writeCacheFile($content) {
			$this->putContentsAndCreateDir($this->cache_filepath, $content);
			$writtenContent = ALWPFileSystem::getInstance()->getContents($this->cache_filepath);
			if ($writtenContent === false || !is_string($writtenContent) ||  strlen($writtenContent) !== strlen($content)) {
				ALWPFileSystem::getInstance()->removeFile($this->cache_filepath);
			}

			if (function_exists('gzencode')) {
				$gzippedContent = gzencode($content, 6);
				$this->putContentsAndCreateDir($this->cache_filepath_gzip, $gzippedContent);
				$writtenGzippedContent = ALWPFileSystem::getInstance()->getContents($this->cache_filepath_gzip);
				if ($writtenGzippedContent === false || !is_string($writtenGzippedContent) || strlen($writtenGzippedContent) !== strlen($gzippedContent)) {
					ALWPFileSystem::getInstance()->removeFile($this->cache_filepath_gzip);
				}
			}
		}

		private function shouldPerformCaching($config) {
			$al_debug_mode = ALHelper::getRawParam('GET', 'al_debug_mode');
			if (!empty($al_debug_mode)) {
				return false;
			}

			$bv_is_third_party_host = $this->alsettings->getOption("bv_is_third_party_host");
			if (isset($bv_is_third_party_host) && $bv_is_third_party_host === "true") {
				return false;
			}

			if (array_key_exists('al_cache_skip_cookies', $GLOBALS) && is_array($GLOBALS['al_cache_skip_cookies']) && is_array($_COOKIE)) {
				foreach ($_COOKIE as $cookie => $value) {
					if (in_array($cookie, $GLOBALS['al_cache_skip_cookies'], true)) {
						return false;
					}
				}
			}

			return $this->canCacheWithQueryParams($config);
		}

		private function canCacheWithQueryParams($config) {
			if (!$config || !is_array($config) || !isset($config['cache_params']) || !is_array($config['cache_params'])) {
				return false;
			}

			$params = ALCacheHelper::getQueryParams();
			if (!$params) {
				return true;
			}

			$ignore_for_all_params = isset($config['cache_params']['ignore_caching_for_all_query_params'])
				? $config['cache_params']['ignore_caching_for_all_query_params']
				: false;

			return !$ignore_for_all_params;
		}

		public function hasIgnoredWordPressContext() {
			if (function_exists('is_admin') && is_admin()) {
				return true;
			}
			if (defined('DOING_AJAX') && DOING_AJAX) {
				return true;
			}
			if (defined('WP_CLI') && WP_CLI) {
				return true;
			}
			if (defined('DOING_CRON') && DOING_CRON) {
				return true;
			}
			return false;
		}

		public function can_apply_optimization() {
			$al_debug_mode = ALHelper::getRawParam('GET', 'al_debug_mode');
			if (!empty($al_debug_mode)) {
				return true;
			}

			if ($this->hasIgnoredWordPressContext()) {
				return false;
			}

			if($this->alsettings->getOption(self::$airlift_optimization_option) === "true") {
				return true;
			}
			
			return false;
		}

		public function optimizePage($buffer) {
			$apply_airlift_optimization = $this->can_apply_optimization();
			if(!$apply_airlift_optimization) {
				return $buffer;
			}

			$original_buffer = $buffer;
			$is_gzip_buffer = $this->isBufferInGzipFormat($buffer);
			if ($is_gzip_buffer) {
				$buffer = $this->sanitizeBuffer($buffer, $is_gzip_buffer);
				if ($buffer === false) {
					return $original_buffer;
				}
			}

			$config = $this->alsettings->getOption(self::$cacheconfig);

			if ($config == false) {
				return $buffer;
			}

			$this->validator = new ALValidator($config);
			$this->alinfo = new ALInfo($this->alsettings);
			$this->optimizer = new ALOptimizer($config, $this->alinfo);
			if (!$this->validator->canOptimizeBuffer($buffer) || !$this->validator->canOptimizePage()) {
				return $buffer;
			}

			$buffer = $this->optimizer->optimizeBuffer($buffer);
			$optimized_buffer_copy = $buffer;

			if ($this->shouldPerformCaching($config)) {
				$this->writeCacheFile($buffer);
			}

			if ($is_gzip_buffer) {
				$buffer = gzencode($buffer, 6);
				if ($buffer === false || !is_string($buffer) || strlen($buffer) == 0) {
					$buffer = $optimized_buffer_copy;
					$buffer = $buffer . '<!-- BUFFER_IS_NOT_GZIP_ENCODED -->';
					header_remove('Content-Encoding');
				}
			}
			return $buffer;
		}
	}
endif;