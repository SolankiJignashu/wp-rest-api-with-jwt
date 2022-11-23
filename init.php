<?php
/**
 * Plugin Name:  WP REST API with JWT Token
 * Description:  Allows user to activate and deactivate using REST API.
 * Version:     1.0
 * Author:      Jignashu Solanki
 *
 * @package WHJ_Teachers_Progress_Certificate
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
require plugin_dir_path( __FILE__ ) . 'constants.php';
require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

require plugin_dir_path( __FILE__ ) . 'class-jwt-rest-api-user.php';
$JWT_Rest_Api_User = new JWT_Rest_Api_User();
$JWT_Rest_Api_User->run();

require plugin_dir_path( __FILE__ ) . 'class-wp-user-operations.php';
$WP_User_Operations = new WP_User_Operations();
$WP_User_Operations->run();
