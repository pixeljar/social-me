<?php

/*
Plugin Name: Social Me
Plugin URI: http://pixeljar.net/
Description: Allows you to import from and post to various social networks.
Version: 0.1
Author: Pixel Jar
Author URI: http://pixeljar.net/
Text Domain: social-me
*/

/**
 * Copyright (c) 2010 Your Name. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

// SET UP PATH CONSTANTS
if ( ! defined( 'SOCME' ) ) define( 'SOCME', 'social-me' );
if ( ! defined( 'SOCME_URL' ) ) define( 'SOCME_URL', plugin_dir_url( __FILE__ ) );
if ( ! defined( 'SOCME_ABS' ) ) define( 'SOCME_ABS', plugin_dir_path( __FILE__ ) );
if ( ! defined( 'SOCME_REL' ) ) define( 'SOCME_REL', basename( dirname( __FILE__ ) ) );
if ( ! defined( 'SOCME_CORE' ) ) define( 'SOCME_CORE', SOCME_ABS.'core/' );
if ( ! defined( 'SOCME_EXT' ) ) define( 'SOCME_EXT', SOCME_ABS.'extensions/' );
if ( ! defined( 'SOCME_LANG' ) ) define( 'SOCME_LANG', SOCME_ABS.'i18n/' );
if ( ! defined( 'SOCME_CSS' ) ) define( 'SOCME_CSS', SOCME_URL.'css/' );
if ( ! defined( 'SOCME_JS' ) ) define( 'SOCME_JS', SOCME_URL.'js/' );

// INTERNATIONALIZATION
load_plugin_textdomain( SOCME, null, SOCME_REL );

// INCLUDE NECESSARY FILES
require_once( SOCME_CORE.'admin-menus.php' );
require_once( SOCME_CORE.'extension-loader.php' );


/* DEBUG HELPERS
************************************/
function pring_r( $arr ) {
	echo _pring_r( $arr );
}
	function _pring_r( $arr ) {
		return '<pre>'.print_r( $arr, true ).'</pre>';
	}