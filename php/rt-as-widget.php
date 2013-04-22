<?php
/**
 * rt Anti Spam Widgets
 *
 * @package AntiSpam
 *
 * @since AntiSpam 1.0
 */

/**
 * AntiSpam Login/Registration Widget Class
 *
 * Used to generate the AntiSpam Widget.
 *
 * @since AntiSpam 1.0
 */
class RTAS_Login_Reg_Widget extends WP_Widget {

    function RTAS_Login_Reg_Widget() {
        parent::WP_Widget( false, $name = 'AntiSpam: Login / Registration', array( 'description' => "Login / Registration with AJAX" ) );
    }

    function widget( $args, $instance ) {
        global $rt_anti_spam_widget;
        $rt_anti_spam_widget = 1;
        extract( $args, EXTR_SKIP );

        echo $before_widget;

        $title = empty( $instance['title'] ) ? 'Login / Register' : apply_filters( 'widget_title', $instance['title'] );

        if ( !empty($title) )
            echo $before_title . $title . $after_title;  ?>

        <div id="rtAS-LnR-Tabs" class="rtas-tabs hide-if-no-js"><?php
            if( is_user_logged_in() ) {
                global $current_user;
                get_currentuserinfo(); ?>
                <div id="logged-in-user-container" class="content">
                    <div class="user-salutation">
                        <?php echo get_avatar($current_user->ID); ?>
                        <h4>Hi <?php echo $current_user->display_name; ?></h4>
                        <p><a href="<?php echo admin_url( '/profile.php' ); ?>" title="Your Profile">Your Profile</a></p>
                        <p><a href="<?php echo wp_logout_url( $_SERVER['REQUEST_URI'] ); ?>" title="Log Out">Log Out</a></p>
                    </div>
                </div><?php
            } else { ?>
                <ul id="tabsmenu" class="login-register-tabs">
                    <li class="active"><a href="#rtAS-Login-Block">Login</a></li>
                    <?php if ( get_option( 'users_can_register' ) != false ) { ?><li><a href="#rtas-registration-block">Register</a></li><?php } ?>
                </ul>
                <div id="rtAS-Login-Block" class="content">
                    <div class="rtas-loader"></div>
                    <form name="rtAS-Login-Form" id="rtAS-Login-Form" action="<?php echo RTAS_LOGIN_URL; ?>" method="post">
                        <span class="rtas-widget-info"></span>
                        <p>
                            <input class="rtas-input" type="text" name="log" id="rtAS_login_username" value="<?php _e('Username') ?>" />
                        </p>

                        <p>
                            <input class="rtas-input" type="password" name="pwd" id="rtAS_login_password" value="<?php _e('Password') ?>" />
                        </p>
							<?php do_action('login_form'); ?>
                        <p>
                            <input type="checkbox" name="rememberme" id="rtAS_login_rememberme" value="forever" /><label class="rtas-rememberme-label" for="rtAS_login_rememberme"><?php _e('Remember Me') ?></label>
                        </p>
                        <p>
                            <input type="submit" name="wp-submit" id="rtAS_login_submit" value="<?php _e('Log In'); ?>" />
                            <a class="forgot-password" href="<?php echo RTAS_LOSTPSWD_URL; ?>" title="Password Lost and Found">Forgot Password?</a>
                        </p>
						<?php do_action('login_form'); ?>
                    </form>
                </div><?php
                if ( get_option( 'users_can_register' ) != false ) { ?>
                    <div id="rtas-registration-block" class="content">
                        <div class="rtas-loader"></div>
                        <form name="rtAS-Registration-Form" id="rtAS-Registration-Form" action="<?php echo RTAS_REGISTER_URL; ?>" method="post">
                            <span class="rtas-widget-info"></span>
                            <p>
                                <input class="rtas-input" type="text" name="user_login" id="rtAS_registration_username" value="<?php _e('Username') ?>" /><span></span>
                            </p>

                            <p>
                                <input class="rtas-input" type="text" name="user_email" id="rtAS_registration_email" value="<?php _e('Email Address') ?>" /><span></span>
                            </p>

                            <?php echo rtas_recaptcha_code(); ?>
                            <span></span>
							<?php do_action('register_form'); ?>
                            <p>
                                <input type="submit" name="wp-submit" id="rtAS_registration_submit" value="<?php _e('Register'); ?>" />
                            </p>

                        </form>
                    </div><?php
                }
            } ?>
        </div>
        <div class="hide-if-js"><?php _e( 'Please enable JavaScript on your browser to use this widget.' ); ?></div><?php
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        return $instance;
    }

    function form( $instance ) {
         if( function_exists( 'is_multisite' ) && is_multisite() ) {
             if ( get_site_option('registration') != 'user' &&  get_site_option('registration') != 'all' ) { ?>
                <p>
                    <span>NOTE: Registration is disabled  on this site. <?php if ( is_super_admin() ) { ?>Click here to <a href="<?php echo network_admin_url('/settings.php#registration1'); ?>" title="Enable User-Registration">enable user-registration</a>.<?php } ?></span>
                </p><?php
            }
         } else {
             if ( get_option('users_can_register') == false ) { ?>
                <p>
                    <span>NOTE: Registration is disabled  on this site. Click here to <a href="<?php echo admin_url('/options-general.php#users_can_register'); ?>" title="Enable User-Registration">enable user-registration</a>.</span>
                </p><?php
            }
         }

        $instance = wp_parse_args((array) $instance, array('title' => ''));
        $title = $instance['title']; ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title: </label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p><?php
    }

}
add_action( 'widgets_init', create_function( '', 'return register_widget( "RTAS_Login_Reg_Widget" );' ) );

