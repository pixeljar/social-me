<div class="wrap">
	<?php
		$credentials = maybe_unserialize( get_option( 'socme-twitter-credentials', array() ) );
		extract( $credentials );
		if( !empty( $oauth_token ) )
			$connection = new TwitterOAuth( TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $oauth_token, $oauth_token_secret );
		$button_text = ( !empty( $screen_name ) ? __( sprintf( 'Deauthorize account %s', $screen_name ), SOCME ) : __( 'Authorize Twitter Account', SOCME ) );
		$button_action = ( !empty( $screen_name ) ? 'http://twitter.com/settings/connections' : admin_url( 'admin.php?page='.SOCME.'-twitter&socme-twitter-oauth=auth' ) );
	?>
	<?php screen_icon( 'social-me-twitter' ); ?>
	<h2><?php _e( 'Twitter', SOCME ); ?> <a href="<?php echo $button_action; ?>" title="<?php echo $button_text; ?>" class="button add-new-h2 thickbox"><?php echo $button_text; ?></a></h2>

	<?php if ( isset($_GET['success']) ) : ?>
		<div id="message" class="updated"><p><?php _e( sprintf( 'Twitter account <strong>%s</strong> authorized.', $screen_name ), SOCME) ?></p></div>
	<?php endif; ?>
	<form method="post">
		
		<?php if( !empty( $screen_name ) ) :
			$user = $connection->get( 'account/verify_credentials' );
			// pring_r( $user );
		?>
		<div id="twitter-profile" style="display: block; border: 1px solid #ccc; padding: 10px; margin-top: 20px;">
			<div id="avatar" style="float: left; padding: 0 10px 10px 0;"><img src="<?php echo $user->profile_image_url; ?>" /></div>
			<div id="info">
				<?php _e( sprintf( '%sName:%s %s (%s)', '<strong>', '</strong>', $user->name, '<a href="twitter.com/'.$user->screen_name.'">@'.$user->screen_name.'</a>' ), SOCME ); ?><br />
				<?php _e( sprintf( '%sLocation:%s %s', '<strong>', '</strong>', $user->location ), SOCME ); ?><br />
				<?php _e( sprintf( '%sURL:%s %s%s%s', '<strong>', '</strong>', '<a href="'.$user->url.'" target="_blank">', $user->url, '</a>' ), SOCME ); ?><br />
				<?php echo wpautop( $user->description ); ?><br />
				
				<?php _e( sprintf( 'Following: %s', $user->friends_count ), SOCME ); ?> |
				<?php _e( sprintf( 'Followers: %s', $user->followers_count ), SOCME ); ?> |
				<?php _e( sprintf( 'Listed: %s', $user->listed_count ), SOCME ); ?> |
				<?php _e( sprintf( 'Total Tweets: %s', $user->statuses_count ), SOCME ); ?>
			</div>
		</div>
		
		
		<h3><?php _e( 'Import Tweets', SOCME ); ?></h3>
		<p><?php _e( 'Don\'t let Twitter own all of the content that you\'ve created. Import your tweets as WordPress posts and be sure to tweet from the tweet menu in the left-hand navigation so you never lose control of your content again.', SOCME ); ?></p>
		<p class="submit"><input class="button-secondary" type="button" value="<?php _e( 'Import Tweets', SOCME ); ?>" onclick="location.href='<?php echo admin_url( 'admin.php?page='.SOCME.'-twitter&socme-twitter-import=start' ); ?>';" />
		
			
		<h3><?php _e( sprintf( 'Account Details for %s', $screen_name ), SOCME ); ?></h3>
		<p><?php _e( '', SOCME ); ?></p>
	
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
		<?php endif; ?>
	
		<p class="submit"><input class="button-primary" type="submit" value="<?php _e( 'Save Changes', SOCME ); ?>" name="Submit" /></p>
	</form>
</div>