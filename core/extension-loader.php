<?php

class socmeExtensionLoader {
	
	var $available_extensions = array();
	var $enabled_extensions = array();
	var $disabled_extensions = array();

	function __construct() {
		add_action( 'init', array( &$this, 'init' ), 1 );
	}

	function init() {
		$d = dir( SOCME_EXT );
		while ( false !== ( $entry = $d->read() ) ) :
			if( $entry[0] != '.' && substr( $entry, -3 ) == 'php' ) :
			
				// use `apply_filters( "extra_".SOCME."_headers", array( 'Other Header' ) );` to add new headers to an extension
				$accepted_headers = array(
					'ExtensionName' => 'Extension Name',
					'ExtensionURI' => 'Extension URI',
					'Description' => 'Description',
					'Version' => 'Version',
					'AuthorName' =>'Author',
					'AuthorURI' =>'Author URI'
				);
				array_push( $this->available_extensions, get_file_data( $d->path.$entry, $accepted_headers, SOCME ) );
				
			endif;
		endwhile;
		$d->close();
	}
	
	function load_extensions() {
		// require_once( $d->path.$entry );
	}
	
	function is_extension_active( $ext ) {
		return in_array( $ext, (array) get_option( 'active_'.SOCME.'_extensions', array() ) );
	}
	
	function activate_extension( $ext ) {
		$active_extensions = get_option( 'active_'.SOCME.'_extensions', array() );	
		// set_option( 'active_'.SOCME.'_extensions', array() ) );
	}

}
$socmeExtensionLoader = new socmeExtensionLoader;