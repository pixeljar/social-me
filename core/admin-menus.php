<?php

class socmeMenus {
	
	function __construct() {
		add_action( 'init', array( &$this, 'init' ), 10 );
	}
	
	function init() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		add_action( 'admin_init', array( &$this, 'extension_processing' ) );
		wp_register_style( SOCME, SOCME_CSS.'social-me.css', array(), '.1', 'screen' );
	}
		
	function add_js() {
		wp_enqueue_script( 'jquery' );
	}
	
	function add_css() {
		wp_enqueue_style( SOCME );
	}
	
	function admin_menu() {
		$socme_main_menu = add_menu_page( 'Social Me', 'Social Me', 'manage_options', SOCME, array( &$this, 'main_menu' ), SOCME_URL.'icons/'.SOCME.'-16.png' );
		add_action( 'admin_print_scripts-'.$socme_main_menu, array( &$this, 'add_js' ), 1 );
		add_action( 'admin_print_styles-'. $socme_main_menu, array( &$this, 'add_css' ), 1 );
		
		$socme_conf_menu = add_submenu_page( SOCME, 'Configure', 'Configure', 'manage_options', SOCME, array( &$this, 'main_menu' ) );
		add_action( 'admin_print_scripts-'.$socme_conf_menu, array( &$this, 'add_js' ), 1 );
		add_action( 'admin_print_styles-'. $socme_conf_menu, array( &$this, 'add_css' ), 1 );
		
		$socme_ext_menu = add_submenu_page( SOCME, 'Extensions', 'Extensions', 'manage_options', SOCME.'-ext', array( &$this, 'extension_menu' ) );
		add_action( 'admin_print_scripts-'.$socme_ext_menu, array( &$this, 'add_js' ), 1 );
		add_action( 'admin_print_styles-'. $socme_ext_menu, array( &$this, 'add_css' ), 1 );
	}
	
	function main_menu() {
		include_once( 'views/main-menu.php' );
	}
	
	function extension_menu() {
		include_once( 'views/extension-manager.php' );
	}
	
	function extension_processing() {
		include_once( 'controllers/extension-manager.php' );
	}
}

$socmeMenus = new socmeMenus;