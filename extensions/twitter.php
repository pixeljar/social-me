<?php

/*
Extension Name: Twitter Extension
Extension URI: http://pixeljar.net/
Description: Allows you to import from and post to Twitter
Version: 0.1
Author: Pixel Jar
Author URI: http://pixeljar.net/
*/
include_once SOCME_CORE."oauth/OAuthStore.php";
include_once SOCME_CORE."oauth/OAuthRequester.php";
define("TWITTER_CONSUMER_KEY", "YxzShDAMGswyCcby1LKSQ");
define("TWITTER_CONSUMER_SECRET", "G2hBY89iFY8nz0c7HZ7GUtJnCCDS0kbzt3XfE0K6TE");

define("TWITTER_OAUTH_HOST","http://api.twitter.com");
define("TWITTER_REQUEST_TOKEN_URL", TWITTER_OAUTH_HOST . "/oauth/request_token");
define("TWITTER_AUTHORIZE_URL", TWITTER_OAUTH_HOST . "/oauth/authorize");
define("TWITTER_ACCESS_TOKEN_URL", TWITTER_OAUTH_HOST . "/oauth/access_token");
define("TWITTER_PUBLIC_TIMELINE_API", TWITTER_OAUTH_HOST . "/statuses/public_timeline.json");
define("TWITTER_UPDATE_STATUS_API", TWITTER_OAUTH_HOST . "/statuses/update.json");

define('OAUTH_TMP_DIR', function_exists('sys_get_temp_dir') ? sys_get_temp_dir() : realpath($_ENV["TMP"])); 

class socmeTwitter {

	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'admin_init', array( &$this, 'parse_request' ) );
		add_filter( 'query_vars', array( &$this, 'query_vars' ) );
	}

	function init() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}
	
	function admin_menu() {
		global $socmeMenus;
		$socme_twitter_menu = add_submenu_page( SOCME, 'Twitter', 'Twitter', 'manage_options', SOCME.'-twitter', array( &$this, 'twitter_menu' ) );
		
		add_action( 'admin_print_scripts-'.$socme_twitter_menu, array( &$socmeMenus, 'add_js' ), 1 );
		add_action( 'admin_print_styles-'. $socme_twitter_menu, array( &$socmeMenus, 'add_css' ), 1 );
	}
	
	function twitter_menu() {
		include_once( 'twitter/views/twitter-menu.php' );
	}
	
	function parse_request() {
		
		$options = array( 'consumer_key' => TWITTER_CONSUMER_KEY, 'consumer_secret' => TWITTER_CONSUMER_SECRET );
		
		if ( !empty( $_GET ) && array_key_exists( 'socme-twitter-oauth', $_GET ) && $_GET['socme-twitter-oauth'] == 'auth' ) {

			OAuthStore::instance( '2Leg', $options );
			try {
				$request = new OAuthRequester( TWITTER_REQUEST_TOKEN_URL, 'POST', array( 'oauth_callback' => admin_url( 'admin.php?page='.SOCME.'&socme-twitter-oauth=confirmed' ) ) );
				$result = $request->doRequest( 0 );
				$params = explode( '&', $result['body'] );
				header( 'Location: '.TWITTER_AUTHORIZE_URL.'?'.$params[0].'&oauth_callback='.admin_url( 'admin.php?page='.SOCME.'&socme-twitter-oauth=confirmed' ) );
			} catch( OAuthException2 $e ) {
				echo '<strong>Exception</strong>: '.$e->getMessage();
			}
			die();
		
	    } elseif ( !empty( $_GET ) && array_key_exists( 'socme-twitter-oauth', $_GET ) && $_GET['socme-twitter-oauth'] == 'confirmed' ) {

			OAuthStore::instance( '2Leg', $options );
			try {
				$request = new OAuthRequester( TWITTER_ACCESS_TOKEN_URL, 'POST', array( 'oauth_verifier' => $_GET['oauth_verifier'], 'oauth_token' => $_GET['oauth_token'] ) );
				$result = $request->doRequest( 0 );
				$params = explode( '&', $result['body'] );
				$to_storage = array();
				foreach ( $params as $pair ) :
					$split = explode( '=', $pair );
					$to_storage[ $split[0] ] = $split[1];
				endforeach;
				add_option( 'socme-twitter-credentials', serialize( $to_storage ) ) or
					update_option( 'socme-twitter-credentials', serialize( $to_storage ) );
				$GLOBALS['twitter_success'] = true;
			
				header( 'Location: '.admin_url( 'admin.php?page='.SOCME.'-twitter&success' ) );
			} catch( OAuthException2 $e ) {
				echo '<strong>Exception</strong>: '.$e->getMessage();
			}
		}
	}

	function query_vars( $vars ) {
	    $vars[] = 'socme-twitter-oauth';
	    return $vars;
	}
}

$socmeTwitter = new socmeTwitter;