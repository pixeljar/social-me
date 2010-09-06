<div class="wrap">
	<?php
		$credentials = maybe_unserialize( get_option( 'socme-twitter-credentials', array() ) );
		extract( $credentials );
		$button_text = ( !empty( $screen_name ) ? __( sprintf( 'Deauthorize account %s', $screen_name ), SOCME ) : __( 'Authorize Twitter Account', SOCME ) );
		$button_action = ( !empty( $screen_name ) ? 'http://twitter.com/settings/connections' : admin_url( 'admin.php?page='.SOCME.'-twitter&socme-twitter-oauth=auth' ) );
	?>
	<?php screen_icon( 'social-me-twitter' ); ?>
	<h2><?php _e( 'Twitter', SOCME ); ?> <a href="<?php echo $button_action; ?>" title="<?php echo $button_text; ?>" class="button add-new-h2 thickbox"><?php echo $button_text; ?></a></h2>

	<?php if ( isset($_GET['success']) ) : ?>
		<div id="message" class="updated"><p><?php _e( sprintf( 'Twitter account <strong>%s</strong> authorized.', $screen_name ), SOCME) ?></p></div>
	<?php endif; ?>
</div>