<?php
/**
 * rt Anti Spam Functions
 *
 * @package AntiSpam
 *
 * @since AntiSpam 1.0
 */

/**
 * AntiSpam Plugin Class
 *
 * Used to generate the AntiSpam Admin Options/Settings.
 *
 * @since AntiSpam 1.0
 */
class rt_anti_spam {

	// constructor of class, PHP4 compatible construction for backward compatibility
	function rt_anti_spam() {
            if( function_exists( 'is_multisite' ) && is_multisite() ) {
                add_action( 'network_admin_menu', array( &$this, 'admin_menu' ) );
            }
            else {
                add_action( 'admin_init', array( &$this, 'register_settings' ), 10, 2 );
                add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
            }
            add_filter( 'plugin_action_links', array(&$this, 'settings_link' ), 10, 2 );
	}
	
        // Register Settings
        function register_settings() {       
            register_setting( 'rt_anti_spam_settings', 'rt_anti_spam_settings' );
                if ( get_option( 'users_can_register' ) == false )
                    add_settings_error( 'Registration Disabled', 'registration_disabled', __( 'Note - Registration is disabled on this site. Click here to <a href="' . admin_url( '/options-general.php#users_can_register' ) . '" title="Enable User-Registration">enable user-registration</a>.' ) );
        }
        
        // Add settings link on plugin page
        function settings_link( $links, $file ) {
            if ( $file == RTAS_BASENAME )
                $links[] = '<a href="'. admin_url( 'options-general.php?page=rt-anti-spam' ) . '">' . __( 'Settings' ) . '</a>';
            return $links;
        }
        
	// extend the admin menu / add particular styles and scripts
	function admin_menu() {
            if ( function_exists( 'is_multisite' ) && is_multisite() )
                $this->pagehook = add_submenu_page( 'settings.php', __( 'AntiSpam Options' ), __( 'AntiSpam Options'), 'manage_network_options', 'rt-anti-spam', array( &$this, 'show_page' ) );
            else
                $this->pagehook = add_options_page(__( 'AntiSpam Options' ), __( 'AntiSpam Options'), 'manage_options', 'rt-anti-spam', array( &$this, 'show_page' ) );
            add_action( 'load-'.$this->pagehook, array( &$this, 'load_page' ) );
            add_action( 'admin_print_styles-' . $this->pagehook, array( &$this, 'admin_styles' ) );
            add_action( 'admin_print_scripts-' . $this->pagehook, array( &$this, 'admin_scripts' ) );
	}
        
        // admin styles
        function admin_styles() {
            wp_enqueue_style( 'rt-as-admin-style', RTAS_CSS_DIR_URL . '/rt-as-admin.css' );
        }
        
        // admin scripts
        function admin_scripts() {
            wp_enqueue_script( 'rt-as-admin-scripts', RTAS_JS_DIR_URL . '/rt-as-admin.js' );
            wp_enqueue_script('rt-fb-share', ('http://static.ak.fbcdn.net/connect.php/js/FB.Share'),'', '', true );
        }
	
	// will be executed if wordpress core detects this page has to be rendered
	function load_page() {
            wp_enqueue_script( 'common' );
            wp_enqueue_script( 'wp-lists' );
            wp_enqueue_script( 'postbox' );
	}
	
