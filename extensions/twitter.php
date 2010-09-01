<?php

class socmeTwitter {

	function __construct() {
		add_action( 'init', array( &$this, 'init' ) );
	}

	function init() {
	}
	
}

$socmeTwitter = new socmeTwitter;