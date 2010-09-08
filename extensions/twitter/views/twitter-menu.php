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
	<form method="post">
		<h3>Section Name</h3>
		<p>Description of this section</p>
	
		<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row"><label for="setting_name">Setting Name</label></th>
			<td><input type="text" name="setting_name" id="setting_name" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="setting_name_2">Setting Name 2</label></th>
			<td>
				<textarea name="setting_name_2" id="setting_name_2" rows="5" cols="40"></textarea>
				<p>
					<input type="checkbox" name="setting_name_3" id="setting_name_3" value="1" />
					<label for="setting_name_3">Do you want to check this box?</label>
				</p>
			</td>
		</tr>
		</tbody>
		</table>
	
	
		<p class="submit"><input class="button-primary" type="submit" value="<?php _e( 'Save Changes', SOCME ); ?>" name="Submit" /></p>
	</form>
</div>