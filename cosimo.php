<?php
/*
Plugin Name: Cosimo
Plugin URI: http://www.andurban.de/wordpress-stuff/plugins/cosimo
Description: Change Of Scene Image Many Often
Author: andurban.de
Version:  0.4
Author URI:  http://www.andurban.de/
----------------------------------------------------------------------------------------
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

// Debug only on localhost
if ($_SERVER['HTTP_HOST'] == 'localhost') error_reporting(E_ALL);  // |E_STRICT

/**
 * hook for wp_head()
 */
function cosimo_head($output) {
  require_once('Cosimo.class.php');

  $cosi = new Cosimo();
	return $cosi->apply($output);
}
add_filter('wp_head', 'cosimo_head');

#################
#  Admin Area   #
#################

if (is_admin()) {

	require_once('CosimoAdmin.class.php');

	/**
	 * Register Faviroll menu in general options menu
	 */
	function cosimo_settings() {
		$ca = new CosimoAdmin();
		$ca->settings();
	}


	/**
	 * Add Settings link to plugin page
	 * @param unknown_type $links
	 */
	function cosimo_addConfigureLink( $links ) {
		$settings_link = '<a href="options-general.php?page='.basename(__FILE__).'">'. __('Settings').'</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	$plugin = plugin_basename(__FILE__);
	add_filter("plugin_action_links_$plugin", 'cosimo_addConfigureLink' );


	/**
	 * register options menu
	 */
	function cosimo_menu() {
		add_submenu_page('options-general.php', __('Cosimo', 'cosimo'), __('Cosimo', 'cosimo'), 'manage_options', basename(__FILE__), 'cosimo_settings');
	}
	add_action('admin_menu', 'cosimo_menu');

}
/* eof */
?>
