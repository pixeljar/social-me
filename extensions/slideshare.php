<?php

/*
Extension Name: Facebook Extension
Extension URI: http://pixeljar.net/
Description: Allows you to import from and post to Facebook.
Version: 0.1
Author: Pixel Jar
Author URI: http://pixeljar.net/
*/

class socmeFacebook {

	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	function init() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}
	
	function admin_menu() {
		global $socmeMenus;
		$socme_flickr_menu = add_submenu_page( SOCME, 'Facebook', 'Facebook', 'manage_options', SOCME.'-facebook', array( &$this, 'facebook_menu' ) );
		
		add_action( 'admin_print_scripts-'.$socme_flickr_menu, array( &$socmeMenus, 'add_js' ), 1 );
		add_action( 'admin_print_styles-'. $socme_flickr_menu, array( &$socmeMenus, 'add_css' ), 1 );
	}
	
	function facebook_menu() {
?>
<div class="wrap">

<?php screen_icon( 'social-me-facebook' ); ?>
<h2><?php _e( 'Facebook', SOCME ); ?></h2>

</div>
<?php
	}
	
}

$socmeFacebook = new socmeFacebook;


/*
Key:
341bca6983663dbb3b7bbf051282a5b5

Secret:
cb5e1c8f7f56129c 
/**/