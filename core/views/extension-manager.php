<?php
// CHECK PERMISSIONS
if( !current_user_can( 'manage_options' ) ) :
	wp_die( __( 'Naughty, naughty. What are you doing sneaking around like that?' ) );
endif;
?>
<?php
	
	$extension = isset($_REQUEST['extension']) ? $_REQUEST['extension'] : '';
	if ( isset($_GET['error']) ) :

		if ( isset($_GET['charsout']) )
			$errmsg = sprintf( __( 'The extension generated %d characters of <strong>unexpected output</strong> during activation. If you notice &#8220;headers already sent&#8221; messages, problems with syndication feeds or other issues, try deactivating or removing this extension.', SOCME ), $_GET['charsout'] );
		else
			$errmsg = __( 'Extension could not be activated because it triggered a <strong>fatal error</strong>.', SOCME );
	?>
	<div id="message" class="updated"><p><?php echo $errmsg; ?></p>
	<?php
		/*if ( !isset($_GET['charsout']) && wp_verify_nonce($_GET['_error_nonce'], 'extension-activation-error_' . $extension ) ) { ?>
	<iframe style="border:0" width="100%" height="70px" src="<?php echo admin_url('plugins.php?action=error_scrape&amp;plugin=' . esc_attr($plugin) . '&amp;_wpnonce=' . esc_attr($_GET['_error_nonce'])); ?>"></iframe>
	<?php
		}/**/
	?>
	</div>
<?php elseif ( isset($_GET['activate']) ) : ?>
	<div id="message" class="updated"><p><?php _e('Extension <strong>activated</strong>.') ?></p></div>
<?php elseif (isset($_GET['activate-multi'])) : ?>
	<div id="message" class="updated"><p><?php _e('Selected extensions <strong>activated</strong>.'); ?></p></div>
<?php elseif ( isset($_GET['deactivate']) ) : ?>
	<div id="message" class="updated"><p><?php _e('Extension <strong>deactivated</strong>.') ?></p></div>
<?php elseif (isset($_GET['deactivate-multi'])) : ?>
	<div id="message" class="updated"><p><?php _e('Selected extensions <strong>deactivated</strong>.'); ?></p></div>
