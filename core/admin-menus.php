<?php

class socmeMenus {
	
	function __construct() {
		add_action( 'init', array( &$this, 'init' ), 10 );
	}
	
	function init() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		
		wp_register_style( 'social-me', SOCME_CSS . 'social-me.css');
		add_action( 'admin_print_scripts-admin.php?page=social-me', array( &$this, 'add_js' ), 1 );
	}
		
	function add_js() {
		wp_enqueue_script( 'jquery' );
	}
	
	function add_css() {
		wp_enqueue_style( 'social-me', SOCME_CSS.'social-me.css', array(), '.1', 'screen' );
	}
	
	function admin_menu() {
		$socme_main_menu = add_menu_page( 'Social Me', 'Social Me', 'manage_options', SOCME, array( &$this, 'main_menu' ), SOCME_URL.'icons/social-me-16.png' );
		$socme_ext_menu = add_submenu_page( SOCME, 'Extensions', 'Extensions', 'manage_options', SOCME.'-ext', array( &$this, 'extension_menu' ) );
		
		add_action( 'admin_print_styles-'. $socme_main_menu, array( &$this, 'add_css' ), 1 );
		add_action( 'admin_print_styles-'. $socme_ext_menu, array( &$this, 'add_css' ), 1 );
		
	}
	
	function main_menu() {
?>
<div class="wrap">

<?php screen_icon( 'social-me' ); ?>
<h2><?php _e( 'Social Me', SOCME ); ?></h2>

</div>
<?php
	}
	
	function extension_menu() {
?>
<div class="wrap">

<?php screen_icon( 'social-me-ext' ); ?>
<h2><?php _e( 'Manage Extensions', SOCME ); ?></h2>

</div>
<?php
	}
}

$socmeMenus = new socmeMenus;