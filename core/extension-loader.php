<?php

class socmeExtensionLoader {

	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	function init() {
	}
	
	function load_extensions() {
	
	}

}

$socmeExtensionLoader = new socmeExtensionLoader;