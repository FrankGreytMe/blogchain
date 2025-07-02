<?php
class WCR_Token {

	private $option_name      = 'wcr_jwt_token';
	private $bearer_token_url = 'https://getme.global/api/v1/auth/sign_in';
	private $jwt_token_url    = 'https://getme.global/api/v1/wordpress_auth/jwt_token';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_post_wcr_generate_token', array( $this, 'handle_token_generation' ) );
	}

	/**
	 * Register a custom menu page.
	 */
	public function add_admin_menu(){
		add_menu_page(
			esc_html__( 'WCR Token', 'material-design-child' ),
			esc_html__( 'WCR Token', 'material-design-child' ),
			'manage_options',
			'wcr-token',
			array( $this, 'render_admin_panel' )
		);
	}

	/**
	 * Display a custom menu page.
	 */
	public function render_admin_panel(){
		$error_message   = get_transient( 'wcr_tokens_error' );
		$success_message = get_transient( 'wcr_tokens_success' );
		$token           = $this->get_token();

		// Clear transients after displaying.
		if ( $error_message ) {
			delete_transient( 'wcr_tokens_error' );
		}
		if ( $success_message ) {
			delete_transient( 'wcr_tokens_success' );
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'WCR Token', 'material-design-child' ); ?></h1>
			<?php
			if ( $error_message ) {
				?>
				<div class="notice notice-error is-dismissible">
					<p><?php echo esc_html( $error_message); ?></p>
				</div>
				<?php
			}

			if ( $success_message ) {
				?>
				<div class="notice notice-success is-dismissible">
					<p><?php echo esc_html( $success_message); ?></p>
				</div>
				<?php
			}
			?>
			<div class="wcr-token-container">
				<div class="wcr-token-settings">
					<h2><?php echo esc_html__( 'Generate Token', 'material-design-child' ); ?></h2>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="wcr-token-form">
						<?php wp_nonce_field( 'wcr_generate_token_nonce', 'wcr_nonce' ); ?>
						<input type="hidden" name="action" value="wcr_generate_token">
						<table class="form-table">
							<tr>
								<th scope="row"><label for="wcr_email"><?php echo esc_html__( 'Email', 'material-design-child' ); ?></label></th>
								<td><input type="email" autocomplete="off" id="wcr_email" name="wcr_email" class="regular-text" required ></td>
							</tr>
							<tr>
								<th scope="row"><label for="wcr_password"><?php echo esc_html__( 'Password', 'material-design-child' ); ?></label></th>
								<td><input type="password" autocomplete="off" id="wcr_password" name="wcr_password" class="regular-text" required></td>
							</tr>
						</table>
						<p class="submit">
							<input type="submit" name="submit" id="submit" class="button button-primary" value="Generate Token">
						</p>
					</form>
				</div>
				<div class="wcr-token-display">
					<h2><?php echo esc_html__( 'JWT Token', 'material-design-child' ); ?></h2>
					<table class="form-table">
						<tr>
							<th scope="row"><label for="wcr_jwt_token"><?php echo esc_html__( 'JWT Token', 'material-design-child' ); ?></label></th>
							<td><input type="text" id="wcr_jwt_token" name="wcr_jwt_token" class="large-text" value="<?php echo esc_attr( $token ); ?>" readonly></td>
						</tr>
						<tr>
							<th scope="row"><label for="wcr_user_info"><?php echo esc_html__( 'Angular User Info', 'material-design-child' ); ?></label></th>
							<td>
								<?php
								$url = 'https://getme.global/api/v1/wordpress_auth/verify_jwt_token'; // <-- Change this URL

								$response = wp_remote_post($url, [
									'timeout' => 15,
									'headers' => [
										'Content-Type' => 'application/json',
									],
									'body' => json_encode(['token' => $token]),
								]);

								if ( is_wp_error( $response ) ) {
									echo 'Error: '.$response->get_error_message();
								} else {
									$body = wp_remote_retrieve_body($response);
									$data = json_decode($body, true);

									if ( ! empty( $data['success'] ) && $data['success'] === true && ! empty( $data['user'] ) ) {
										$user = $data['user'];
										?>
										<ul>
											<li><strong>ID:</strong> <?php echo esc_html($user['id']); ?></li>
											<li><strong>Username:</strong> <?php echo esc_html($user['username']); ?></li>
											<li><strong>Name:</strong> <?php echo esc_html($user['name']); ?></li>
											<li><strong>Email:</strong> <?php echo esc_html($user['email']); ?></li>
											<li><strong>Permissions:</strong> <?php echo esc_html(implode(', ', $user['permissions'] ?? [])); ?></li>
											<li><strong>Organization:</strong> <?php echo esc_html($user['organization']['name'] ?? ''); ?></li>
										</ul>
										<?php
									}
								}
								?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<?php
	}

	public function handle_token_generation() {
		// Verify nonce.
		if ( ! wp_verify_nonce( $_POST['wcr_nonce'], 'wcr_generate_token_nonce' ) ) {
			set_transient(
				'wcr_tokens_error',
				esc_html__( 'Security check failed.', 'material-design-child' ),
				30
			);
			wp_redirect( admin_url( 'admin.php?page=wcr-token' ) );
			exit;
		}

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			set_transient(
				'wcr_tokens_error',
				esc_html__( 'Insufficient permissions.', 'material-design-child' ),
				30
			);
			wp_redirect( admin_url( 'admin.php?page=wcr-token' ) );
			exit;
		}

		$email    = isset( $_POST['wcr_email'] ) && ! empty( $_POST['wcr_email'] ) ? sanitize_email( wp_unslash( $_POST['wcr_email'] ) ) : '';
		$password = isset( $_POST['wcr_password'] ) && ! empty( $_POST['wcr_password'] ) ? sanitize_text_field( wp_unslash( $_POST['wcr_password'] ) ) : '';

		if ( empty( $email ) || empty( $password ) ) {
			set_transient(
				'wcr_tokens_error',
				'Email and password are required.',
				30
			);
			wp_redirect( admin_url( 'admin.php?page=wcr-token' ) );
			exit;
		}

		$jwt_token = $this->generate_jwt_token( $email, $password );

		if ( is_wp_error( $jwt_token ) ) {
			set_transient(
				'wcr_tokens_error',
				$jwt_token->get_error_message(),
				30
			);
		} else {
			// Save token to options.
			update_option( $this->option_name, $jwt_token );
			set_transient(
				'wcr_tokens_success',
				'JWT token generated successfully!',
				30
			);
		}

		wp_redirect( admin_url( 'admin.php?page=wcr-token' ) );
		exit;
	}

	public function generate_jwt_token( $email, $password ) {
		// Step 1: Get Bearer Token.
		$bearer_token = $this->get_bearer_token( $email, $password );
		if ( is_wp_error( $bearer_token ) ) {
			return $bearer_token;
		}

		// Step 2: Get JWT Token using Bearer Token.
		$jwt_token = $this->get_jwt_token( $bearer_token );
		return $jwt_token;
	}

	public function get_bearer_token( $email, $password ) {

		$bearer_token_url = add_query_arg(
			array(
				'email'    => $email,
				'password' => $password,
			),
			$this->bearer_token_url
		);

		$response = wp_remote_post(
			$bearer_token_url,
			array(
				'timeout'   => 30,
				'sslverify' => false,
				'headers'   => array(
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
				)
			)
		);

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

		if ( 200 !== $response_code ) {
			$error_message = 'Authentication failed';

			// Try to parse error message from response body.
			$body_data = json_decode( $response_body, true );
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

		return $authorization;
	}

	/**
	 * generate JWT Token using Bearer Token.
	 */
	private function get_jwt_token( $bearer_token ) {
		$response = wp_remote_get( $this->jwt_token_url, array(
			'timeout'   => 30,
			'sslverify' => false,
			'headers'   => array(
				'authorization' => $bearer_token,
				'Accept'        => 'application/json',
			),
		) );

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

		return $data['jwt_token'];
	}

	public function get_token() {
		return get_option( $this->option_name, '' );
	}
}
$GLOBALS['wcr_token'] = new WCR_Token();
