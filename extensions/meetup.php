<?php

/*
Extension Name: Meetup Extension
Extension URI: http://pixeljar.net/
Description: Allows you to import from and post to Meetup.com.
Version: 0.1
Author: Pixel Jar
Author URI: http://pixeljar.net/
*/

class socmeMeetup {

	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	function init() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}
	
	function admin_menu() {
		global $socmeMenus;
		$socme_meetup_menu = add_submenu_page( SOCME, 'Meetup', 'Meetup', 'manage_options', SOCME.'-meetup', array( &$this, 'meetup_menu' ) );
		
		add_action( 'admin_print_scripts-'.$socme_meetup_menu, array( &$socmeMenus, 'add_js' ), 1 );
		add_action( 'admin_print_styles-'. $socme_meetup_menu, array( &$socmeMenus, 'add_css' ), 1 );
	}
	
	function meetup_menu() {
?>
<div class="wrap">

<?php screen_icon( 'social-me-meetup' ); ?>
<h2><?php _e( 'Meetup', SOCME ); ?></h2>

</div>
<?php
	}
	
}

$socmeMeetup = new socmeMeetup;


/*
Key:
341bca6983663dbb3b7bbf051282a5b5

Secret:
cb5e1c8f7f56129c 
/**/