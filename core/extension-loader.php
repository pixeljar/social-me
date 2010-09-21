<?php

class socmeExtensionLoader {
	
	var $available_extensions = array();
	var $enabled_extensions = array();
	var $disabled_extensions = array();

	function __construct() {
		add_action( 'init', array( &$this, 'init' ), 1 );
	}

	function init() {
		$directories = array( SOCME_EXT, WP_PLUGIN_DIR );
		$directories = apply_filters( 'socme_extensions_search_directories', $directories);
		$d = dir( SOCME_EXT );
		while ( false !== ( $entry = $d->read() ) ) :
			if( $entry[0] != '.' && substr( $entry, -3 ) == 'php' ) :

				$accepted_headers = array(
					'ExtensionName' => 'Extension Name',
					'ExtensionURI' => 'Extension URI',
					'Description' => 'Description',
					'Version' => 'Version',
					'AuthorName' =>'Author',
					'AuthorURI' =>'Author URI'
				);
				array_push( $this->available_extensions, array_merge( (array) get_file_data( $d->path.$entry, $accepted_headers, SOCME ), array( 'ExtensionPath' => $entry ) ) );
				
			endif;
		endwhile;
		$d->close();
		$this->load_extensions();
	}
	
	function load_extensions() {
		$extensions = get_option( 'socme_active_extensions', array() );
		if( !empty( $extensions ) ) :
			foreach( $extensions as $extension ) :
				require_once( SOCME_EXT.$extension );
			endforeach;
		endif;
	}
	
	function is_extension_active( $ext ) {
		return in_array( $ext, (array) get_option( 'socme_active_extensions', array() ) );
	}
	
	function activate_extension( $extension, $redirect = '' ) {
		$extension  = plugin_basename( trim( $extension ) );
		$current = get_option( 'socme_active_extensions', array() );

		if ( !in_array( $extension, $current ) ) {
			if ( !empty($redirect) )
				wp_redirect( add_query_arg( '_error_nonce', wp_create_nonce( 'extension-activation-error_' . $extension ), $redirect ) ); // we'll override this later if the plugin can be included without fatal error
			ob_start();
			include( SOCME_EXT . '/' . $extension );
			do_action( 'socme_activate_extension', trim( $extension ) );
			$current[] = $extension;
			sort($current);
			update_option( 'socme_active_extensions', $current );
			do_action( 'socme_activate_' . trim( $extension ) );
			do_action( 'socme_activated_extension', trim( $extension ) );
			if ( ob_get_length() > 0 ) {
				$output = ob_get_clean();
				return new WP_Error( 'unexpected_output', __( 'The extension generated unexpected output.' ), $output );
			}
			ob_end_clean();
		}

		return null;
	}
	
	function deactivate_extensions( $extensions, $silent = false ) {
		$current = get_option( 'socme_active_extensions', array() );
		$do_blog = false;
		
		foreach ( (array) $extensions as $extension ) {
			$extension = plugin_basename( $extension );
			if ( ! socmeExtensionLoader::is_extension_active( $extension ) )
				continue;
			if ( ! $silent )
				do_action( 'socme_deactivate_extension', trim( $extension ) );

			// Deactivate for this blog only
			$key = array_search( $extension, (array) $current );
			if ( false !== $key ) {
				$do_blog = true;
				array_splice( $current, $key, 1 );
			}

			//Used by Plugin updater to internally deactivate plugin, however, not to notify plugins of the fact to prevent plugin output.
			if ( ! $silent ) {
				do_action( 'socme_deactivate_' . trim( $extension ) );
				do_action( 'socme_deactivated_extension', trim( $extension ) );
			}
		}

		if ( $do_blog )
			update_option( 'socme_active_extensions', $current );
	}
	
	function activate_extensions( $extensions, $redirect = '' ) {
		
		if ( !is_array( $extensions ) )
			$extensions = array( $extensions );

		$errors = array();
		foreach ( (array) $extensions as $extension ) :

			if ( !empty($redirect) )
				$redirect = add_query_arg( 'extension', $extension, $redirect );
			
			$result = socmeExtensionLoader::activate_extension( $extension, $redirect );
			
			if ( is_wp_error( $result ) )
				$errors[$extension] = $result;

		endforeach;

		if ( !empty( $errors ) )
			return new WP_Error( 'extensions_invalid', __( 'One of the extensions is invalid.' ), $errors );

		return true;
	}

}
$socmeExtensionLoader = new socmeExtensionLoader;