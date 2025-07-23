<?php
/**
 * WCR User Management system.
 */

/**
 * WCR_User_Manager class.
 */
class WCR_User_Manager {

	public $table_name        = 'wcr_tokens';
	public $cookie_name       = 'wcr_token_key';
	private $cookie_expiry    = 30 * DAY_IN_SECONDS; // 30 days
	private $api_base         = 'https://xyz.com/api/';
	private $bearer_token_url = 'https://getme.global/api/v1/auth/sign_in';
	private $jwt_token_url    = 'https://getme.global/api/v1/wordpress_auth/jwt_token';
	private $validate_token_x = 'https://wcr.is/api/v1/auth/validate_token';
	private $validate_token   = 'https://getme.global/api/v1/auth/validate_token';
	private $verify_jwt_token = 'https://getme.global/api/v1/wordpress_auth/verify_jwt_token';

	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'after_setup_theme', array( $this, 'maybe_create_table' ) );
		add_action( 'wcr_cleanup_expired_tokens', array( $this, 'clean_expired_tokens' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'wcr_login_form', array( $this, 'login_form_shortcode' ) );

		add_action( 'wp_ajax_wcr_login', array( $this, 'handle_login' ) );
		add_action( 'wp_ajax_nopriv_wcr_login', array( $this, 'handle_login' ) );

		add_action( 'wp_ajax_wcr_logout', array( $this, 'handle_logout' ) );
		add_action( 'wp_ajax_nopriv_wcr_logout', array(  $this, 'handle_logout' ) );

		add_action( 'wp_ajax_wcr_get_user_data', array( $this, 'get_user_data_callback' ) );
		add_action( 'wp_ajax_nopriv_wcr_get_user_data', array( $this, 'get_user_data_callback' ) );
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {
		// Check if user has token_key cookie and load user data.
		if ( isset( $_COOKIE[ $this->cookie_name ] ) ) {
			$this->load_user_data_from_cookie();
		}

		/**
		 * Schedule cleanup of expired tokens
		 */
		if ( ! wp_next_scheduled( 'wcr_cleanup_expired_tokens' ) ) {
			wp_schedule_event( time(), 'daily', 'wcr_cleanup_expired_tokens' );
		}
	}

	/**
	 * Create database table for storing tokens
	 */
	public function maybe_create_table() {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->table_name;

		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name ) {
			return;
		}

		$charset_collate = $wpdb->get_charset_collate();

		/*
		$sql = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			token_key varchar(64) NOT NULL UNIQUE,
			wcr_token text NOT NULL,
			user_email varchar(100) NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			expires_at datetime NOT NULL,
			last_accessed datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY token_key (token_key),
			KEY user_email (user_email),
			KEY expires_at (expires_at)
		) $charset_collate;";
		*/

		$sql = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			token_key varchar(64) NOT NULL UNIQUE,
			wcr_token text NOT NULL,
			user_data longtext NOT NULL,
			user_email varchar(100) NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			expires_at datetime NOT NULL,
			last_accessed datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY user_email (user_email),
			KEY expires_at (expires_at)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql);
	}

	public function clean_expired_tokens() {
		$this->cleanup_expired_tokens();
	}

	/**
	 * Clean up expired tokens (run this via cron)
	 */
	public function cleanup_expired_tokens() {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->table_name;

		$wpdb->query("DELETE FROM $table_name WHERE expires_at < NOW()");
	}

	/**
	 * Enqueue the frontend JavaScript
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'wcr_front', get_stylesheet_directory_uri() . '/assets/js/wcr-front.js', array( 'jquery' ), '1.0', true);

		wp_localize_script(
			'wcr_front',
			'wcr_front_obj',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'wcr_nonce' )
			)
		);

		wp_enqueue_style( 'wcr_front', get_stylesheet_directory_uri() . '/assets/css/wcr-front.css', array(), '1.0.0' );
	}

	/**
	 * Login form shortcode
	 */
	public function login_form_shortcode() {
		ob_start();
		if ( $this->is_user_logged_in() ) {
			$this->display_user_data();
		} else {
			?>
			<div class="wcr-authform-container">
				<div class="wcr-authform-wrapper">
					<div class="wcr-authform-img-wrapper">
						<div class="wcr-authform-logo-wrapper">
							<span class="wcr-authform-logo-inner">
								<svg class="wcr-authform-logo" viewBox="0 0 256 256" fill="none" xmlns="http://www.w3.org/2000/svg"><path _ngcontent-ng-c2071322268="" d="M201.02 104.028C201.02 82.349 183.286 64.7749 161.409 64.7749C148.807 64.7749 137.597 70.6195 130.346 79.7081L130.131 79.8026C123.413 73.1731 115.815 69.2876 108.869 66.9976C108.901 66.979 108.928 66.9518 108.96 66.9331C104.928 65.5525 100.615 64.7734 96.1058 64.7734C74.2269 64.7749 56.4902 82.3476 56.4902 104.028C56.4902 108.026 57.1045 111.882 58.2246 115.518C58.2318 115.508 58.2434 115.498 58.2549 115.488C61.397 126.659 67.8531 133.671 69.8693 135.396L78.4298 143.505L78.94 143.995L78.4081 144.535C75.8355 147.142 75.8919 151.322 78.5238 153.866C81.1542 156.411 85.3687 156.36 87.9399 153.755L88.4934 153.211C88.5555 153.158 88.6322 153.124 88.7189 153.124C88.9212 153.124 89.0816 153.281 89.0816 153.485C89.0816 153.568 89.0484 153.638 88.9993 153.701L89.0065 153.707L88.4775 154.245C85.9034 156.852 85.9598 161.032 88.5917 163.573C91.2236 166.122 95.438 166.073 98.0092 163.461L98.5295 162.954C98.5946 162.877 98.6914 162.825 98.8012 162.825C99.005 162.825 99.1655 162.983 99.1655 163.185C99.1655 163.272 99.1308 163.349 99.0759 163.411L99.0773 163.414L99.0686 163.422C99.0614 163.43 99.0556 163.441 99.0469 163.447L98.5454 163.954C95.9728 166.559 96.0277 170.739 98.6596 173.283C101.293 175.829 105.507 175.781 108.077 173.174L108.621 172.62L108.631 172.631C108.693 172.584 108.768 172.55 108.85 172.55C109.053 172.55 109.215 172.709 109.215 172.911C109.215 172.991 109.183 173.06 109.137 173.12L109.141 173.124L109.112 173.154C109.108 173.16 109.103 173.163 109.099 173.17L108.61 173.667C106.038 176.273 106.093 180.452 108.725 182.997C111.357 185.544 115.574 185.494 118.142 182.884L118.769 182.248L118.592 182.476L127.446 190.828C129.83 193.074 132.948 194.311 136.229 194.311H136.398C139.866 194.264 143.094 192.866 145.484 190.377C147.803 187.958 149.044 184.664 148.934 181.267C149.073 181.273 149.219 181.279 149.394 181.273C152.855 181.233 156.075 179.837 158.466 177.347C160.775 174.94 161.998 171.798 161.938 168.439C162.091 168.444 162.286 168.449 162.458 168.449C165.922 168.404 169.15 167.007 171.54 164.518C173.863 162.096 175.104 158.805 174.993 155.407C175.132 155.412 175.322 155.42 175.45 155.415C178.914 155.371 182.137 153.973 184.525 151.484C188.915 146.921 189.147 139.921 185.369 135.058C194.591 127.868 201.02 116.542 201.02 104.028ZM179.365 146.619C178.342 147.684 176.852 148.295 175.272 148.295C173.787 148.295 172.412 147.769 171.403 146.817L162.06 138.172C161.468 137.739 160.739 137.476 159.944 137.476C157.963 137.476 156.354 139.073 156.354 141.039C156.354 142.173 156.902 143.177 157.743 143.829L166.178 151.755C168.427 153.879 168.518 157.419 166.379 159.647C165.356 160.717 163.864 161.329 162.284 161.329C160.803 161.329 159.427 160.804 158.417 159.849L149.4 151.268L149.395 151.276C148.754 150.696 147.907 150.33 146.97 150.33C144.989 150.33 143.378 151.926 143.378 153.896C143.378 154.844 143.76 155.702 144.373 156.343L144.363 156.352L153.12 164.603C155.346 166.726 155.428 170.264 153.305 172.482C152.282 173.545 150.791 174.157 149.21 174.157C147.724 174.157 146.351 173.631 145.344 172.68L136.443 164.355L136.437 164.362C135.788 163.706 134.883 163.298 133.883 163.298C131.899 163.298 130.291 164.893 130.291 166.863C130.291 167.902 130.748 168.823 131.463 169.476L140.116 177.621C142.364 179.745 142.455 183.288 140.319 185.517C139.297 186.582 137.804 187.195 136.224 187.195C134.739 187.195 133.367 186.672 132.357 185.716L123.543 177.405L124.071 176.872C126.643 174.263 126.587 170.081 123.954 167.536C121.323 164.989 117.11 165.041 114.535 167.646L114.233 167.951L114.224 167.94C114.158 168.014 114.064 168.061 113.952 168.061C113.753 168.061 113.594 167.902 113.594 167.705C113.594 167.59 113.647 167.493 113.73 167.427L114 167.158C116.573 164.548 116.516 160.368 113.884 157.824C111.254 155.273 107.038 155.328 104.464 157.937L104.149 158.257L104.142 158.251C104.079 158.297 104.004 158.334 103.92 158.334C103.721 158.334 103.559 158.174 103.559 157.975C103.559 157.893 103.59 157.823 103.637 157.763L103.631 157.757L103.662 157.727C103.666 157.721 103.669 157.715 103.673 157.712L103.935 157.446C106.506 154.835 106.453 150.658 103.818 148.11C101.186 145.565 96.9744 145.616 94.3974 148.221L94.0549 148.548C93.9899 148.635 93.8901 148.696 93.7716 148.696C93.5722 148.696 93.4103 148.532 93.4103 148.336C93.4103 148.223 93.4667 148.126 93.549 148.062L93.867 147.737C96.4353 145.128 96.3833 140.949 93.7485 138.401C91.1181 135.855 86.905 135.909 84.3295 138.516L83.9855 138.862L74.7169 129.963C74.6258 129.884 67.8199 123.956 64.6619 111.078C64.6865 111.07 64.7096 111.057 64.7342 111.048C64.2182 108.785 63.9205 106.441 63.9205 104.026C63.9205 86.445 78.3532 72.1434 96.0985 72.1434C98.7391 72.1434 101.296 72.4957 103.754 73.0958C103.756 73.0929 103.757 73.0886 103.757 73.0886C110.057 74.8043 117.25 77.9451 123.618 83.7095L90.8637 115.849V115.85C89.534 117.09 88.6914 118.839 88.6914 120.791C88.6914 124.551 91.7656 127.595 95.5551 127.595C95.7473 127.595 95.928 127.554 96.1188 127.537C96.1231 127.55 96.1289 127.557 96.1347 127.567C96.2821 127.55 96.431 127.515 96.5813 127.492C96.7619 127.464 96.9368 127.436 97.1146 127.403C102.565 126.334 112.847 115.489 122.205 109.683C127.483 106.457 130.631 105.436 136.421 105.436C141.245 105.436 145.63 107.247 148.96 110.205C149.359 110.498 149.778 110.856 150.247 111.313L170.273 130.367L170.267 130.384L178.973 138.646C181.228 140.768 181.501 144.393 179.365 146.619Z" fill="#1E1E1E"></path><path _ngcontent-ng-c2071322268="" d="M234.281 165.605V165.605C234.572 202.293 205.211 232.345 168.527 232.907L132.624 233.457" stroke="#888888" stroke-width="8" stroke-miterlimit="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="4 24"></path><path _ngcontent-ng-c2071322268="" d="M115.447 229.291C112.225 231.135 112.225 235.781 115.447 237.624L135.106 248.872C138.306 250.702 142.29 248.392 142.29 244.705L142.29 222.21C142.29 218.524 138.306 216.213 135.106 218.044L115.447 229.291Z" fill="#888888"></path><path _ngcontent-ng-c2071322268="" d="M23.277 90.3945V90.3945C22.9855 53.7068 52.3465 23.6548 89.031 23.0929L124.934 22.5428" stroke="#888888" stroke-width="8" stroke-miterlimit="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="4 24"></path><path _ngcontent-ng-c2071322268="" d="M142.111 26.7085C145.333 24.8653 145.333 20.2191 142.111 18.3758L122.452 7.12846C119.252 5.29774 115.268 7.60817 115.268 11.2948L115.268 33.7895C115.268 37.4762 119.252 39.7866 122.451 37.9559L142.111 26.7085Z" fill="#888888"></path><path _ngcontent-ng-c2071322268="" d="M91.2241 235.339V235.339C54.1955 235.628 23.8643 205.998 23.2866 168.973L22.7504 134.605" stroke="#888888" stroke-width="8" stroke-miterlimit="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="4 24"></path><path _ngcontent-ng-c2071322268="" d="M26.8979 117.487C25.0462 114.309 20.4548 114.309 18.6032 117.487L7.2527 136.968C5.38826 140.168 7.69659 144.185 11.4001 144.185L34.101 144.185C37.8045 144.185 40.1128 140.168 38.2484 136.968L26.8979 117.487Z" fill="#888888"></path><path _ngcontent-ng-c2071322268="" d="M164.776 23.0652V23.0652C201.805 22.7764 232.136 52.406 232.713 89.4313L233.25 123.799" stroke="#888888" stroke-width="8" stroke-miterlimit="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="4 24"></path><path _ngcontent-ng-c2071322268="" d="M229.102 140.917C230.954 144.095 235.545 144.095 237.397 140.917L248.747 121.436C250.612 118.236 248.303 114.219 244.6 114.219H221.899C218.196 114.219 215.887 118.236 217.752 121.436L229.102 140.917Z" fill="#888888"></path></svg>
							</span>
						</div>
						<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/login-image.jpg' ); ?>">
					</div>
					<div class="wcr-authform-form-wrapper">
						<div class="wcr-authform-greeter">
							<span class="wcr-authform-greeter-icon-wrapper">
								<svg class="wcr-authform-greeter-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25 25" fill="none"><path _ngcontent-ng-c472896373="" d="M15.3638 3.62354H19.3638C19.8942 3.62354 20.4029 3.83425 20.778 4.20932C21.1531 4.58439 21.3638 5.0931 21.3638 5.62354V19.6235C21.3638 20.154 21.1531 20.6627 20.778 21.0377C20.4029 21.4128 19.8942 21.6235 19.3638 21.6235H15.3638M10.3638 17.6235L15.3638 12.6235M15.3638 12.6235L10.3638 7.62354M15.3638 12.6235H3.36377" stroke-linecap="round" stroke-linejoin="round" stroke="#1E1E1E" stroke-width="2.5"></path></svg>
							</span>
							<h1 class="wcr-authform-title">Sign in</h1>
						</div>
						<form id="wcr-login-form" method="post">
							<?php wp_nonce_field( 'wcr_nonce', 'nonce' ); ?>
							<p>
								<label for="wcr-email"><?php echo esc_html__( 'Email:', 'material-design-child' ); ?></label>
								<input type="email" id="wcr-email" name="email" required>
							</p>
							<p>
								<label for="wcr-email"><?php echo esc_html__( 'Password:', 'material-design-child' ); ?></label>
								<input type="password" id="wcr-password" name="password" required>
							</p>
							<p>
								<button type="submit"><?php echo esc_html__( 'Login', 'material-design-child' ); ?></button>
							</p>
						</form>
					</div>
				</div>
			</div>
			<?php
		}
		return ob_get_clean();
	}

	public function display_user_data() {
		$is_user_logged_in = $this->is_user_logged_in();
		if ( $is_user_logged_in ) {
			$user_data = $this->get_user_data();
			$user_role = $this->get_user_role();
			$name = join( ' ', array_filter( array( $user_data->first_name, $user_data->last_name ) ) );
			?>
			<div class="wcr-user-data">
				<h3><?php echo 'Welcome, ' . esc_html( $name ); ?></h3>
				<p><?php echo '<p>Email: ' . esc_html( $user_data->email ); ?></p>
				<!--<p><?php echo '<p>User Role: ' . esc_html( $user_role ); ?></p>-->
				<div class="wcr-logout-fields-wrapper">
					<?php wp_nonce_field( 'wcr_nonce', 'nonce' ); ?>
					<p>
						<button type="submit" class="wcr-logout-btn"><?php echo esc_html__( 'Logout', 'material-design-child' ); ?></button>
					</p>
				</div>
			</div>
			<?php
		}
	}

	/**
	 * Handle login request from third-party site
	 */
	public function handle_login() {
		// Verify nonce for security.
		if ( ! wp_verify_nonce( $_POST['nonce'], 'wcr_nonce' ) ) {
			wp_send_json_error( 'Security check failed.' );
		}

		$email    = isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$password = isset( $_POST['password'] ) && ! empty( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';

		if ( empty( $email ) || empty( $password ) ) {
			wp_send_json_error( 'Email and password are required' );
		}

		// Send API request to xyz.com to get token
		$wcr_token_data = $this->generate_jwt_token( $email, $password );

		if ( is_wp_error( $wcr_token_data ) ) {
			wp_send_json_error( $wcr_token_data->get_error_message() );
		}

		// Create token_key and save to database
		$token_key = $this->create_and_save_token( $email, $wcr_token_data['jwt_token'], $wcr_token_data['user_data'] );

		if ( $token_key ) {
			// Set cookie with token_key.
			$this->set_token_cookie( $token_key );

			// Fetch and return user data
			$user_data = $this->fetch_user_data( $wcr_token_data['jwt_token'] );

			wp_send_json_success(
				array(
					'message'   => 'Login successful',
					'user_data' => $user_data
				)
			);
		} else {
			wp_send_json_error( 'Unable to login. Try again leter or contact site administrator. Error: Token save error.' );
		}
	}

	public function handle_logout() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'wcr_nonce' ) ) {
			wp_die('Security check failed');
		}

		$this->logout_user();

		wp_send_json_success('Logged out successfully');
	}

	/**
	 * Authenticate with xyz.com API
	 */
	private function generate_jwt_token( $email, $password ) {
		// Step 1: Get Bearer Token.
		$bearer_data = $this->get_bearer_token_data( $email, $password );
		if ( is_wp_error( $bearer_data ) ) {
			return $bearer_data;
		}

		// Step 2: Get JWT Token using Bearer Token.
		$jwt_token = $this->get_jwt_token( $bearer_data['authorization'], $bearer_data['user_data'] );
		return $jwt_token;
	}

	public function get_bearer_token_data( $email, $password ) {

		$bearer_token_url = add_query_arg(
			array(
				'email'    => $email,
				'password' => $password,
			),
			$this->bearer_token_url
		);

		$request_args = array(
			'timeout'   => 30,
			'sslverify' => false,
			'headers'   => array(
				'Content-Type' => 'application/json',
				'Accept'       => 'application/json',
			)
		);

		$response = wp_remote_post( $bearer_token_url, $request_args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_body    = wp_remote_retrieve_body( $response );
		$response_headers = wp_remote_retrieve_headers( $response );

		// Log response for debugging.
		error_log( 'Bearer Token Response Code: ' . $response_code );
		error_log( 'Bearer Token Response Body: ' . $response_body );
		error_log( 'Bearer Token Response Headers: ' . print_r( $response_headers, true ) );

		// Try to parse error message from response body.
		$body_data = json_decode( $response_body, true );

		if ( 200 !== $response_code ) {
			$error_message = 'Authentication failed';

			if ( is_array( $body_data ) && isset( $body_data['message'] ) ) {
				$error_message = $body_data['message'];
			} elseif ( is_array( $body_data ) && isset( $body_data['error'] ) ) {
				$error_message = $body_data['error'];
			}

			return new WP_Error( 'authentication_error', $error_message . ' (HTTP ' . $response_code . ')' );
		}

		// Get authorization header.
		$authorization = '';
		if ( isset( $response_headers['authorization'] ) ) {
			$authorization = $response_headers['authorization'];
		} elseif (isset( $response_headers['Authorization'] ) ) {
			$authorization = $response_headers['Authorization'];
		}

		if ( empty( $authorization ) ) {
			return new WP_Error( 'authentication_error', 'Authorization token not found in response.' );
		}

		return array(
			'authorization' => $authorization,
			'user_data'     => isset( $body_data['data'] ) && ! empty( $body_data['data'] ) && is_array( $body_data['data'] ) ? $body_data['data'] : array(),
		);
	}

	/**
	 * generate JWT Token using Bearer Token.
	 */
	private function get_jwt_token( $bearer_token, $user_data = array() ) {
		$request_args = array(
			'timeout'   => 30,
			'sslverify' => false,
			'headers'   => array(
				'authorization' => $bearer_token,
				'Accept'        => 'application/json',
			),
		);

		$response = wp_remote_get( $this->jwt_token_url, $request_args );

		// Check for WP_Error.
		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'token_request_error', 'JWT Token request error: ' . $response->get_error_message() );
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		// Log response for debugging.
		error_log( 'JWT Token Response Code: ' . $response_code );
		error_log( 'JWT Token Response Body: ' . $response_body );

		if ( 200 !== $response_code ) {
			return new WP_Error( 'token_retrive_error', 'Failed to retrieve JWT token (HTTP ' . $response_code . ')' );
		}

		$data = json_decode( $response_body, true );

		if ( ! is_array( $data ) ) {
			return new WP_Error( 'invalid_response_format', 'Invalid response format from JWT token endpoint.' );
		}

		if ( ! isset( $data['success'] ) || ! filter_var( $data['success'], FILTER_VALIDATE_BOOLEAN ) ) {
			$error_message = 'JWT token generation failed.';
			if ( isset( $data['message'] ) ) {
				$error_message = $data['message'];
			}

			return new WP_Error( 'token_generation_failed', $error_message );
		}

		if ( ! isset( $data['jwt_token'] ) || empty( $data['jwt_token'] ) ) {
			return new WP_Error( 'token_not_found', 'JWT token not found in response.' );
		}

		return array(
			'jwt_token' => $data['jwt_token'],
			'user_data' => $user_data,
		);
	}

	/**
	 * Create unique token_key and save token to database
	 */
	private function create_and_save_token( $email, $wcr_token, $user_data ) {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->table_name;

		// Generate unique token_key
		$token_key = $this->generate_token_key();

		// Set expiry time (30 days from now)
		$expires_at = date( 'Y-m-d H:i:s', time() + $this->cookie_expiry );

		// Check if user already has a token, update if exists
		$existing = $wpdb->get_row( $wpdb->prepare(
			"SELECT id FROM $table_name WHERE user_email = %s",
			$email
		) );

		if ( $existing ) {
			$data = array(
				'token_key'  => $token_key,
				'wcr_token'  => $wcr_token,
				'user_data'  => wp_json_encode( $user_data ),
				'expires_at' => $expires_at,
				'created_at' => current_time( 'mysql' )
			);

			// Update existing record.
			$result = $wpdb->update(
				$table_name,
				$data,
				array( 'user_email' => $email),
				array( '%s', '%s', '%s', '%s', '%s' ),
				array( '%s' )
			);
		} else {
			$data = array(
				'token_key'  => $token_key,
				'wcr_token'  => $wcr_token,
				'user_data'  => wp_json_encode( $user_data ),
				'user_email' => $email,
				'expires_at' => $expires_at
			);

			// Insert new record.
			$result = $wpdb->insert(
				$table_name,
				$data,
				array( '%s', '%s', '%s', '%s', '%s' )
			);
		}

		return $result !== false ? $token_key : false;
	}

	/**
	 * Generate secure token_key
	 */
	private function generate_token_key() {
		return hash( 'sha256', wp_generate_password(32, true) . time() . wp_salt() );
	}

	/**
	 * Set token_key cookie
	 */
	private function set_token_cookie( $token_key ) {
		setcookie(
			$this->cookie_name,
			$token_key,
			time() + $this->cookie_expiry,
			'/',
			'',
			is_ssl(),
			true // HttpOnly flag for security
		);
	}

	public function get_token_key() {
		$token_key = sanitize_text_field( $_COOKIE[ $this->cookie_name ] );

		if ( empty( $token_key ) ) {
			$token_key = false;
		}

		return $token_key;
	}

	/**
	 * Load user data from cookie token_key
	 */
	public function load_user_data_from_cookie() {
		$token_key = $this->get_token_key();

		if ( ! $token_key ) {
			return false;
		}

		$wcr_token = $this->get_token_by_key( $token_key );

		if ( ! $wcr_token ) {
			// Invalid or expired token, clear cookie
			$this->clear_token_cookie();
			return false;
		}

		global $wcr_user_data;

		$user_data = $this->get_user_data_by_key( $token_key );

		if ( $user_data ) {
			// Store user data in global variable or session for use throughout the site.
			$wcr_user_data = $user_data;

			// Update last accessed time.
			$this->update_last_accessed( $token_key );
			return $user_data;
		} else {
			// Fetch user data.
			$user_data = $this->fetch_user_data( $wcr_token );

			if ( $user_data ) {
				// Store user data in global variable or session for use throughout the site.
				$wcr_user_data = $user_data;

				// Update last accessed time.
				$this->update_last_accessed( $token_key );

				return $user_data;
			}
		}

		return false;
	}

	/**
	 * Get token from database by token_key
	 */
	private function get_token_by_key( $token_key ) {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->table_name;

		$result = $wpdb->get_row( $wpdb->prepare(
			"SELECT wcr_token, expires_at FROM $table_name
			 WHERE token_key = %s AND expires_at > NOW()",
			$token_key
		) );

		return $result ? $result->wcr_token : false;
	}

	/**
	 * Get token from database by token_key
	 */
	private function get_user_data_by_key( $token_key ) {
		global $wpdb;
		$data = false;

		$table_name = $wpdb->prefix . $this->table_name;

		$result = $wpdb->get_row( $wpdb->prepare(
			"SELECT user_data FROM $table_name
			 WHERE token_key = %s",
			$token_key
		) );

		if ( $result ) {
			$data = json_decode( $result->user_data );
		}

		return $data;
	}

	/**
	 * Fetch user data from xyz.com using token
	 */
	private function fetch_user_data( $wcr_token ) {
		$request_args = array(
			'timeout'   => 30,
			'sslverify' => false,
			'headers'   => array(
				// 'Authorization' => 'Bearer ' . $wcr_token,
				'Content-Type' => 'application/json',
			),
			'body' => json_encode(
				array(
					'token' => $wcr_token,
				)
			),
		);

		$response = wp_remote_post( $this->verify_jwt_token, $request_args );

		if ( is_wp_error( $response ) ) {
			error_log( 'User Data API Error: ' . $response->get_error_message() );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		return $data;
	}

	/**
	 * Update last accessed time for token
	 */
	private function update_last_accessed( $token_key) {
		global $wpdb;

		$table_name = $wpdb->prefix . $this->table_name;

		$wpdb->update(
			$table_name,
			array( 'last_accessed' => current_time( 'mysql' ) ),
			array( 'token_key' => $token_key),
			array( '%s' ),
			array( '%s' )
		);
	}

	/**
	 * AJAX handler to get user data
	 */
	public function get_user_data_callback() {
		global $wcr_user_data;

		if ( $wcr_user_data) {
			wp_send_json_success( $wcr_user_data );
		} else {
			wp_send_json_error( 'No user data available' );
		}
	}

	/**
	 * Clear token cookie
	 */
	private function clear_token_cookie() {
		setcookie( $this->cookie_name, '', time() - 3600, '/' );
	}

	/**
	 * Logout user and clear token
	 */
	public function logout_user() {
		if ( isset( $_COOKIE[ $this->cookie_name ] ) ) {
			$token_key = $this->get_token_key();

			// Remove token from database.
			global $wpdb;
			$table_name = $wpdb->prefix . $this->table_name;
			$wpdb->delete( $table_name, array( 'token_key' => $token_key ) );

			// Clear cookie
			$this->clear_token_cookie();

			// Clear global user data
			global $wcr_user_data;
			$wcr_user_data = null;
		}
	}

	/**
	 * Check if user is logged in via third-party
	 */
	public function is_user_logged_in() {
		global $wcr_user_data;
		return ! empty( $wcr_user_data );
	}

	/**
	 * Get current user data
	 */
	public function get_user_data() {
		global $wcr_user_data;
		return $wcr_user_data;
	}

	public function get_permissions() {
		$permissions = array();
		$user_data   = $this->get_user_data();

		if ( isset( $user_data->permissions ) && is_array( $user_data->permissions ) ) {
			$permissions = $user_data->permissions;
		}

		return $permissions;
	}

	public function get_user_role() {
		$user_role   = 'blog';
		$permissions = $this->get_permissions();

		if ( is_array( $permissions ) && ! empty( $permissions ) ) {
			$first_key = array_key_first( $permissions );
			$user_role = $permissions[ $first_key ];
		}

		return $user_role;
	}
}

$GLOBALS['wcr_user_manager'] = new WCR_User_Manager();