/* Enqueueing CSS files */
function rtas_stylesheet() {
    $rt_as_style_url = RTAS_CSS_DIR_URL . '/rt-as-style.css';
    $rt_as_style_file = RTAS_CSS . '/rt-as-style.css';
    $rt_as_style_url_ie = RTAS_CSS_DIR_URL . '/rt-as-style-ie.css';
    $rt_as_style_file_ie = RTAS_CSS . '/rt-as-style-ie.css';

    if ( file_exists( $rt_as_style_file ) ) {
        wp_register_style( 'rt-as-style', $rt_as_style_url );
        wp_enqueue_style( 'rt-as-style');
    }

    if( file_exists( $rt_as_style_file_ie ) ) {
        wp_register_style( 'rt-as-style-ie', RTAS_CSS_DIR_URL . '/rt-as-style-ie.css', '', '', 'screen, projection' );
        $GLOBALS['wp_styles']->add_data( 'rt-as-style-ie', 'conditional', 'IE' );
        wp_enqueue_style( 'rt-as-style-ie' );
    }
}
add_action( 'wp_print_styles', 'rtas_stylesheet' );

/* Enqueueing JavaScript files */
function rtas_javascript() {

    $rt_as_js_url = RTAS_JS_DIR_URL . '/rt-as-common.js';
    $rt_as_js_file = RTAS_JS . '/rt-as-common.js';

    if ( file_exists( $rt_as_js_file ) ) {
        wp_enqueue_script( 'rt-as-common', $rt_as_js_url, array( 'jquery' ), null, true );
        $admin_ajax_url = array( 'admin_ajax_url' => admin_url( 'admin-ajax.php' ) );
        wp_localize_script( 'rt-as-common', 'url', $admin_ajax_url );
    }
    $protocol = is_ssl() ? 'https://' : 'http://';
    wp_register_script( 'recaptcha-ajax', $protocol.'www.google.com/recaptcha/api/js/recaptcha_ajax.js', '', null, true );
}
add_action( 'wp_enqueue_scripts', 'rtas_javascript', 9999 );

function rtas_widget_javascript() {
    wp_print_scripts('recaptcha-ajax');
}
add_action( 'wp_footer', 'rtas_widget_javascript', 9999 );

function rtas_check_username() {
    if ( username_exists( $_POST['username'] ) ){
        echo 'Already exists. If it is yours, click <a href="' . RTAS_LOSTPSWD_URL . '">here</a> to reset password.';
    } elseif ( $_POST['username'] == '' ) {
        echo 'Username cannot be empty.';
    } else {
        echo "Available.";
    }
    die();
}
add_action( 'wp_ajax_check_username', 'rtas_check_username' );
add_action( 'wp_ajax_nopriv_check_username', 'rtas_check_username' );

function rtas_register_validation() {
    $errors = array();
    if ( isset( $_POST['username'] ) ) {
        if ( username_exists( $_POST['username'] ) ){
            $errors['username'] = 'Already exists. If it is yours, click <a href="' . RTAS_LOSTPSWD_URL . '">here</a> to reset password.';
        } elseif ( $_POST['username'] == '' ) {
            $errors['username'] = 'Username cannot be empty.';
        } else {
            $errors['username'] = "Available.";
        }
        if( isset($_POST['check_username']) ) {
            echo $errors['username'];
            die();
        }
    }

    if ( is_email( $_POST['email'] ) && email_exists( $_POST['email'] ) ) {
        $errors['email'] = 'Already exists. If it is yours, click <a href="' . RTAS_LOSTPSWD_URL . '">here</a> to reset password.';
    } elseif ( !is_email( $_POST['email'] ) ) {
        $errors['email'] = 'Enter a valid email.';
    } else {
        $errors['email'] = "Valid Email.";
    }
    if( isset( $_POST['check_email'] ) ) {
        echo $errors['email'];
        die();
    }

    if ( !rtas_check_recaptcha_answer() ){
        $errors['recaptcha'] = 'Captcha code is wrong. Renter Captcha Code.';
    } elseif( $errors['email'] == 'Valid Email.' && $errors['username'] == 'Available.' ) {
        $random_password = wp_generate_password( 12, false );
        if( function_exists( 'is_multisite' ) && is_multisite() ) {
            $new_user_id = wpmu_create_user( $_POST['username'], $random_password, $_POST['email'] );
        } else {
            $new_user_id = wp_create_user( $_POST['username'], $random_password, $_POST['email'] );
        }
        $errors['recaptcha'] = "Please check your email inbox for verification.";
        wp_new_user_notification( $new_user_id, $random_password );
    } else {
        $errors['recaptcha'] = "Renter Captcha Code.";
    }
    echo json_encode($errors);

    die(); // this is required to return a proper result
}
add_action( 'wp_ajax_validate_registration', 'rtas_register_validation' );
add_action( 'wp_ajax_nopriv_validate_registration', 'rtas_register_validation' );

function rtas_login_validation() {
    if( is_wp_error( wp_signon() ) )
        echo false;
    else
        echo true;

    die(); // this is required to return a proper result
}
add_action( 'wp_ajax_validate_login', 'rtas_login_validation' );
add_action( 'wp_ajax_nopriv_validate_login', 'rtas_login_validation' );
