<?php

if(!defined('WPINC')) // MUST have WordPress.
	exit('Do NOT access this file directly: '.basename(__FILE__));

  require_once( 'titan-framework-checker.php' );
  require_once( 'titan-framework-options.php' );

require_once( plugin_dir_path( __FILE__ ) . '/inc/class.nhpa_directory.php' );

  add_action( 'plugins_loaded', function () {
  	NHPA_Directory::get_instance();
  } );

 ?>