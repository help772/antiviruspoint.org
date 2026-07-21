<?php

namespace WcPaysafe;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoloader for the plugin classes.
 * Looks for all directories and files in the plugin\includes folder.
 * Loads only classes starting with the PREFIX string.
 *
 * @version  2.3.0
 * @author   VanboDevelops | Ivan Andreev
 *
 *        Copyright: (c) 2015-2020 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Autoloader {
	
	/**
	 * The extension class prefix
	 */
	const PREFIX = 'WC_Paysafe_';
	/**
	 * @const string The main namespace
	 */
	const MAIN_NAMESPACE = 'WcPaysafe';
	/**
	 * The classes path
	 * @var string
	 */
	private $path;
	/**
	 * Folder path to the file
	 * @var string
	 */
	private $folder_path;
	/**
	 * The string of the mapped files and folders
	 * @var mixed|void
	 */
	private $files_and_folders;
	/**
	 * The path to the includes folder.
	 * This path is generated from the passed $path and $folder parameters to the class constructor
	 * @var string
	 */
	private $path_to_includes;
	/**
	 * The Version of the mapped files and folders
	 * @var mixed|void
	 */
	private $files_and_folders_version;
	/**
	 * The version of the plugin files. Passed parameter
	 * @var string
	 */
	private $files_version;
	/**
	 * The name of the files and folders option
	 * @var string
	 */
	private $files_and_folders_name;
	/**
	 * The name of the files and folders version option
	 * @var string
	 */
	private $files_and_folders_version_name;
	/**
	 * The path to the plugins directory on the server
	 * @since 2.3
	 * @var string
	 */
	private $plugins_path;
	
	/**
	 * Constructor
	 *
	 * @param string $path
	 * @param string $files_version
	 * @param string $folder
	 */
	public function __construct( $path, $files_version, $folder = 'includes' ) {
		$this->path             = $path;
		$this->folder_path      = $folder;
		$this->files_version    = $files_version;
		$this->path_to_includes = $this->path . DIRECTORY_SEPARATOR . $this->folder_path;
		$this->plugins_path     = dirname( $this->path );
		
		// Get the file structure
		$this->files_and_folders = get_option( $this->get_files_and_folders_name(), array() );
		
		// Get the file structure version
		$this->files_and_folders_version = get_option( $this->get_files_and_folders_version_name(), '0.0.2' );
	}
	
	/**
	 * Returns the option name for the files and folders mapping
	 *
	 * @return string
	 */
	public function get_files_and_folders_name() {
		if ( null == $this->files_and_folders_name ) {
			$this->files_and_folders_name = strtolower( self::PREFIX ) . 'files';
		}
		
		return $this->files_and_folders_name;
	}
	
	/**
	 * Returns the option name for the files and folders version
	 *
	 * @return string
	 */
	public function get_files_and_folders_version_name() {
		if ( null == $this->files_and_folders_version_name ) {
			$this->files_and_folders_version_name = strtolower( self::PREFIX ) . 'files_version';
		}
		
		return $this->files_and_folders_version_name;
	}
	
	/**
	 * Returns the option name for the includes real path
	 *
	 * @return string
	 */
	public function get_includes_real_path_option_name() {
		if ( null == $this->real_path_option_name ) {
			$this->real_path_option_name = strtolower( self::PREFIX ) . 'real_path';
		}
		
		return $this->real_path_option_name;
	}
	
	/**
	 * Include the plugin classes
	 *
	 * @param $class_name
	 *
	 * @return bool
	 */
	public function load_classes( $class_name ) {
		
		if ( false !== strpos( $class_name, "\\" ) ) {
			$name_to_load = $class_name;
			if ( substr( $class_name, 0, strlen( self::MAIN_NAMESPACE . '\\' ) ) == self::MAIN_NAMESPACE . '\\' ) {
				$name_to_load = substr( $class_name, strlen( self::MAIN_NAMESPACE . '\\' ) );
			}
			if ( 0 === strpos( trim( $class_name, '\\' ), self::MAIN_NAMESPACE . '\\' ) ) {
				return $this->load_namespaced_class( $name_to_load );
			}
		}
		
		return $this->load_non_namespaced_class( $class_name );
	}
	
	/**
	 * Loads the non namespaced classes
	 *
	 * @param $class_name
	 *
	 * @return bool|void
	 */
	private function load_non_namespaced_class( $class_name ) {
		// We will include only our classes
		if ( 0 !== strpos( $class_name, self::PREFIX ) ) {
			return;
		}
		
		// Path to the classes folder
		// If the files and folders version is different, then regenerate them
		if ( version_compare( $this->files_and_folders_version, $this->files_version, '!=' ) ) {
			$this->regenerate_directory_contents();
		}
		
		// If we still have an empty files and folders, regenerate them
		if ( empty( $this->files_and_folders ) ) {
			$this->files_and_folders = $this->get_dir_contents( $this->path_to_includes );
			// Save the results of the contents
			$this->update_folders_and_files_contents( $this->files_and_folders );
		}
		
		// The file name we want to load
		$include_file_name = 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';
		
		// Look through all files and folders
		foreach ( $this->files_and_folders as $path ) {
			$file_name = basename( $path );
			
			// Match the file by name
			if ( $include_file_name === $file_name ) {
				
				require_once( $this->plugins_path . DIRECTORY_SEPARATOR . $path );
				
				return true;
			}
		}
		
		/**
		 * We should never get to here, but in case something happens and we do,
		 * we want to make sure that the version is reset and on next load we will remap the files and folders
		 */
		$this->update_files_and_folders_version( '0.0.1' );
		
		return false;
	}
	
	/**
	 * Loads the namespaced class.
	 * The main namespace (self::MAIN_NAMESPACE) will be omitted from the directories.
	 *
	 * Example:
	 * self::MAIN_NAMESPACE = "Class"; - The name of the Main namespace
	 * $class_name = \Class\Admin\Hello; - The name of the class
	 * $includes_path = path/plugin/includes; - The includes path
	 * $path = path/plugin/includes/admin/class-hello.php - The path of the file
	 *
	 * @param $class_name
	 *
	 * @return bool
	 */
	private function load_namespaced_class( $class_name ) {
		$file_dir = str_replace( array( '_', '\\' ), array( '-', DIRECTORY_SEPARATOR ), $class_name );
		$file     = strtolower( $file_dir ) . ".php";
		$path     = realpath( $this->path_to_includes . DIRECTORY_SEPARATOR . $file );
		
		if ( $path ) {
			return require_once( $path );
		}
		
		return false;
	}
	
	/**
	 * Scans the plugin directories and returns all files and directories
	 *
	 * @param string $path_to_includes
	 * @param array  $results
	 *
	 * @return array
	 */
	private function get_dir_contents( $path_to_includes, &$results = array() ) {
		$files = scandir( $path_to_includes );
		
		foreach ( $files as $key => $value ) {
			$path = realpath( $path_to_includes . DIRECTORY_SEPARATOR . $value );
			if ( ! is_dir( $path ) && false !== strpos( $path, '.php' ) ) {
				$results[] = str_replace( $this->plugins_path, '', $path );
			} else if ( $value != "." && $value != ".." ) {
				$this->get_dir_contents( $path, $results );
			}
		}
		
		return $results;
	}
	
	/**
	 * Regenerates the directory contents and saves the version and include path for them
	 * @since 2.2
	 */
	public function regenerate_directory_contents() {
		$this->files_and_folders = $this->get_dir_contents( $this->path_to_includes );
		// Save the results of the contents
		$this->update_folders_and_files_contents( $this->files_and_folders );
		$this->update_files_and_folders_version( $this->files_version );
		$this->files_and_folders_version = $this->files_version;
	}
	
	/**
	 * Updates the option for the files and folders version
	 *
	 * @param $version
	 */
	public function update_files_and_folders_version( $version ) {
		update_option( $this->get_files_and_folders_version_name(), $version );
	}
	
	/**
	 * Updates the files and folders option in the db
	 *
	 * @since 2.3.0
	 *
	 * @param $folders_and_files
	 */
	protected function update_folders_and_files_contents( $folders_and_files ) {
		update_option( $this->get_files_and_folders_name(), $folders_and_files );
	}
}