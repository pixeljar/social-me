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
		add_filter( 'manage_posts_columns', array( &$this, 'columns' ), 10, 2 );
		add_action( 'manage_posts_custom_column', array( &$this, 'column_data' ), 10, 2 );
		add_action( 'save_post', array( &$this, 'save_post' ) );
	}

	function init() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		$labels = array(
			'name' => _x('Tweets', 'post type general name'),
			'singular_name' => _x('Tweet', 'post type singular name'),
			'add_new' => _x('Post Tweet', 'socme-tweet'),
			'add_new_item' => __('Post Tweet'),
			'edit_item' => __('Edit Tweet'),
			'new_item' => __('Post Tweet'),
			'view_item' => __('View Tweet'),
			'search_items' => __('Search Tweets'),
			'not_found' =>  __('No tweets found'),
			'not_found_in_trash' => __('No tweets found in Trash'), 
			'parent_item_colon' => ''
		);
		register_post_type( 'socme-tweet',
			array(
				'label' => 'Tweets',
				'labels' => $labels,
				'description' => 'Tweets are how birds communicate with each other. Twitter is a service that is for the birds. These are your tweets.',
				'public' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => true,
				'show_ui' => true,
				'capability_type' => 'socme_tweet',
				'capabilities' => array(
					'edit_socme_tweet' => 'no_way_jose',
					'edit_posts' => 'no_way_jose',
					'edit_others_posts' => 'no_way_jose',
					'publish_posts' => 'publish_posts',
					'read_post' => 'read_posts',
					'read_private_posts' => 'read_private_posts',
					'delete_posts' => 'delete_posts'
				),
				'hierarchical' => false,
				'supports' => array(
					'editor'
				),
				'taxonomies' => array(
					'hash_tags'
				),
				'menu_icon' => SOCME_URL.'icons/twitter-16.png',
				'rewrite' => array(
					'slug' => 'tweeted',
					'with_front' => false
				),
				'query_var' => true,
				'can_export' => true,
				'show_in_nav_menus' => false
			)
		);
		
		$labels = array(
			'name' => _x( 'Hash Tags', 'taxonomy general name' ),
			'singular_name' => _x( 'Hash Tag', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Hash Tags' ),
			'popular_items' => __( 'Frequently Used Hash Tags' ),
			'all_items' => __( 'All Hash Tags' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit Hash Tag' ), 
			'update_item' => __( 'Update Hash Tag' ),
			'add_new_item' => __( 'Add Hash Tag' ),
			'new_item_name' => __( 'New Hash Tag' ),
			'separate_items_with_commas' => __( 'Separate hash tags with commas' ),
			'add_or_remove_items' => __( 'Add or remove hash tags' ),
			'choose_from_most_used' => __( 'Choose from the most used hash tags' )
		);
		register_taxonomy(
			'hash_tag',
			'socme-tweet',
			array(
				'hierarchical' => false,
				'labels' => $labels,
				'show_ui' => true,
				'query_var' => true,
				'rewrite' => array( 'slug' => 'hash-tag' )
			)
		);
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
				echo '<strong>Get Token Exception</strong>: '.$e->getMessage();
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
				echo '<strong>Return Values Exception</strong>: '.$e->getMessage();
			}
		}
	}

	function query_vars( $vars ) {
	    $vars[] = 'socme-twitter-oauth';
	    return $vars;
	}

	function columns( $columns, $post_type ) {
		if( 'socme-tweet' == $post_type ) :
			$columns = array(
				'cb'			=> '<input type="checkbox" />',
				'title'			=> 'Title',
				'tweet'			=> 'Tweet',
				'hash_tags'		=> 'Hash Tags',
				'date'			=> 'Date'
			);	
		endif;
		return $columns;
	}
	
	function column_data( $column_name, $post_id ) {
		global $post_type;
		if( 'socme-tweet' == $post_type ) :
			$term_args = array();
		
			if( 'hash_tags' == $column_name ) :
				$terms = wp_get_object_terms( $post_id, 'hash_tag', $term_args );
				if( $terms ) {
					$my_func = create_function( '$term',
												'return "<a href=\"edit.php?post_type=socme-twitter&hash_tag=".$term->slug."\">".$term->name."</a>";');
					$text = array_map( $my_func, $terms, array( $column_name ) );
					echo implode(', ', $text);
				} else {
					echo '<i>'.__( 'No hash tags yet', SOCME ).'</i>';
				}
			elseif( 'tweet' == $column_name ) :
				the_content( $post_id );
			endif;
		endif;
	}
	
	function save_post( $post_id ) {

		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;

		if( isset( $_POST[ 'post_type' ] ) && 'socme-tweet' == $_POST[ 'post_type' ] ) :
			if( !current_user_can( 'edit_post', $post_id ) ) :
				return $post_id;
			endif;
		else :
			return $post_id;
		endif;
		
		$credentials = maybe_unserialize( get_option( 'socme-twitter-credentials', array() ) );
		if( isset( $credentials['oauth_token'] ) ) :
		
			$options = array( 'consumer_key' => TWITTER_CONSUMER_KEY, 'consumer_secret' => TWITTER_CONSUMER_SECRET );
			OAuthStore::instance( '2Leg', $options );
			try {
				query_posts( array( 'post_type' => 'socme-tweet', 'p' => $post_id ) );
				while ( have_posts() ) {
					the_post();
					$content = get_the_content();
				}
				wp_reset_query();
				
				/* GRRRRRRRRRRR
				 * using GET here because POST pulls in all the WordPress crap.
				 * Problem originates from OAuthRequest::getRequestBody()
				/**/
				$request = new OAuthRequester( TWITTER_UPDATE_STATUS_API, 'POST', array( 'oauth_token' => $credentials['oauth_token'] ), 'status='.urlencode( $content ) );
				pring_r( $request );
				$result = $request->doRequest( $credentials['user_id'] );
				pring_r( $result );
				add_post_meta( $post_id, 'socme-tweet-result', $result, true );
				die( 'dead' );
			} catch( OAuthException2 $e ) {
				echo '<strong>Tweet Post Exception</strong>: '.$e->getMessage();
			}
		endif;
	}
}

$socmeTwitter = new socmeTwitter;