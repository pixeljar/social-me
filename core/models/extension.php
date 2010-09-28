<?php

class socmeExtension {
	
	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}
	
	function init() {
		
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		
		/* Hook for extensions to
		 * create post types to store
		 * social network content
		 ***************************************************/
		$post_types = array();
		$post_types = apply_filters( 'socme_create_post_types', $post_types );
		foreach( $post_types as $post_type ) :
			register_post_type( $post_type->slug, $post_type->args );
		endforeach;
		
		/* Hook for extensions to
		 * create taxonomies for
		 * social network content
		 ***************************************************/
		$taxonomies = array();
		$taxonomies = apply_filters( 'socme_create_taxonomies', $taxonomies );
		foreach( $taxonomies as $taxonomy ) :
			register_taxonomy( $taxonomy->slug, $taxonomy->post_type, $taxonomy->args );
		endforeach;
		
	}
	
	function admin_menu() {
		
		global $socmeMenus;
		
		/* Hook for extensions to
		 * create sub menus
		 ***************************************************/
		$menus = array();
		$menus = apply_filters( 'socme_create_menus', $menus );
		foreach( $menus as $menu ) :

			$submenu = add_submenu_page( SOCME, $menu->page_title, $menu->menu_title, 'manage_options', SOCME.'-'.$menu->slug, array( &$menu->instance, 'admin_menu' ) );
		
			add_action( 'admin_print_scripts-'.$submenu, array( &$socmeMenus, 'add_js' ), 1 );
			add_action( 'admin_print_styles-'. $submenu, array( &$socmeMenus, 'add_css' ), 1 );
		endforeach;
		
		/* Hook for extensions to
		 * create meta boxes
		 ***************************************************/
		$meta_boxes = array();
		$meta_boxes = apply_filters( 'socme_create_meta_boxes', $meta_boxes );
		foreach( $meta_boxes as $meta_box ) :
			add_meta_box( $meta_box->slug, $meta_box->title, array( &$meta_box->instance, $meta_box->slug.'_meta_box' ), $meta_box->post_type, $meta_box->context, $meta_box->priority, $meta_box->args );
		endforeach;
		
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
		
		do_action( 'socme_save_post' );
		
	}
}