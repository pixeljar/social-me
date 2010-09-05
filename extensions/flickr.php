<?php

/*
Extension Name: Flickr Extension
Extension URI: http://pixeljar.net/
Description: Allows you to import from and post to Flickr.
Version: 0.1
Author: Pixel Jar
Author URI: http://pixeljar.net/
*/

class socmeFlickr {

	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	function init() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}
	
	function admin_menu() {
		global $socmeMenus;
		$socme_flickr_menu = add_submenu_page( SOCME, 'Flickr Config', 'Flickr Configuration', 'manage_options', SOCME.'-flickr', array( &$this, 'flickr_menu' ) );
		
		add_action( 'admin_print_scripts-'.$socme_flickr_menu, array( &$socmeMenus, 'add_js' ), 1 );
		add_action( 'admin_print_styles-'. $socme_flickr_menu, array( &$socmeMenus, 'add_css' ), 1 );
	}
	
	function flickr_menu() {
?>
<div class="wrap">

<?php screen_icon( 'social-me-flickr' ); ?>
<h2><?php _e( 'Flickr Configuration', SOCME ); ?></h2>

</div>
<?php
	}
	
}

$socmeFlickr = new socmeFlickr;