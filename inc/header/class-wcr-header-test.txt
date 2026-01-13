<?php
class WCR_Header_Test {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		add_filter( 'wcr_get_current_wcr_user', array( $this, 'get_current_wcr_user' ) );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'WCR Header Test',
            'WCR Header Test',
            'manage_options',
            'wcr-header-test',
            array( $this, 'render_options_page' )
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'wcr_theme_options_group',
            'wcr_test_user_login_status',
            array( $this, 'sanitize_radio' )
        );

        register_setting(
            'wcr_theme_options_group',
            'wcr_test_userrole',
            array( $this, 'sanitize_select' )
        );
    }

    /**
     * Sanitize radio input
     */
    public function sanitize_radio( $input ) {
        $valid = array( 'logged_in', 'logged_out' );
        return in_array( $input, $valid ) ? $input : 'logged_out';
    }

    /**
     * Sanitize select input
     */
    public function sanitize_select( $input ) {
        $valid = array( 'org_admin', 'consult', 'blog' );
        return in_array( $input, $valid ) ? $input : 'org_admin';
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts( $hook ) {
        // Only load on our options page
        if ( 'toplevel_page_wcr-header-test' !== $hook ) {
            return;
        }

		wp_enqueue_style(
			'wcr_admin_header_css',
			get_stylesheet_directory_uri() . '/assets/css/admin/admin-header.css',
			array(),
			filemtime( get_stylesheet_directory() . '/assets/css/admin/admin-header.css' )
		);

        // Enqueue the script
        wp_enqueue_script(
            'wcr_admin_header_js',
            get_stylesheet_directory_uri() . '/assets/js/admin/admin-header.js', // Empty src - we'll inline the script
            array( 'jquery' ),
            filemtime( get_stylesheet_directory() . '/assets/js/admin/admin-header.js' ),
            true
        );

        // Add inline JavaScript
        $inline_css = $this->add_inline_css();
        $inline_js  = $this->add_inline_js();
		wp_add_inline_style( 'wcr_admin_header_css', $inline_css );
		wp_add_inline_script( 'wcr_admin_header_js', $inline_js );
    }

    /**
     * Add inline style for conditional logic
     */
    private function add_inline_css() {
		ob_start();
        ?>
		<style type="text/css">
        .button-disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        #userrole-row {
            transition: all 0.3s ease-in-out;
        }
        .form-table tr {
            transition: background-color 0.3s ease;
        }
        .form-table tr:hover {
            background-color: #f9f9f9;
        }
        </style>
		<?php
		return ob_get_clean();
	}
	/**
     * Add inline JavaScript for conditional logic
     */
    private function add_inline_js() {
		ob_start();
        ?>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Function to toggle user role field visibility
            function toggleUserRoleField() {
                var selectedValue = $('input[name="wcr_test_user_login_status"]:checked').val();
                if (selectedValue === 'logged_in') {
                    $('#userrole-row').slideDown(300);
                } else {
                    $('#userrole-row').slideUp(300);
                }
            }

            // Initial check on page load
            toggleUserRoleField();

            // Bind to change event
            $('input[name="wcr_test_user_login_status"]').change(function() {
                toggleUserRoleField();
            });

            // Enhanced: Add visual feedback when saving
            $('form').on('submit', function() {
                var submitButton = $(this).find('#submit');
                var originalText = submitButton.val();

                submitButton.prop('disabled', true)
                           .val('Saving...')
                           .addClass('button-disabled');

                setTimeout(function() {
                    submitButton.prop('disabled', false)
                               .val(originalText)
                               .removeClass('button-disabled');
                }, 2000);
            });

            // Enhanced: Add confirmation when changing settings
            $('input[name="wcr_test_user_login_status"]').change(function() {
                var newValue = $(this).val();
                var oldValue = '<?php echo esc_js( get_option( 'wcr_test_user_login_status', 'logged_out' ) ); ?>';

                if (newValue !== oldValue) {
                    if (newValue === 'logged_out' && $('#userrole-row select').val() !== 'org_admin') {
						return true;
						/*
                        if (confirm('Changing to "Logged Out" will ignore the selected user role. Continue?')) {
                        } else {
                        }
                            $('input[name="wcr_test_user_login_status"][value="' + oldValue + '"]').prop('checked', true);
                            toggleUserRoleField();
                            return false;
						*/
                    }
                }
            });
        });
        </script>
        <?php
		return ob_get_clean();
    }

    /**
     * Render options page
     */
    public function render_options_page() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Show success message
        if ( isset( $_GET['settings-updated'] ) ) {
            add_settings_error(
                'wcr_messages',
                'wcr_message',
                __( 'Settings Saved', 'text-domain' ),
                'updated'
            );
        }

        // Show error/update messages
        settings_errors( 'wcr_messages' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'wcr_theme_options_group' );
                do_settings_sections( 'wcr_theme_options_group' );

				$user_roles    = get_field( 'wcr_user_roles', 'option' );
				$selected_role = get_option( 'wcr_test_userrole' );
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">
                            <label for="wcr_test_user_login_status">User Login Status</label>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">User Login Status</legend>
                                <label for="logged_in">
                                    <input type="radio"
                                           id="logged_in"
                                           name="wcr_test_user_login_status"
                                           value="logged_in"
                                           <?php checked( get_option( 'wcr_test_user_login_status' ), 'logged_in' ); ?> />
                                    <span><?php _e( 'Logged In', 'text-domain' ); ?></span>
                                </label>
                                <br>
                                <label for="logged_out">
                                    <input type="radio"
                                           id="logged_out"
                                           name="wcr_test_user_login_status"
                                           value="logged_out"
                                           <?php checked( get_option( 'wcr_test_user_login_status' ), 'logged_out' ); ?> />
                                    <span><?php _e( 'Logged Out', 'text-domain' ); ?></span>
                                </label>
                            </fieldset>
                            <p class="description">
                                <?php _e( 'Select whether the content should be visible to logged-in or logged-out users.', 'text-domain' ); ?>
                            </p>
                        </td>
                    </tr>

                    <tr valign="top" id="userrole-row">
                        <th scope="row">
                            <label for="wcr_test_userrole">User Role</label>
                        </th>
                        <td>
                            <select name="wcr_test_userrole" id="wcr_test_userrole" class="regular-text">
								<?php
								foreach ( $user_roles as $k => $data ) {
									?>
									<option value="<?php echo esc_attr( $data['user_role'] ); ?>" <?php selected( $selected_role, $data['user_role'] ); ?>><?php echo esc_html( $data['user_role_name'] ); ?></option>
									<?php
								}
								?>
                            </select>
                        </td>
                    </tr>
                </table>

                <?php submit_button( __( 'Save Settings', 'text-domain' ) ); ?>
            </form>
			<?php /* ?>

            <div class="card" style="margin-top: 20px;">
                <h2><?php _e( 'Usage Information', 'text-domain' ); ?></h2>
                <p><?php _e( 'To use these settings in your theme, add the following code to your template files:', 'text-domain' ); ?></p>
                <code>
                    $login_status = get_option('wcr_test_user_login_status');<br>
                    $user_role = get_option('wcr_test_userrole');<br>
                    <br>
                    if ($login_status === 'logged_in' && is_user_logged_in()) {<br>
                    &nbsp;&nbsp;// Check user role and show content<br>
                    }
                </code>
            </div>
			<?php */ ?>
        </div>
        <?php
    }

    /**
     * Get option value with default fallback
     */
    public static function get_option( $option_name, $default = '' ) {
        $value = get_option( $option_name, $default );

        switch( $option_name ) {
            case 'wcr_test_user_login_status':
                return in_array( $value, array( 'logged_in', 'logged_out' ) ) ? $value : $default;

            case 'wcr_test_userrole':
                return in_array( $value, array( 'org_admin', 'consult', 'blog' ) ) ? $value : $default;

            default:
                return $value;
        }
    }

    /**
     * Check if content should be displayed based on settings
     */
    public static function should_display_content() {
        $login_status = self::get_option( 'wcr_test_user_login_status', 'logged_out' );
        $user_role = self::get_option( 'wcr_test_userrole', 'org_admin' );

        if ( $login_status === 'logged_in' ) {
            if ( ! is_user_logged_in() ) {
                return false;
            }

            // Check if current user has the selected role
            $current_user = wp_get_current_user();
            return in_array( $user_role, $current_user->roles );

        } elseif ( $login_status === 'logged_out' ) {
            return ! is_user_logged_in();
        }

        return false;
    }

	public function get_current_wcr_user( $current_user ) {
		$user_login_status = get_option( 'wcr_test_user_login_status', 'logged_out' );
		if ( $user_login_status && 'logged_in' === $user_login_status ) {
			$new_user = new stdClass();
			$new_user->user_id = 999;
			$test_userrole     = get_option( 'wcr_test_userrole', '' );
			$new_user->role = ( $test_userrole ) ? $test_userrole : 'test';
			$current_user = $new_user;
		}
		return $current_user;
	}
}
new WCR_Header_Test();
