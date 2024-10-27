<?php
/*
Plugin Name: Amazon Niche Store
Plugin URI: http://geeklad.com/build-your-own-amazon-niche-store
Description: The Amazon Niche Store no longer works, and has been superseded by the <a target="_blank" href="http://geeklad.com/free-wordpress-store-for-amazon-associates">Free WordPress Store for Amazon Associates</a>.
Author: GeekLad
Version: 1.1.3
Author URI: http://geeklad.com/
License: GPL
*/

/*  Copyright 2009 GeekLad (email : geeklad@geeklad.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include_once ABSPATH . 'wp-admin/includes/admin.php';
include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
class SilentSkin extends WP_Upgrader_Skin {
	function feedback($foo = false, $bar = false) { }
	function error($foo = false, $bar = false) { }
	function before() { }
	function after() { }
}

add_action('admin_notices', 'amazon_niche_store_replacement');
function amazon_niche_store_replacement() {
	if(!is_wp_error(get_option("free_wordpress_store_for_amazon_associates_download_result")))
		echo '<div class="updated"><p>The Amazon Niche Store no longer works, and has been superseded by the Free WordPress Store for Amazon Associates, which has been installed for you.  Go and <a href="options-general.php?page=free-wordpress-store-amazon/free-wordpress-store-amazon.php">configure the plugin</a>.</p></div>';
	else
		echo '<div class="error"><p>The Amazon Niche Store no longer works, and has been superseded by the <a href="http://geeklad.com/free-wordpress-store-for-amazon-associates">Free WordPress Store for Amazon Associates</a>.  Please download and install it instead.</p></div>';
}

function amazon_niche_store_self_destruct($plugins) {
	if(!is_wp_error(get_option("free_wordpress_store_for_amazon_associates_download_result"))) {
		// Remove the plugin from the list of plugins
		unset($plugins['amazon-niche-store/amazon-niche-store.php']);
		
		// Self destruct this plugin
		deactivate_plugins(array("amazon-niche-store/amazon-niche-store.php"));
		delete_plugins(array("amazon-niche-store/amazon-niche-store.php"));
		
		// Activate the new plugin
		activate_plugins('free-wordpress-store-amazon/free-wordpress-store-amazon.php', '', false, true);
		
		// Output the plugins
	}
	return $plugins;
}
add_filter('all_plugins', 'amazon_niche_store_self_destruct');

function install_free_wordpress_store_for_amazon_associates() {
	// Download and install the replacement plugin
	WP_Filesystem();
	$upgrader = new Plugin_Upgrader( new SilentSkin( compact('title', 'url', 'nonce', 'plugin', 'api') ) );
	// Create a temp buffer in case there are errors output
	ob_start();
	$download = $upgrader->download_package("http://geeklad.com/tools/free-wordpress-store-amazon.zip");
	$working_dir = $upgrader->unpack_package($download);
	$result = $upgrader->install_package(array(
		'source' => $working_dir,
		'destination' => WP_PLUGIN_DIR,
		'clear_destination' => false,
		'clear_working' => true,
		'hook_extra' => array()
	));
	update_option("free_wordpress_store_for_amazon_associates_download_result", $result);
	ob_end_clean();
}
register_activation_hook( __FILE__, 'install_free_wordpress_store_for_amazon_associates');