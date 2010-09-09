<?php

/*
Extension Name: LinkedIn Extension
Extension URI: http://pixeljar.net/
Description: Allows you to import from and post to LinkedIn.
Version: 0.1
Author: Pixel Jar
Author URI: http://pixeljar.net/
*/

class socmeLinkedIn {

	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	function init() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}
	
	function admin_menu() {
		global $socmeMenus;
		$socme_linkedin_menu = add_submenu_page( SOCME, 'LinkedIn', 'LinkedIn', 'manage_options', SOCME.'-linkedin', array( &$this, 'linkedin_menu' ) );
		
		add_action( 'admin_print_scripts-'.$socme_linkedin_menu, array( &$socmeMenus, 'add_js' ), 1 );
		add_action( 'admin_print_styles-'. $socme_linkedin_menu, array( &$socmeMenus, 'add_css' ), 1 );
	}
	
	function linkedin_menu() {
?>
<div class="wrap">

<?php screen_icon( 'social-me-linkedin' ); ?>
<h2><?php _e( 'LinkedIn', SOCME ); ?></h2>

</div>
<?php
	}
	
}

$socmeLinkedIn = new socmeLinkedIn;


/*
Key:
341bca6983663dbb3b7bbf051282a5b5

Secret:
cb5e1c8f7f56129c 
/**/