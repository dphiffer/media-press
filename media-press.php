<?php
/*
Plugin Name: Media Press
Plugin URI: http://phiffer.org/wordpress/media-press/
Description: Manage image sizes, responsive-friendly captions & galleries
Version: 0.1
Author: Dan Phiffer
Author URI: http://phiffer.org/
*/

if ( ! function_exists( 'dbug' ) ) {
	
	function dbug() {
	  if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
			$args = func_get_args();
			foreach ( $args as $arg ) {
				if ( is_scalar( $arg ) ) {
					error_log( $arg );
				} else {
					$arg = print_r( $arg, true );
					error_log( $arg );
				}
			}
		}
	}
	
}

if ( ! function_exists( 'mp_init' ) ) {
	
	class MP_Base {
		
		function add_action( $hook, $method = null, $priority = 10, $args = 0 ) {
			$this->add_filter( $hook, $method, $priority, $args );
		}
		
		function add_filter( $hook, $method = null, $priority = 10, $args = 1 ) {
			if ( empty( $method ) ) {
				$method = $hook;
			}
			add_action( $hook, array( $this, $method ), $priority, $args );
		}
		
	}
	
	function mp_init() {
	  require_once __DIR__ . '/image-sizes/image-sizes.php';
	  require_once __DIR__ . '/image-editor/image-editor.php';
	}
	mp_init();
	
}
