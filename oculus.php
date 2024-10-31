<?php
/**
 * @package Oculus
 */
/*
Plugin Name: Oculus
Plugin URI: http://yundun.aliyun.com/
Description: 
Version: 0.1
Author: potatoooooo
License: GPLv2 or later
Text Domain: oculus 
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'OCULUS_VERSION', '0.1' );
define( 'OCULUS__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'OCULUS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'Oculus', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Oculus', 'plugin_deactivation' ) );

require_once( OCULUS__PLUGIN_DIR . 'class.oculus.php' );

add_action( 'init', array( 'Oculus', 'init' ) );

if ( is_admin() ) {
	require_once( OCULUS__PLUGIN_DIR . 'class.oculus-admin.php' );
	add_action( 'init', array( 'Oculus_Admin', 'init' ) );
}

