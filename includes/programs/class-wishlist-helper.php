<?php

/**
 * Helper utilities for Wishlist Helper
 */
class POM_Wishlist_Helper {

    public static $settingsInstance;

    function __construct() {

        //Actions
        add_action( 'wp_ajax_pom_addprogram', array( &$this, "ajax" ) );

        //Filters

        //Short codes
        add_shortcode( "addprogram", array( &$this, "process_addprogram" ) );

        //Scripts

    }

    public static function getSettingsInstance() {
        if ( is_null( self::$settingsInstance ) ) {
            require_once( 'class-wishlist-helper-settings.php' );
            self::$settingsInstance = new POM_Wishlist_Helper_Settings();
        }

        return self::$settingsInstance;

    }

    //Create shortcode for addprogram
    function process_addprogram( $atts ) {
        $this->add_scripts();
        /** @var int $program_id */
        /** @var string $text */
        /** @var string $after */
        /** @var string $already */
        /** @var string $mail_list */
        extract( shortcode_atts( array(
            // Text to be displayed on button
            'text'       => get_option(
                'pom_wishlist_helper_default_text',
                'Add %s to &ldquo;My Programs&rdquo;'
            ),
            //text to be displayed after selection
            'after'      => get_option(
                'pom_wishlist_helper_default_after',
                "You're all set! Just click &ldquo;Go to My Programs&rdquo; at the top right of the page to access %s."
            ),
            //text to be displayed if user already has access
            'already'    => get_option(
                'pom_wishlist_helper_default_already',
                "<strong>You're already subscribed to %s.</strong> Just click &ldquo;Go to My Programs&rdquo; at the top of the page to access it."
            ),
            'program_id' => null,
            'mail_list'  => ''
        ), $atts ) );
        if ( !is_user_logged_in() ) {
            return "<div class='need_login_message'>" . get_option( 'pom_wishlist_helper_not_logged_in', 'You need to log in now.' ) . "</div>";
        }

        if ( $program_id === null ) {
            return "<div class='warning'>No program was selected</div>";
        }
//        $program_id = (int) $program_id;
        if ( !function_exists( 'wlmapi_get_level' ) ) {
            return "<div class='warning'>Wishlist member is not active! Please activate it.</div>";
        }
        $level = wlmapi_get_level( $program_id );
        if ( $level['success'] === 0 ) {
            return "<div class='warning'>{Error!} Can't find program with the ID of $program_id.</div>";
        }
        $user           = get_current_user_id();
        $user_is_member = wlmapi_is_user_a_member( $program_id, $user );
        if ( $user_is_member ) {
            $output = "<div class='already_capable'>" . sprintf( $already, $level['level']['name'] ) . "</div>";
        } else {
            $uid    = $user;
            $nonce  = wp_create_nonce( "nonce" );
            $output = sprintf( "<div>
                <a class='add_program button' href='javascript:void(0);'
                data-program_id='$program_id'
                data-user='$uid'
                data-nonce='$nonce'
                 >$text</a>
                </div>", $level['level']['name'] );
        }

        return $output;
    }

    function ajax() {
        $result = array( 'error' => "Something went wrong" );
        check_ajax_referer( "nonce", '_ajax_nonce', true );
        if ( isset( $_POST['toDo'] ) ) {
            switch ( $_POST['toDo'] ) {
                case "add" :

                    $program_id = $_POST['program_id'];
                    $user       = $_POST['user'];
                    $args       = array(
                        'Users' => array( $user )
                    );
                    $api_methods = new WLMAPIMethods();
                    $api = $api_methods->loadAPI();
                    $response = $api->post("/levels/$program_id/members", $args);
                    $members = unserialize($response);

                    if ( $members['success'] === 1 ) {
                        $result = array(
                            'success' => true,
                            'after'   => "You're all set. Just go to \"My Programs\" on the top right to access your program.",
                            'members_from_post' => $members
                        );
                    }
                    $response = $api->get("/levels/$program_id/members");
                    $members = unserialize($response);
                    $result['members_from_get'] = $members;
                    break;
            }

        }
        $result = json_encode( $result );

        die( $result );

    }


    /* ************
    Back end functions
     **************
    */
    function admin_addprogram() {
        if ( !current_user_can( 'edit_users' ) ) {
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }
        $nonce   = "addprogram";
        $updated = false;
        if ( isset( $_POST['message'] ) && wp_verify_nonce( $nonce ) ) {
            $this->setSettings( array(
                    "messages" =>
                        array( "default" => $_POST['message'] )
                )
            );
            $updated = true;
        }
        $messages = $this->getSettings();
        ?>
        <div class="wrap">
            <h2>Addprogram Settings</h2>
            <?php if ( $updated ) {
                echo "<div class='updated'>Message updated.</div>";
            } ?>
            <h3>Addprogram Default hidden message</h3>

            <form>
                <textarea rows="5" cols="200"
                          name="message"><?php echo isset( $messages['default'] ) ? $messages['default'] : ""; ?></textarea>
                <?php wp_nonce_field( $nonce ); ?>
                <input type="submit" value="Submit"/>
            </form>
        </div>
    <?php

    }

    function add_scripts() {
        // embed the javascript file that makes the AJAX request
        $script = plugins_url( '../../assets/js/addPrograms.js', __FILE__ );
        wp_enqueue_script( 'pom_addPrograms', $script, array( 'jquery' ), "0.1", true );

        wp_localize_script( 'pom_addPrograms', 'pom_add',
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' )
            ) );
    }


    /*
     * Determine if plugins are active
     */
    function plugin_is_active( $plugin_path ) {
        $return_var = in_array( $plugin_path, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );

        return $return_var;
    }

    function plugin_action_links( $links, $file ) {
        static $this_plugin;

        if ( !$this_plugin ) {
            $this_plugin = plugin_basename( __FILE__ );
        }

        if ( $file == $this_plugin ) {
            // The "page" query string value must be equal to the slug
            // of the Settings admin page we defined earlier
            $settings_link = '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=' . $this->slug . '">Settings</a>';
            array_unshift( $links, $settings_link );
        }

        return $links;
    }

}

