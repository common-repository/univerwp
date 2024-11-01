<?php

namespace UNIVERWP;

/*
 * Plugin Name:       Univerwp
 * Plugin URI:        https://7bar.github.io/
 * Description:       Univerwp is a plugin, as a framework, that aims to extend WordPress in various areas: security, performance and additional features. It is modular and has a central logging utility.
 * Version:           0.1.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            7bar
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       univerwp
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')){
	exit;
}

define('UNIVERWP', 'univerwp');
define('UNIVERWP__FILE__', __FILE__);
define('UNIVERWP_PATH', plugin_dir_path(__FILE__));
define('UNIVERWP_BASENAME', plugin_basename(__FILE__));

/**
 * UniverWP Main Loader
 * 
 */
if (!class_exists('UniverWP')){
	include_once plugin_dir_path(UNIVERWP__FILE__) . "includes/admin/univerwp.class.php";
	if (!isset($uwp)) {
		$uwp = new UniverWP();
	}	
}

register_activation_hook(UNIVERWP__FILE__, array('UNIVERWP\UniverWP', 'activate'));
register_deactivation_hook(UNIVERWP__FILE__, array('UNIVERWP\UniverWP', 'deactivate'));
register_uninstall_hook(UNIVERWP__FILE__, array('UNIVERWP\UniverWP', 'uninstall'));

/**
 * UniverWP Remove Bloated Configuration (RBC) module
 * 
 */
if (!class_exists('UniverWP_RBC')){
	include_once plugin_dir_path(UNIVERWP__FILE__) . "includes/admin/modules/univerwp_rbc.class.php";

	if (!isset($uwp_rbc)) {
		$uwp_rbc = new UniverWP_RBC();
	}
}