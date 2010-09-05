<?php

class socmeMenus {
	
	function __construct() {
		add_action( 'init', array( &$this, 'init' ), 10 );
	}
	
	function init() {
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		
		wp_register_style( 'social-me', SOCME_CSS . 'social-me.css');
		add_action( 'admin_print_scripts-admin.php?page=social-me', array( &$this, 'add_js' ), 1 );
	}
		
	function add_js() {
		wp_enqueue_script( 'jquery' );
	}
	
	function add_css() {
		wp_enqueue_style( 'social-me', SOCME_CSS.'social-me.css', array(), '.1', 'screen' );
	}
	
	function admin_menu() {
		$socme_main_menu = add_menu_page( 'Social Me', 'Social Me', 'manage_options', SOCME, array( &$this, 'main_menu' ), SOCME_URL.'icons/social-me-16.png' );
		$socme_ext_menu = add_submenu_page( SOCME, 'Extensions', 'Extensions', 'manage_options', SOCME.'-ext', array( &$this, 'extension_menu' ) );
		
		add_action( 'admin_print_styles-'. $socme_main_menu, array( &$this, 'add_css' ), 1 );
		add_action( 'admin_print_styles-'. $socme_ext_menu, array( &$this, 'add_css' ), 1 );
		
	}
	
	function main_menu() {
?>
<div class="wrap">

<?php screen_icon( 'social-me' ); ?>
<h2><?php _e( 'Social Me', SOCME ); ?></h2>

</div>
<?php
	}
	
	function extension_menu() {
?>
<div class="wrap">

<?php screen_icon( 'social-me-ext' ); ?>
<h2><?php _e( 'Manage Extensions', SOCME ); ?></h2>
<form method="post">
<table id="all-plugins-table" class="widefat" cellspacing="0">
<thead>
<tr>
	<th class="manage-column check-column" scope="col"><input type="checkbox" /></th>
	<th class="manage-column" scope="col">Extension</th>
	<th class="manage-column" scope="col">Description</th>
</tr>
</thead>
<tfoot>
<tr>
	<th class="manage-column check-column" scope="col"><input type="checkbox" /></th>
	<th class="manage-column" scope="col">Extension</th>
	<th class="manage-column" scope="col">Description</th>
</tr>
</tfoot>
<tbody class="plugins">
<?php
	global $socmeExtensionLoader;
	foreach( $socmeExtensionLoader->available_extensions as $ext ) :
	// pring_r( $ext );
	$plugin_path = '';
?>
<tr class="inactive">
	<th class="check-column" scope="row"><input type="checkbox" value="<?php echo $plugin_path ?>" name="checked[]" /></th>
	<td class="plugin-title"><strong><?php echo $ext['ExtensionName']; ?></strong></td>
	<td class="desc"><?php echo wpautop( $ext['Description'] ); ?></td>
</tr>
<tr class="inactive second">
	<td></td>
	<td class="plugin-title">
		<div class="row-actions-visible">
			<?php $activate_url = wp_nonce_url( admin_url( 'admin.php?page='.SOCME.'-ext&action=activate&ext='.$plugin_path, 'activate-socme-extenstion' ) ); ?>
			<?php $deactivate_url = wp_nonce_url( admin_url( 'admin.php?page='.SOCME.'-ext&action=deactivate&ext='.$plugin_path, 'deactivate-socme-extenstion' ) ); ?>
			<span class="activate"><a href="<?php echo $activate_url; ?>" title="<?php _e('Activate this extension', SOCME); ?>" class="edit">Activate</a></span>
		</div>
	</td>
	<td class="desc">
		<?php _e( ( sprintf( 'Version %s', $ext['Version'] ).' | '.sprintf( 'By %s%s%s', '<a href="'.$ext['AuthorURI'].'">', $ext['AuthorName'], '</a>' ).' | '.sprintf( '%sVisit extension site%s', '<a href="'.$ext['ExtensionURI'].'">', '</a>' ) ), SOCME ); ?>
	</td>
</tr>
<tr class="plugin-update-tr">
	<td class="plugin-update" colspan="3">
		<div class="update-message">
			<?php _e( sprintf( 'There is a new version of %s available.', $ext['ExtensionName'] ), SOCME ); ?>
		<?php _e( sprintf( '%sView Details%s.', '<a href="'.$ext['ExtensionURI'].'">', '</a>' ), SOCME ); ?>
		</div>
	</td>
</tr>
<?php
	endforeach;
?>
</tbody>
</table>
</form>
</div>
<?php
	}
}

$socmeMenus = new socmeMenus;