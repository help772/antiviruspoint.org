<?php

if (!class_exists('ALValidator')) :
	class ALValidator {
		public $cache_config;
		public $ignore_files;
		public $ignore_extensions;
		public $allowed_methods;
		public $allowed_query_params;
		public $cache_ssl;
		public $ignored_request_uri_regex;
		public $ignored_user_agents_for_optimization;
		public $do_not_cache;
		public $ignored_query_params_for_optimization;
		public $ignore_opt_for_all_query_params;
		public $skip_cookies_to_ignore_optimization;
		public $ignored_cookies_for_optimization;

		public function __construct($config) {
			$cache_config = isset($config['cache_params']) && is_array($config['cache_params']) ? $config['cache_params'] : null;
			if (!isset($cache_config)) {
				$this->do_not_cache = true;
			} else {
				$this->ignore_files = isset($cache_config['ignore_files']) ? $cache_config['ignore_files'] : array();
				$this->ignore_extensions = isset($cache_config['ignore_extensions']) ? $cache_config['ignore_extensions'] : array();
				$this->allowed_methods = isset($cache_config['allowed_methods']) ? $cache_config['allowed_methods'] : array();
				$this->allowed_query_params = isset($cache_config['allowed_query_params']) ? $cache_config['allowed_query_params'] : array();
				$this->cache_ssl = isset($cache_config['cache_ssl']) ? $cache_config['cache_ssl'] : array();
				$this->ignored_request_uri_regex = isset($cache_config['ignored_request_uri_regex']) ? $cache_config['ignored_request_uri_regex'] : array();
				$this->ignored_cookies_for_optimization = isset($cache_config['ignored_cookies_for_optimization'])
					? $cache_config['ignored_cookies_for_optimization']
					: (isset($cache_config['ignored_cookies']) ? $cache_config['ignored_cookies'] : array());
				$this->skip_cookies_to_ignore_optimization = isset($cache_config['skip_cookies_to_ignore_optimization'])
					? $cache_config['skip_cookies_to_ignore_optimization']
					: array();
				$this->ignored_user_agents_for_optimization = isset($cache_config['ignored_user_agents_for_optimization'])
					? $cache_config['ignored_user_agents_for_optimization']
					: (isset($cache_config['ignored_user_agents']) ? $cache_config['ignored_user_agents'] : array());
				$this->ignored_query_params_for_optimization = isset($cache_config['ignored_query_params_for_optimization'])
					? $cache_config['ignored_query_params_for_optimization']
					: (isset($cache_config['ignored_query_params']) ? $cache_config['ignored_query_params'] : array());
				$this->ignore_opt_for_all_query_params = isset($cache_config['ignore_opt_for_all_query_params'])
					? $cache_config['ignore_opt_for_all_query_params']
					: (isset($cache_config['ignore_all_query_params']) ? $cache_config['ignore_all_query_params'] : false);
				$this->skip_empty_user_agent_check = isset($cache_config['skip_empty_user_agent_check']) ? $cache_config['skip_empty_user_agent_check'] : false;
			}
		}

		public function isIgnoredFile() {
			$request_uri = ALCacheHelper::getRequestUriBase();
			foreach ($this->ignore_files as $file) {
				if (strpos($request_uri, '/' . $file)) {
					return true;
				}
			}
			return false;
		}

		public function isIgnoredExtension() {
			$request_uri = ALCacheHelper::getRequestUriBase();
			if (strtolower($request_uri) === '/index.php') {
				return false;
			}
			$extension = pathinfo($request_uri, PATHINFO_EXTENSION);
			return $extension && in_array($extension, $this->ignore_extensions);
		}

		public function isIgnoredRequestMethod() {
			$method = ALHelper::getRawParam('SERVER', 'REQUEST_METHOD');
			if (in_array($method, $this->allowed_methods)) {
				return false;
			}
			return true;
		}

		public function isIgnoredQueryString() {
			$al_debug_mode = ALHelper::getRawParam('GET', 'al_debug_mode');
			if (!empty($al_debug_mode)) {
				return false;
			}

			$params = ALCacheHelper::getQueryParams();
			if (!$params) {
				return false;
			}

			if (!!$this->ignore_opt_for_all_query_params) {
				return true;
			}
			if (array_intersect_key($params, array_flip($this->ignored_query_params_for_optimization))) {
				return true;
			}
			if (array_intersect_key($params, array_flip($this->allowed_query_params))) {
				return false;
			}
			return false;
		}

		public function canCacheSSL() {
			if (function_exists('is_ssl')) {
				return !is_ssl() || $this->cache_ssl;
			}
			return true;
		}

		public function isIgnoredRequestURI() {
			$request_uri = ALCacheHelper::getRequestURIBase();
			foreach ($this->ignored_request_uri_regex as $regex) {
				if (ALHelper::safePregMatch($regex, $request_uri)) {
					return true;
				}
			}
			return false;
		}

		public function hasIgnoredCookies() {
			if (!is_array($_COOKIE) || empty( $_COOKIE )) {
				return false;
			}

			foreach (array_keys($_COOKIE) as $cookie_name) {
				$is_skipped = false;
				
				// First check if cookie should be skipped from ignore list
				foreach ($this->skip_cookies_to_ignore_optimization as $skip_cookie) {
					if (ALHelper::safePregMatch($skip_cookie, $cookie_name)) {
						$is_skipped = true;
						break;
					}
				}

				// If cookie is not in skip list, check if it matches ignore patterns
				if (!$is_skipped) {
					foreach ($this->ignored_cookies_for_optimization as $ignored_cookie) {
						if (ALHelper::safePregMatch($ignored_cookie, $cookie_name)) {
							return true;
						}
					}
				}
			}
			return false;
		}

		public function hasIgnoredUserAgents() {
			$user_agent = ALHelper::getRawParam('SERVER', 'HTTP_USER_AGENT');
			if (!isset($user_agent)) {
				return $this->skip_empty_user_agent_check ? false : true;
			}
			foreach ($this->ignored_user_agents_for_optimization as $ignored_ua) {
				if (ALHelper::safePregMatch($ignored_ua, $user_agent)) {
					return true;
				}
			}
			return false;
		}

		public function hasDonotCachePage() {
			if (defined('AL_DONOTCACHEPAGE') && AL_DONOTCACHEPAGE) {
				return true;
			}
			return false;
		}

		public function shouldSkipOptimization() {
			if (defined('AL_DONOTOPTIMIZEPAGE') && AL_DONOTOPTIMIZEPAGE) {
				return true;
			}
			return false;
		}

		public function checkIfSearchQuery() {
			global $wp_query;
			if (!isset($wp_query)) {
				return false;
			}
			return $wp_query->is_search();
		}

		public function canOptimizeBuffer($buffer) {
			if (strlen($buffer) <= 255 || http_response_code() !== 200 || $this->shouldSkipOptimization() || $this->checkIfSearchQuery()) {
				return false;
			}
			return true;
		}

		public function canOptimizePage() {
			if ($this->do_not_cache || $this->isIgnoredFile() || $this->isIgnoredExtension() || $this->isIgnoredRequestMethod() ||
					is_admin() || $this->isIgnoredQueryString() || !$this->canCacheSSL() ||
					$this->isIgnoredRequestURI() || $this->hasIgnoredCookies() || $this->hasIgnoredUserAgents()) {
				return false;
			}
			return true;
		}
	}
endif;