	// executed to show the plugins complete admin page
	function show_page() {
            if( function_exists( 'is_multisite' ) && is_multisite() ) {
                if( isset( $_POST['Submit'] ) ) {
                    $public_key = ( isset( $_POST['rt_anti_spam_settings']['recaptcha']['public_key'] ) ) ? trim( $_POST['rt_anti_spam_settings']['recaptcha']['public_key'] ) : '';
                    $private_key = ( isset( $_POST['rt_anti_spam_settings']['recaptcha']['private_key'] ) ) ? trim( $_POST['rt_anti_spam_settings']['recaptcha']['private_key'] ) : '';

                    $rt_anti_spam_settings = array(
                        'recaptcha' => array(
                            'public_key' => $public_key ,
                            'private_key' => $private_key
                        )
                    );

                    update_site_option( 'rt_anti_spam_settings', $rt_anti_spam_settings );
                }

            }
            global $screen_layout_columns;
            add_meta_box( 'rt-as-recaptcha-settings', 'reCAPTCHA Settings', array( &$this, 'recaptcha_metabox' ), $this->pagehook, 'normal', 'core' ); ?>
            <div id="howto-metaboxes-general" class="wrap">
                <?php screen_icon( 'options-general' ); ?>
                <h2><?php _e( 'AntiSpam Options' ); ?></h2>
                <form action="<?php echo ( function_exists( 'is_multisite' ) && is_multisite() ) ? '' : 'options.php'; ?>" method="post">
                    <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
                    <?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
                    <input type="hidden" name="action" value="save_rt_anti_spam_options" />

                    <div id="poststuff" class="metabox-holder has-right-sidebar">
                        <div id="side-info-column" class="inner-sidebar">
                            <?php rtas_default_sidebar(); ?>
                        </div>
                        <div id="post-body" class="has-sidebar">
                            <div id="post-body-content" class="has-sidebar-content">
                                <?php settings_fields( 'rt_anti_spam_settings' ); ?>
                                <?php do_meta_boxes( $this->pagehook, 'normal', '' ); ?>
                                <p>
                                    <input type="submit" value="Save Changes" class="button-primary" name="Submit"/>	
                                </p>
                            </div>
                        </div>
                        <br class="clear"/>
                    </div>	
                </form>
            </div>
            <script type="text/javascript">
                    //<![CDATA[
                    jQuery(document).ready( function($) {
                            // close postboxes that should be closed
                            $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                            // postboxes setup
                            postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
                    });
                    //]]>
            </script><?php
	}

	function recaptcha_metabox() { ?>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <td colspan="2">
                            <span class="description">You can get the keys from <a target="_blank" href="http://www.google.com/recaptcha/whyrecaptcha" title="reCaptcha">here</a>.</span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="recaptcha_public_key">Public Key</label></th>
                        <td>
                            <input type="text" value="<?php echo RTAS_PUBLIC_KEY; ?>" size="40" name="rt_anti_spam_settings[recaptcha][public_key]" id="recaptcha_public_key" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label for="recaptcha_private_key">Private Key</label></th>
                        <td>
                            <input type="text" value="<?php echo RTAS_PRIVATE_KEY; ?>" size="40" name="rt_anti_spam_settings[recaptcha][private_key]" id="recaptcha_private_key" />
                        </td>
                    </tr>
                </tbody>
            </table><?php
	}
	
	
}

$rt_anti_spam = new rt_anti_spam();

/**
 * 
 *
 * @since AntiSpam 1.0
 */
function rt_anti_spam_defaults() {
    $defaults = array( 'recaptcha' => array( 'public_key' => 'Public Key', 'private_key' => 'Private Key' ) );
    if ( function_exists( 'is_multisite' ) && is_multisite() ) {
        if ( !get_site_option( 'rt_anti_spam_settings' ) )
            update_site_option( 'rt_anti_spam_settings', $defaults );
    } else {
        if ( !get_option( 'rt_anti_spam_settings' ) )
            update_option( 'rt_anti_spam_settings', $defaults );
    }
}

/**
 * 
 *
 * @since AntiSpam 1.0
 */
