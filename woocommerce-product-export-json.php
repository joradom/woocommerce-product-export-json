<?php
/**
* Plugin Name: Woocommerce - Product Export JSON
* Plugin URI: https://woocommerce.com
* Description: Export the products in a JSON format for WooCommerce.
* Version: 1.0.0
* Author: Virender Singh
* Author URI: https://woocommerce.com
* License: GPL2
*/
define( 'WOOPROGEN_PLUGIN_VERSION', '1.0.0' );
define( 'WOOPROGEN_PLUGIN_DOMAIN', 'woocommerce-product-export-json' );
define( 'WOOPROGEN_PLUGIN_URL', WP_PLUGIN_URL . '/woocommerce-product-export-json' );

/**
 * Add the Product Export menu item.
 */
add_action('admin_menu', 'wpej_admin_menu');
function wpej_admin_menu () {
  add_submenu_page('woocommerce', 'Product JSON Export', 'Product JSON Export', 'manage_woocommerce', 'wpej-product-export', 'wpej_product_export');
}


function wpej_product_export () {
	
	if($_GET['m'] == 1){
		echo '<div class="updated notice notice-success is-dismissible" id="message">
		<p>JSON file generated. <a href="'.site_url("/wp-content/plugins/".WOOPROGEN_PLUGIN_DOMAIN."/download.php").'">Download Now</a></p>
		<button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>';
	}

	echo '<br /><div class="product-generator-admin" style="margin-right: 1em; background: rgb(255, 255, 255) none repeat scroll 0% 0%; padding: 20px 20px;">';
	echo '<h1>';
	echo __( 'Product Export JSON', WOOPROGEN_PLUGIN_DOMAIN );
	echo '</h1><br />';
	echo '<div>';
	echo __( 'This produces for export the product in <strong>JSON</strong> format purposes.', WOOPROGEN_PLUGIN_DOMAIN );
	echo ' ';
	echo __( 'You can use this plugin on any production site.', WOOPROGEN_PLUGIN_DOMAIN );
	echo ' ';
	echo __( 'The plugin will <strong>NOT</strong> clean up the data it has created.', WOOPROGEN_PLUGIN_DOMAIN );
	echo ' ';
	echo '</div>';

	echo '<div class="settings">';
	echo '<form name="settings" method="post" action="'.site_url("/wp-content/plugins/".WOOPROGEN_PLUGIN_DOMAIN."/download.php").'">';
	echo '<div>';

	echo '<div class="buttons"><br />';	
	echo sprintf( '<input class="button button-primary" type="submit" name="submit" value="%s" />', __( 'Generate New JSON', WOOPROGEN_PLUGIN_DOMAIN ) );
	echo ' &nbsp; <a class="button button-primary" href="'.site_url("/wp-content/plugins/".WOOPROGEN_PLUGIN_DOMAIN."/download.php").'" >Download JSON </a>  ';
	echo '<input type="hidden" name="action" value="save" />';
	echo '</div>';
	
	echo '</div>';
	echo '</form>';
	echo '</div>';
	
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wpej_action_links');

function wpej_action_links ( $links ) {
	$setting = array( '<a href="' . admin_url( 'admin.php?page=wpej-product-export' ) . '">Settings</a>');
	return array_merge( $links, $setting );
}
?>