<?php

// ini_set( 'memory_limit', '-1' );
/**
 * Class WP_User_Operations
 *
 * @package WP_User_Operations
 *
 */

/**
 * ALl the common function related to teachers progress.
 */

class WP_User_Operations {

	public function run() {
		# code...
		add_action( 'show_user_profile', array( $this, 'whj_disable_show_user_profile' ) );
		add_action( 'edit_user_profile', array( $this, 'whj_disable_show_user_profile' ) );
		add_action( 'personal_options_update', array( $this, 'whj_disable_save_user_profile' ), 1 );
		add_action( 'edit_user_profile_update', array( $this, 'whj_disable_save_user_profile' ), 1 );
		add_filter( 'authenticate', array( $this, 'whj_restrict_user_access' ), 100, 1 );
		add_action( 'simple_jwt_login_login_hook', array( $this, 'whj_restrict_user_access' ), 10, 1 );
	}
	public function whj_restrict_user_access( $user ) {
		if ( empty( $user ) ) {
			return $user;
		}
		if ( ! isset( $user->ID ) ) {
			return $user;
		}
		# code...
		// if ( ! is_user_logged_in() ) {
		//  return;
		// }
		// echo '<pre>';
		// print_r( $user );
		// echo '</pre>';
		// exit;
		$user_id = $user->ID;
		$status  = $this->whj_get_user_status( $user_id );
		if ( $status == 'deactive' ) {
			$sessions = WP_Session_Tokens::get_instance( $user_id );
			$sessions->destroy_all();
			wp_die( __( USER_DEACTIVATE_MESSAGE, 'whj' ) );
		}

		// exit;
		return $user;
	}
	public function whj_disable_save_user_profile( $user_id ) {
		# code...
		if ( ! current_user_can( 'edit_users' ) ) {
			return;
		}

		if ( empty( $user_id ) ) {
			return;
		}
		if ( ( isset( $_POST['whj_deactivate_user'] ) ) && ( ! empty( $_POST['whj_deactivate_user'] ) ) && ( intval( $_POST['whj_deactivate_user'] ) === intval( $user_id ) ) ) {
			$this->whj_user_deactivate( $user_id );
		} else {
			$this->whj_user_activate( $user_id );
		}
	}
	public function whj_disable_show_user_profile( WP_User $user ) {
		# code...
		if ( ! current_user_can( 'edit_users' ) ) {
			return '';
		}
		$user_id   = $user->ID;
		$j_disable = get_user_meta( $user_id, 'whj_deactivate_user', true );
		$checked   = '';
		if ( ! empty( $j_disable ) ) {
			$checked = "checked='checked'";
		}
		?>
	<table class="form-table">
		<tr>
			<th><?php echo __( 'Deactivate User Login', 'whj_wp' ); ?></th>
			<td><input type="checkbox" name="whj_deactivate_user" value="<?php echo $user_id; ?>" <?php echo $checked; ?>></td>
		</tr>
	</table>

		<?php
	}
	public function whj_user_deactivate( $user_id ) {
		# code...
		// $user_id = $this->user_id;
		update_user_meta( $user_id, 'whj_deactivate_user', current_time( 'Y-m-d H:i:s' ) );
		$user = new WP_User( $user_id );
		$user->set_role( 'deactivated' );
		$sessions = WP_Session_Tokens::get_instance( $user_id );
		$sessions->destroy_all();
		return true;
	}
	public function whj_user_activate( $user_id ) {
		# code...
		// $user_id = $this->user_id;
		delete_user_meta( $user_id, 'whj_deactivate_user' );
		$user = new WP_User( $user_id );
		$user->set_role( 'subscriber' );

		return true;
	}
	public function whj_get_user_status( $user_id ) {
		# code...
		$status = get_user_meta( $user_id, 'whj_deactivate_user', true );
		if ( ! empty( $status ) ) {
			return 'deactive';
		}
		return 'active';
	}
}