function rtas_check_recaptcha_answer() {
    $resp = recaptcha_check_answer( RTAS_PRIVATE_KEY, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field'] );
    return ( !$resp->is_valid ) ? false : true;
}

/**
 * 
 *
 * @since AntiSpam 1.0
 */
function rtas_validate_captcha( $errors, $sanitized_user_login, $user_email ) {
    if ( !rtas_check_recaptcha_answer() && $_POST['recaptcha_challenge_field'] )
        $errors->add( 'invalid_captcha', __( '<strong>ERROR</strong>: Captcha code is wrong.' ) );
    return $errors;
}
add_filter( 'registration_errors', 'rtas_validate_captcha', '', 3 );

/**
 * 
 *
 * @since AntiSpam 1.0
 */
function rtas_ms_validate_captcha( $result ) {
    if ( !rtas_check_recaptcha_answer() && $_POST['recaptcha_challenge_field'] )
        $result['errors']->add( 'invalid_captcha', __( 'Captcha code is wrong.' ) );
    return $result;
}
add_filter( 'wpmu_validate_user_signup', 'rtas_ms_validate_captcha' );

/**
 * 
 *
 * @since AntiSpam 1.0
 */
function rtas_recaptcha_error_shake( $shake_error_codes ) {
    $shake_error_codes[] = 'invalid_captcha';
    return $shake_error_codes;
}
add_filter( 'shake_error_codes', 'rtas_recaptcha_error_shake' );

/**
 * 
 *
 * @since AntiSpam 1.0
 */
function rtas_add_captcha_validation() { ?>
    <style type="text/css">
        #login { width : 340px; }
    </style><?php
    rtas_recaptcha_code();
}
add_action( 'register_form', 'rtas_add_captcha_validation' );

/**
 * 
 *
 * @since AntiSpam 1.0
 */
function rtas_ms_add_captcha_validation($errors) {
    if ( $errmsg = $errors->get_error_message('invalid_captcha') ) {
		echo '<p class="error">' . $errmsg . '</p>';
    }
    rtas_recaptcha_code();
}
add_action( 'signup_extra_fields', 'rtas_ms_add_captcha_validation' );

/**
 * 
 *
 * @since AntiSpam 1.0
 */
function rtas_login_styles( ) { 
    if ( isset( $_GET['action'] ) && 'register' == $_GET['action'] ) {
        echo "<link rel='stylesheet' href='" . esc_url( RTAS_CSS_DIR_URL . '/rt-as-recaptcha-style.css' ) . "' type='text/css' />\n";
    }
}
add_action( 'login_head', 'rtas_login_styles' );

/**
 * 
 *
 * @since AntiSpam 1.0
 */
function rtas_recaptcha_code() { ?>
    <script type="text/javascript">
        var RecaptchaOptions = {
            theme : 'custom',
            custom_theme_widget : 'rtas-recaptcha-container'
        };
    </script>
    
        <div id="rtas-recaptcha-container">
            <div id="rtas-recaptcha">
                <div id="recaptcha_image"></div>
                <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrect please try again</div>

                <div class="recaptcha-code">
                    <span class="recaptcha_only_if_image">Enter the words above:</span>
                    <span class="recaptcha_only_if_audio">Enter the numbers you hear:</span>
                    <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />
                </div>
                
                <div class="recaptcha-buttons">
                    <div class="recaptcha_another"><a href="javascript:Recaptcha.reload()">Get another CAPTCHA</a></div>
                    <div class="recaptcha_only_if_image"><a href="javascript:Recaptcha.switch_type('audio')">Get an audio CAPTCHA</a></div>
                    <div class="recaptcha_only_if_audio"><a href="javascript:Recaptcha.switch_type('image')">Get an image CAPTCHA</a></div>

                    <div class="recaptcha_help"><a href="javascript:Recaptcha.showhelp()">Help</a></div>
                </div>
            </div>
        </div><?php
    echo recaptcha_get_html( RTAS_PUBLIC_KEY );
}

/**
 * 
 *
 * @since AntiSpam 1.0
 */
function rtas_multisite_notices() { 
    if ( get_site_option( 'registration' ) != 'user' &&  get_site_option( 'registration' ) != 'all' ) { ?>
        <div class="error settings-error" id="setting-error-registration_disabled"> 
            <p><strong>Note - Registration is disabled on this site. Click here to <a title="Enable User-Registration" href="<?php echo network_admin_url( '/settings.php#register1' ); ?>">enable user-registration</a>.</strong></p>
        </div><?php
    }
} 
add_action( 'network_admin_notices', 'rtas_multisite_notices' );

/**
 * Default admin sidebar with metabox styling
 *
 * @since AntiSpam 1.0
 */
function rtas_default_sidebar() { ?>
    <div class="postbox" id="social">
        <div title="<?php _e( 'Click to toggle' ); ?>" class="handlediv"><br /></div>
        <h3 class="hndle"><span><?php _e( 'Getting Social is Good' ); ?></span></h3>
        <div class="inside" style="text-align:center;">
            <a href="<?php printf( '%s', 'http://www.facebook.com/rtPanel' ); ?>" target="_blank" title="<?php _e( 'Become a fan on Facebook' ); ?>" class="facebook"><?php _e( 'Facebook' ); ?></a>
            <a href="<?php printf( '%s', 'http://twitter.com/rtPanel' ); ?>" target="_blank" title="<?php _e( 'Follow us on Twitter' ); ?>" class="twitter"><?php _e( 'Twitter' ); ?></a>
            <a href="<?php printf( '%s', 'http://feeds.feedburner.com/rtpanel' ); ?>" target="_blank" title="<?php _e( 'Subscribe to our feeds' ); ?>" class="rss"><?php _e( 'RSS Feed' ); ?></a>
        </div>
    </div>

    <div class="postbox" id="donations">
        <div title="<?php _e( 'Click to toggle' ); ?>" class="handlediv"><br /></div>
        <h3 class="hndle"><span><?php _e( 'Promote, Donate, Share' ); ?>...</span></h3>
        <div class="inside">
            <p><?php printf( __( 'Buy coffee/beer for team behind <a href="%s" title="rtPanel">rtPanel</a>.' ), 'http://rtpanel.com' ); ?></p>
            <div class="rt-paypal" style="text-align:center">
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                    <input type="hidden" name="cmd" value="_donations" />
                    <input type="hidden" name="business" value="paypal@rtcamp.com" />
                    <input type="hidden" name="lc" value="US" />
                    <input type="hidden" name="item_name" value="rtPanel / Anti-Spam Plugin" />
                    <input type="hidden" name="no_note" value="0" />
                    <input type="hidden" name="currency_code" value="USD" />
                    <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest" />
                    <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" />
                    <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
                </form>
            </div>
            <div class="rt-social-share" style="text-align:center; width: 127px; margin: 2px auto">
                <div class="rt-facebook" style="float:left; margin-right:5px;">
                    <a style=" text-align:center;" name="fb_share" type="box_count" share_url="http://rtpanel.com/"></a>
                </div>
                <div class="rt-twitter" style="">
                    <a href="<?php printf( '%s', 'http://twitter.com/share' ); ?>"  class="twitter-share-button" data-text="I &hearts; #rtPanel"  data-url="http://rtpanel.com" data-count="vertical" data-via="rtPanel"><?php _e( 'Tweet' ); ?></a>
                    <script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>

    <div class="postbox" id="support">
        <div title="<?php _e( 'Click to toggle'); ?>" class="handlediv"><br /></div>
        <h3 class="hndle"><span><?php _e( 'Free Support' ); ?></span></h3>
        <div class="inside"><p><?php printf( __( 'If you have any problems with this theme or good ideas for improvements, please talk about them in the <a href="%s" target="_blank" title="Click here for rtPanel Free Support">Support forums</a>' ), 'http://rtpanel.com/support' ); ?>.</p></div>
    </div>

    <div class="postbox" id="latest_news">
        <div title="<?php _e( 'Click to toggle'); ?>" class="handlediv"><br /></div>
        <h3 class="hndle"><span><?php _e( 'Latest News' ); ?></span></h3>
        <div class="inside"><?php rtas_get_feeds(); ?></div>
    </div><?php
}

/**
 * Display feeds from a specified Feed URL
 *
 * @param string $feed_url The Feed URL.
 *
 * @since AntiSpam 1.0
 */
function rtas_get_feeds( $feed_url='http://feeds.feedburner.com/rtpanel' ) {

    // Get RSS Feed(s)
    include_once( ABSPATH . WPINC . '/feed.php' );
    $maxitems = 0;
    // Get a SimplePie feed object from the specified feed source.
    $rss = fetch_feed( $feed_url );
    if ( !is_wp_error( $rss ) ) { // Checks that the object is created correctly

        // Figure out how many total items there are, but limit it to 5.
        $maxitems = $rss->get_item_quantity( 5 );

        // Build an array of all the items, starting with element 0 (first element).
        $rss_items = $rss->get_items( 0, $maxitems );
        
    } ?>
    <ul><?php
        if ( $maxitems == 0 ) {
            echo '<li>'.__( 'No items', 'rtPanel' ).'.</li>';
        } else {
            // Loop through each feed item and display each item as a hyperlink.
            foreach ( $rss_items as $item ) { ?>
                <li>
                    <a href='<?php echo $item->get_permalink(); ?>' title='<?php echo __( 'Posted ', 'rtPanel' ) . $item->get_date( 'j F Y | g:i a' ); ?>'><?php echo $item->get_title(); ?></a>
                </li><?php
            }
        } ?>
    </ul><?php
}
