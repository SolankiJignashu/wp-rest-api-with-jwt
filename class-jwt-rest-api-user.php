<?php

use \Firebase\JWT\JWT;

// ini_set( 'memory_limit', '-1' );
/**
 * Class JWT_Rest_Api_User
 *
 * @package JWT_Rest_Api_User
 *
 */

/**
 * ALl the common function related to REST API
 */
class JWT_Rest_Api_User {

	public $version     = '1.0';
	private $email      = '';
	private $user_id    = '';
	private $route      = '';
	private $start_time = '';
	/**
	 * Initilizing hooks
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'rest_api_init', array( $this, 'wp_rest_api_user_init' ) );
	}
	public function wp_rest_api_user_init() {
		# code...
		# code...
		register_rest_route(
			'/wp/v1',
			'/user_deactivate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'wp_rest_api_user_deactivate_callback' ),
				'permission_callback' => array( $this, 'wp_rest_api_user_check_callback' ),
			)
		);
		register_rest_route(
			'/wp/v1',
			'/user_activate',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'wp_rest_api_user_activate_callback' ),
				'permission_callback' => array( $this, 'wp_rest_api_user_check_callback' ),
			)
		);

		add_filter( 'wp-rest-api-log-entries-pre-insert', array( $this, 'wp_reset_api_email_args_addition' ), 10, 1 );
	}
	
	public function wp_rest_api_user_check_callback( $data ) {
		global $wpdb;
		$this->start_time = microtime( true );
		// This is your client secret
		// $key           = wp_SECRET_KEY;
		// $token_payload = array( 'email' => 'jignashu.solanki@whitehatjr.com' );

		// // This is your id token
		// $jwt = JWT::encode( $token_payload, base64_decode( strtr( $key, '-_', '+/' ) ), 'HS256' );

		// print "JWT:\n";
		// print_r( $jwt );
		// echo '<pre>';
		// // print_r( $data );
		// echo '</pre>';
		// exit;
		$this->route = $data->get_route();
		$payload     = $data->get_header( 'payload' );
		if ( empty( $payload ) ) {
			$this->wp_rest_api_error( 'Invalid Payload' );
			return new WP_Error( 'rest_invalid_data', esc_html__( 'Invalid data.', 'learndash' ), array( 'status' => 200 ) );
		}
		// $jwt = $data['payload'];
		//
		try {
			$decoded = JWT::decode( $payload, strtr( WP_SECRET_KEY, '-_', '+/' ), array( 'HS256' ) );
			// echo '<pre>';
			// print_r( $decoded );
			// echo '</pre>';
			// exit;
			if ( ! isset( $decoded->email ) ) {
				$this->wp_rest_api_error( 'Invalid Email Address' );
				return new WP_Error( 'rest_invalid_data', esc_html__( 'Invalid data.', 'learndash' ), array( 'status' => 200 ) );
			}
			$email_data = $decoded->email;
		} catch ( Exception $e ) {
			$this->wp_rest_api_error( $e->getMessage() );
			return new WP_Error(
				'rest_invalid_data',
				esc_html__( $e->getMessage(), 'learndash' ),
				array( 'status' => 200 )
			);
		}

		// echo '<pre>';
		// print_r( $decoded );
		// echo '</pre>';
		# code...
		// if ( ! isset( $data['email'] ) ) {
		//  return new WP_Error( 'rest_invalid_data', esc_html__( 'Invalid data.', 'learndash' ), array( 'status' => 200 ) );
		// }
		$this->email = sanitize_email( $email_data );

		if ( empty( $this->email ) ) {
			$this->wp_rest_api_error( 'Invalid Email Address' );
			return new WP_Error( 'rest_invalid_data', esc_html__( 'Invalid data.', 'learndash' ), array( 'status' => 200 ) );
		}
		$sql = "SELECT ID FROM ".$wpdb->prefix.'users WHERE user_email LIKE "'.$this->email.'"';
		$user_id = $wpdb->get_var($sql);
		// $user = get_user_by( 'email', $this->email );
		if ( empty( $user_id ) ) {
			$this->wp_rest_api_error( 'User Does not exists (' . $this->email . ')' );
			return new WP_Error( 'rest_user_not_exists', esc_html__( 'User does not exists.', 'learndash' ), array( 'status' => 200 ) );
		}
		$this->user_id = $user_id;
		return true;
	}
	public function wp_rest_api_user_deactivate_callback( $data ) {
		# code...
		$user_id                    = $this->user_id;
		$WP_User_Operations = new WP_User_Operations();
		$WP_User_Operations->wp_user_deactivate( $user_id );
		$this->wp_rest_api_error( 'User Deactivated. email ->' . $this->email );
		$result   = array( 'success' => 'Operation Performed Successfully!!' );
		$response = new WP_REST_Response( $result, 200 ); // data => array of returned data

		return $response;
	}
	public function wp_rest_api_user_activate_callback( $data ) {
		# code...
		$user_id                    = $this->user_id;
		$WP_User_Operations = new WP_User_Operations();
		$WP_User_Operations->wp_user_activate( $user_id );
		$this->wp_rest_api_error( 'User Activated. email ->' . $this->email );
		$result   = array( 'success' => 'Operation Performed Successfully!!' );
		$response = new WP_REST_Response( $result, 200 ); // data => array of returned data

		return $response;
	}
	public function wp_rest_api_error( $message = '' ) {
		# code...
		if ( $this->user_id != '' ) {
			$message .= ', user_id -> ' . $this->user_id;
		}
		$end_time       = microtime( true );
		$execution_time = $end_time - $this->start_time;
		wp_error_log( $this->route . ' Time Taken ' . $execution_time . ' seconds ' . $message );
	}
}
