<?php

/*
Extension Name: Twitter Extension
Extension URI: http://pixeljar.net/
Description: Allows you to import from and post to Twitter
Version: 0.1
Author: Pixel Jar
Author URI: http://pixeljar.net/
*/
define( 'TWITTER_CONSUMER_KEY', 'YxzShDAMGswyCcby1LKSQ' );
define( 'TWITTER_CONSUMER_SECRET', 'G2hBY89iFY8nz0c7HZ7GUtJnCCDS0kbzt3XfE0K6TE' );
define( 'TWITTER_OAUTH_CALLBACK', admin_url( 'admin.php?page='.SOCME.'-twitter&socme-twitter-oauth=confirmed' ) );
require_once( 'twitter/oauth/twitteroauth.php' );
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
		$credentials = get_option( 'socme-twitter-credentials' );
		// only show custom post type if we're authenticated
		if( !empty( $credentials ) ) :
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
					'capability_type' => 'post',
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
		endif;
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
		
		if ( !empty( $_GET ) && array_key_exists( 'socme-twitter-oauth', $_GET ) && $_GET['socme-twitter-oauth'] == 'auth' ) {

			/* Build TwitterOAuth object with client credentials. */
			$connection = new TwitterOAuth( TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET );
 
			/* Get temporary credentials. */
			$request_token = $connection->getRequestToken( TWITTER_OAUTH_CALLBACK );

			/* Save temporary credentials to session. */
			set_transient( 'socme-twitter-temp-credentials', serialize( $request_token ), 60*60*1 );
			// $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
			// $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
 
			/* If last connection failed don't display authorization link. */
			switch ( $connection->http_code ) {
				case 200:
					/* Build authorize URL and redirect user to Twitter. */
					$url = $connection->getAuthorizeURL( $request_token['oauth_token'] );
					header( 'Location: '.$url );
					break;
				default:
					/* Show notification if something went wrong. */
					echo 'Could not connect to Twitter. Refresh the page or try again later.';
			}
			die();

		} elseif ( !empty( $_GET ) && array_key_exists( 'socme-twitter-oauth', $_GET ) && $_GET['socme-twitter-oauth'] == 'confirmed' ) {
			
			// Pull the token from WordPress temp data
			$token = maybe_unserialize( get_transient( 'socme-twitter-temp-credentials' ) );
			
			/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
			$connection = new TwitterOAuth( TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $token['oauth_token'], $token['oauth_token_secret'] );

			/* Request access tokens from twitter */
			$access_token = $connection->getAccessToken( $_REQUEST['oauth_verifier'] );

			/* Save the access tokens. Normally these would be saved in a database for future use. */
			add_option( 'socme-twitter-credentials',  $access_token );

			/* Remove no longer needed request tokens */
			delete_transient( 'socme-twitter-temp-credentials' );

			/* If HTTP response is 200 continue otherwise send to connect page to retry */
			if ( 200 == $connection->http_code ) {
				/* The user has been verified and the access tokens can be saved for future use */
				header( 'Location: '.admin_url( 'admin.php?page='.SOCME.'-twitter' ) );
			}
		} elseif ( !empty( $_GET ) && array_key_exists( 'socme-twitter-import', $_GET ) && $_GET['socme-twitter-import'] == 'start' ) {
			$token = maybe_unserialize( get_option( 'socme-twitter-credentials', array() ) );
			$connection = new TwitterOAuth( TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $token['oauth_token'], $token['oauth_token_secret'] );
			$maxid = get_option( 'socme-twitter-import-lastid', '' );
			$args = array(
				'count' => 10,
				'trim_user' => 1,
				'include_rts' => 1,
				'include_entities' => 1
			);
			if ( !empty( $maxid ) )
				$args = array_merge( $args, array( 'max_id' => $maxid ) );
				
			// pring_r( $args );
			$statuses = $connection->get('statuses/user_timeline', $args );
			// pring_r( $statuses );
			
			if( is_array( $statuses ) ) :
				foreach( $statuses as $status ) :
					$post = array(
						'comment_status' => 'closed',
						'ping_status' => 'closed',
						'post_content' => $status->text,
						'post_date' => date( 'Y-m-d H:i:s', strtotime( $status->created_at ) ),
						'post_name' => 'tweet-'.$status->id,
						'post_status' => 'publish',
						'post_title' => 'tweet-'.$status->id,
						'post_type' => 'socme-tweet'
					);
			
					$post_id = wp_insert_post( $post );
					add_post_meta($post_id, 'socme-twitter-object', serialize( $status ), true);
					
					$hash_tags = array();
					foreach( $status->entities as $type => $val ) :
				
						if( $type == 'hashtags' ) :
							for( $i = 0; $i < count( $val ); $i++ ) :
								array_push( $hash_tags, '#'.$val[$i]->text );
							endfor;
						endif;
				
					endforeach;
					wp_set_object_terms( $post_id, $hash_tags, 'hash_tag', true );
					
					// pring_r( $post );
				endforeach;
				
				add_option( 'socme-twitter-import-lastid', $statuses[ count( $statuses ) - 1 ]->id ) or
					update_option( 'socme-twitter-import-lastid', $statuses[ count( $statuses ) - 1 ]->id );
			endif;
		}
	}

	function query_vars( $vars ) {
	    $vars[] = 'socme-twitter-oauth';
	    $vars[] = 'socme-twitter-import';
	    return $vars;
	}

	function columns( $columns, $post_type ) {
		if( 'socme-tweet' == $post_type ) :
			$columns = array(
				'cb'			=> '<input type="checkbox" />',
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
		
			// Get the content
			query_posts( array( 'post_type' => 'socme-tweet', 'p' => $post_id ) );
			while ( have_posts() ) {
				the_post();
				$content = get_the_content();
			}
			wp_reset_query();
				
			/* Get user access tokens out of the session. */
			$access_token = maybe_unserialize( get_option( 'socme-twitter-credentials' ) );

			/* Create a TwitterOauth object with consumer/user tokens. */
			$connection = new TwitterOAuth( TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret'] );

			/* Post content to twitter */
			$result = $connection->post( 'statuses/update', array( 'status' => $content ) );

			add_post_meta( $post_id, 'socme-tweet-result', serialize( $result ), true );
		else :
			return $post_id;
		endif;
	}
}

$socmeTwitter = new socmeTwitter;