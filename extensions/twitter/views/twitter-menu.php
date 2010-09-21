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
		<p><?php _e( 'Don\'t let Twitter own the content that you\'ve worked so hard to create. Import your tweets as WordPress posts and be sure to tweet from the tweet menu in the left-hand navigation so you never lose control of your content again.', SOCME ); ?></p>
		<p class="submit"><input class="button-secondary" type="button" value="<?php _e( 'Import Tweets', SOCME ); ?>" onclick="location.href='<?php echo admin_url( 'admin.php?page='.SOCME.'-twitter&socme-twitter-import=start' ); ?>';" />
		
			
		<h3><?php _e( 'Twitter Button', SOCME ); ?></h3>
		<p><?php _e( 'Twitter provides a twitter button to make it easier for your visitors to share your content. We can automatically include the Twitter button in each of your blog posts and pages.', SOCME ); ?></p>
	
		<table class="form-table">
		<tbody>
		<tr valign="top">
			<th scope="row"><label for="socme_twitter_button_active">Use Twitter Button?</label></th>
			<td>
				<input type="checkbox" name="socme_twitter_button_active" id="socme_twitter_button_active" value="1" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Twitter Button Style</th>
			<td>
				<div style="min-height: 100px; width: 115px; float: left; margin-right: 10px; background: url('<?php echo SOCME_URL.'extensions/twitter/images/tweetv.png' ?>') no-repeat 20px 30px;">
					<input type="radio" name="socme_twitter_button_style[]" id="socme_twitter_button_vertical" value="vertical" /> <label for="socme_twitter_button_vertical">Vertical count</label>
				</div>
				<div style="min-height: 100px; width: 130px; float: left; margin-right: 10px; background: url('<?php echo SOCME_URL.'extensions/twitter/images/tweeth.png' ?>') no-repeat 20px 50px;">
					<input type="radio" name="socme_twitter_button_style[]" id="socme_twitter_button_horizontal" value="horizontal" /> <label for="socme_twitter_button_horizontal">Horizontal count</label>
				</div>
				<div style="min-height: 100px; width: 130px; float: left; margin-right: 10px; background: url('<?php echo SOCME_URL.'extensions/twitter/images/tweetn.png' ?>') no-repeat 20px 50px;">
					<input type="radio" name="socme_twitter_button_style[]" id="socme_twitter_button_nocount" value="no-count"/> <label for="socme_twitter_button_nocount">No count</label>
				</div>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Twitter Button Position</th>
			<td>
				<input type="radio" name="socme_twitter_button_position[]" id="socme_twitter_button_tl" value="1" /> <label for="socme_twitter_button_tl">Top left</label>
				<input type="radio" name="socme_twitter_button_position[]" id="socme_twitter_button_tr" value="2" /> <label for="socme_twitter_button_tr">Top right</label>
				<input type="radio" name="socme_twitter_button_position[]" id="socme_twitter_button_bl" value="3"/> <label for="socme_twitter_button_b">Below</label>
			</td>
		</tr>
		</tbody>
		</table>
		<?php endif; ?>
	
		<p class="submit"><input class="button-primary" type="submit" value="<?php _e( 'Save Changes', SOCME ); ?>" name="Submit" /></p>
	</form>
</div>