<?php endif; ?>
<div class="wrap">

	<?php screen_icon( 'plugins' ); ?>
	<h2><?php _e( 'Manage Extensions', SOCME ); ?> <a href="http://pixeljar.net" target="_blank" title="<?php _e( 'Browse Extensions', SOCME ); ?>" class="button add-new-h2"><?php _e( 'Browse Extensions', SOCME ); ?></a></h2>
	<form method="post">
		<?php wp_nonce_field( SOCME.'-bulk-manage-extensions' ); ?>

		<!-- DESCRIPTION -->
		<p><?php _e( 'Extensions are a bit like child plugins. They allow you to extend and modify the core functionality of the Social Me plugin by hooking into and filtering the output of the main plugin.', SOCME ) ?></p>

		<div class="tablenav">
			<div class="alignleft actions">
				<select name="action">
					<option value="">Bulk Actions</option>
					<option value="activate-selected">Activate</option>
					<option value="deactivate-selected">Deactivate</option>
					<?php
					/*
					<option value="update-selected">Upgrade</option>
					<option value="delete-selected">Delete</option>
					/**/
					?>
				</select>
				<input type="submit" value="Apply" name="doaction_active" class="button-secondary action" />
			</div>
		</div>
		<table id="all-plugins-table" class="widefat" cellspacing="0">
		<thead>
		<tr>
			<th class="manage-column check-column" scope="col"><input type="checkbox" /></th>
			<th class="manage-column" scope="col"><?php _e( 'Extension', SOCME ); ?></th>
			<th class="manage-column" scope="col"><?php _e( 'Description', SOCME ); ?></th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<th class="manage-column check-column" scope="col"><input type="checkbox" /></th>
			<th class="manage-column" scope="col"><?php _e( 'Extension', SOCME ); ?></th>
			<th class="manage-column" scope="col"><?php _e( 'Description', SOCME ); ?></th>
		</tr>
		</tfoot>
		<tbody class="plugins">
		<?php
			global $socmeExtensionLoader;
			foreach( $socmeExtensionLoader->available_extensions as $ext ) :
				$is_active = socmeExtensionLoader::is_extension_active( $ext['ExtensionPath'] );
		?>
		<tr class="<?php echo ( $is_active ) ? 'active' : 'inactive'; ?>">
			<th class="check-column" scope="row"><input type="checkbox" value="<?php echo $ext['ExtensionPath'] ?>" name="checked[]" /></th>
			<td class="plugin-title"><strong><?php echo $ext['ExtensionName']; ?></strong></td>
			<td class="desc"><?php echo wpautop( $ext['Description'] ); ?></td>
		</tr>
		<tr class="<?php echo ( $is_active ) ? 'active' : 'inactive'; ?> second">
			<td></td>
			<td class="plugin-title">
				<div class="row-actions-visible">
					<?php $activate_url = wp_nonce_url( admin_url( 'admin.php?page='.SOCME.'-ext&action=activate&extension='.$ext['ExtensionPath'] ), SOCME.'-activate-extension_'.$ext['ExtensionPath'] ); ?>
					<?php $deactivate_url = wp_nonce_url( admin_url( 'admin.php?page='.SOCME.'-ext&action=deactivate&extension='.$ext['ExtensionPath'] ), SOCME.'-deactivate-extension_'.$ext['ExtensionPath'] ); ?>
					<span class="<?php echo ( $is_active ) ? 'deactivate' : 'activate'; ?>"><a href="<?php echo ( $is_active ) ? $deactivate_url : $activate_url; ?>" title="<?php _e( sprintf( '%s this extension', ( $is_active ? 'Deactivate' : 'Activate' ) ), SOCME); ?>" class="edit"><?php echo $is_active ? __( 'Deactivate', SOCME ) : __( 'Activate', SOCME ); ?></a></span>
					
					<?php
					/*
					<?php $delete_url = wp_nonce_url( admin_url( 'admin.php?page='.SOCME.'-ext&action=delete&extension='.$ext['ExtensionPath'], SOCME.'-delete-extension_'.$ext['ExtensionPath'] ) ); ?>
					<span class="delete"><a href="<?php echo $delete_url; ?>" title="<?php _e('Delete this extension', SOCME); ?>" class="delete">Delete</a></span>
					<?php
					/**/
					?>
				</div>
			</td>
			<td class="desc">
				<?php _e( ( sprintf( 'Version %s', $ext['Version'] ).' | '.sprintf( 'By %s%s%s', '<a href="'.$ext['AuthorURI'].'">', $ext['AuthorName'], '</a>' ).' | '.sprintf( '%sVisit extension site%s', '<a href="'.$ext['ExtensionURI'].'">', '</a>' ) ), SOCME ); ?>
			</td>
		</tr>
		<?php
		/*
		<tr class="plugin-update-tr">
			<td class="plugin-update" colspan="3">
				<div class="update-message">
					<?php _e( sprintf( 'There is a new version of %s available.', $ext['ExtensionName'] ), SOCME ); ?>
				<?php _e( sprintf( '%sView Details%s.', '<a href="'.$ext['ExtensionURI'].'">', '</a>' ), SOCME ); ?>
				</div>
			</td>
		</tr>
		/**/
		?>
		<?php
			endforeach;
		?>
		</tbody>
		</table>
		<div class="tablenav">
			<div class="alignleft actions">
				<select name="action2">
					<option value="">Bulk Actions</option>
					<option value="activate-selected">Activate</option>
					<option value="deactivate-selected">Deactivate</option>
					<?php
					/*
					<option value="update-selected">Upgrade</option>
					<option value="delete-selected">Delete</option>
					/**/
					?>
				</select>
				<input type="submit" value="Apply" name="doaction_active" class="button-secondary action" />
			</div>
		</div>
	</form>

</div>