<?php

/*
Extension Name: Twitter Extension
Extension URI: http://pixeljar.net/
Description: Allows you to import from and post to Twitter
Version: 0.1
Author: Pixel Jar
Author URI: http://pixeljar.net/
*/

class socmeTwitter {

	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	function init() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}
	
	function admin_menu() {
		$socme_twitter_menu = add_submenu_page( SOCME, 'Twitter Config', 'Twitter Configuration', 'manage_options', SOCME.'-twitter', array( &$this, 'twitter_menu' ) );
	}
	
	function twitter_menu() {
?>
<div class="wrap">

<?php screen_icon( 'social-me-twitter' ); ?>
<h2><?php _e( 'Twitter Configuration', SOCME ); ?></h2>

</div>
<?php
	}
	
}

$socmeTwitter = new socmeTwitter;