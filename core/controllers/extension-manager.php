<?php
// CHECK PERMISSIONS
if( !current_user_can( 'manage_options' ) ) :
	wp_die( __( 'Naughty, naughty. What are you doing sneaking around like that?' ) );
endif;

if ( !empty( $_REQUEST['action'] ) )
	$action = $_REQUEST['action'];
elseif ( !empty( $_REQUEST['action2'] ) )
	$action = $_REQUEST['action2'];
else
	$action = false;

// PROCESS ACTIONS
if( !empty( $action ) && !empty( $_REQUEST['page'] ) && SOCME.'-ext' == $_REQUEST['page'] ) :
	
	$extension = isset( $_REQUEST['extension'] ) ? $_REQUEST['extension'] : '';
	
	switch( $action ) :
	
		case 'activate' :
			if ( check_admin_referer( SOCME.'-activate-extension_'.$extension ) ) :
			
				$result = socmeExtensionLoader::activate_extension( $extension, admin_url( 'admin.php?page='.SOCME.'-ext&error=true&extension='.$extension ) );
				if ( is_wp_error( $result ) ) :
					if ( 'unexpected_output' == $result->get_error_code() ) :
						$redirect = admin_url( 'admin.php?page='.SOCME.'-ext&error=true&charsout='.strlen( $result->get_error_data() ).'&extension='.$extension );
						wp_redirect( add_query_arg( '_error_nonce', wp_create_nonce( 'extension-activation-error_'.$extension ), $redirect ) );
						exit;
					else :
						wp_die( $result );
					endif;
				endif;
				wp_redirect( admin_url( 'admin.php?page='.SOCME.'-ext&activate=true&plugin_status=all&paged=1' ) ); // overrides the ?error=true one above
				exit;
				
			endif;
			break;
			
		case 'deactivate' :
			if ( check_admin_referer( SOCME.'-deactivate-extension_'.$extension ) ) :
				socmeExtensionLoader::deactivate_extensions( $extension );
				$redirect = admin_url( 'admin.php?page='.SOCME.'-ext&deactivate=true&plugin_status=all&paged=1' );
				if ( headers_sent() ) :
					echo "<meta http-equiv='refresh' content='" . esc_attr( "0;url=".$redirect ) . "' />";
				else :
					wp_redirect( $redirect );
				endif;
				exit;
				break;
			endif;
			break;
			
		case 'activate-selected' :
			if ( check_admin_referer( SOCME.'-bulk-manage-extensions' ) ) :

			$extensions = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			$extensions = array_filter( $extensions, create_function( '$extension', 'return !socmeExtensionLoader::is_extension_active( $extension );') ); // Only activate plugins which are not already active.
			if ( empty( $extensions ) ) :
				wp_redirect( admin_url( 'admin.php?page='.SOCME.'-ext&plugin_status=all&paged=1' ) );
				exit;
			endif;

			socmeExtensionLoader::activate_extensions( $extensions, admin_url( 'admin.php?page='.SOCME.'-ext&error=true' ) );
			wp_redirect( admin_url( 'admin.php?page='.SOCME.'-ext&activate-multi=true&plugin_status=all&paged=1' ) );
			exit;
			
			endif;
			break;
			
		case 'deactivate-selected' :
			if ( check_admin_referer( SOCME.'-bulk-manage-extensions' ) ) :

			$extensions = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
			$extensions = array_filter( $extensions, create_function( '$extension', 'return socmeExtensionLoader::is_extension_active( $extension );') ); //Do not deactivate plugins which are already deactivated.
			if ( empty( $extensions ) ) {
				wp_redirect( admin_url( 'admin.php?page='.SOCME.'-ext&plugin_status=all&paged=1' ) );
				exit;
			}

			socmeExtensionLoader::deactivate_extensions( $extensions );
			wp_redirect( admin_url( 'admin.php?page='.SOCME.'-ext&deactivate-multi=true&plugin_status=all&paged=1' ) );
			exit;
			
			endif;
			break;
			
/*			
		case 'delete' :
			if ( check_admin_referer( SOCME.'-delete-extension_'.$extension ) ) :
				// process request
			endif;
			break;
		case 'update-selected' :
		case 'delete-selected' :
			if ( check_admin_referer( SOCME.'-bulk-manage-extensions' ) ) :
				// process request
			endif;
			break;
/**/
			
	endswitch;
	
	$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'error', 'deleted', 'activate', 'activate-multi', 'deactivate', 'deactivate-multi', '_error_nonce' ), $_SERVER['REQUEST_URI'] );
endif;