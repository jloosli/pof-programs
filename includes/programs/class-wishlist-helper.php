<?php

/**
 * Get constants defined and check version
 */
class POM_Add_Access {
    private $debug_on = true,
        $debug_email = "jloosli@gmail.com",
        $slug = 'pom-addprogram';

    function __construct() {

        //Actions
        add_action( 'wp_ajax_pom_addprogram', array( &$this, "ajax" ) );

        //Filters

        //Short codes
        add_shortcode( "addprogram", array( &$this, "process_addprogram" ) );

        //Scripts

    }

    //Create shortcode for addprogram
    function process_addprogram( $atts ) {
        $this->add_scripts();
        extract( shortcode_atts( array(
            'text'       => "Add %s to \"My Programs\"",
            // Text to be displayed on button
            'after'      => "You're all set! Just click &ldquo;Go to My Programs&rdquo; at the top right of the page to access %s.",
            //text to be displayed after selection
            'already'    => "<strong>You're already subscribed to %s.</strong> Just click &ldquo;Go to My Programs&rdquo; at the top of the page to access it.",
            //text to be displayed if user already has access
            'program_id' => null,
            // Which program to add,
            'mail_list'  => ""
        ), $atts ) );
        if ( !is_user_logged_in() ) {
            return "<div class='need_login_message'><h3>In order to access your new program here at Power of Moms:</h3>
                (1) Login (or register as a new site member) at the top of the page<br />
                (2) Then click the button that will magically appear in place of this message.<br />
                (3) Finally, click \"Go to My Programs\" at the top right of the page where you logged in&hellip;and voila!&mdash;your program will appear!</div>";
        }
        $uAccess = new GF_UAM();
        if ( $program_id === null ) {
            return "<div class='warning'>No program was selected</div>";
        }
        if ( !array_key_exists( $program_id, $uAccess->getAccessGroups() ) ) {
            return "<div class='warning'>{Error!} Can't find program with the ID of $program_id.</div>";
        } else {
            $program = $uAccess->getProgramInfo( $program_id );
        }
        if ( in_array( $program_id, $uAccess->getCapabilities( get_current_user_id() ) ) ) {
            $output = "<div class='already_capable'>" . sprintf( $already, $program->name ) . "</div>";
        } else {
            $uid    = get_current_user_id();
            $nonce  = wp_create_nonce( "nonce" );
            $output = sprintf( "<div>
                <a class='add_program button' href='javascript:void(0);'
                data-program_id='$program_id'
                data-user='$uid'
                data-nonce='$nonce'
                data-mail_list='$mail_list'
                 >$text</a>
                </div>", $program->name );
        }

        return $output;
    }

    function ajax() {
        /* AJAX check  */
        if ( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
            $result = array( 'error' => "Something went wrong" );
            check_ajax_referer( "nonce" );
            if ( isset( $_POST['toDo'] ) ) {
                switch ( $_POST['toDo'] ) {
                    case "add" :
                        $program_id = $_POST['program_id']; // remove "id_" from the string;
                        $user       = $_POST['user'];
                        $uAccess    = new GF_UAM;
                        $uAccess->addCapability( $user, $program_id );
                        $result = array(
                            'success' => true,
                            'after'   => "You're all set. Just go to \"My Programs\" on the top right to access your program."
                        );
                        if ( $_POST['mail_list'] != '' ) {
                            require_once 'mailchimp/MCAPI.class.php';

                            $apikey     = 'fbeaaf45b67c1db423c120a6beb36341-us1';
                            $api        = new MCAPI( $apikey );
                            $user_info  = get_userdata( $_POST['user'] );
                            $merge_vars = array( 'FNAME' => $user_info->first_name, 'LNAME' => $user_info->last_name );

                            // By default this sends a confirmation email - you will not see new members
                            // until the link contained in it is clicked!
                            $retval = $api->listSubscribe( $mail_list, $$user_info->user_email, $merge_vars );

                            if ( $api->errorCode ) {
                                $err = '';
                                $err .= "Unable to load listSubscribe()!\n";
                                $err .= "\tCode=" . $api->errorCode . "\n";
                                $err .= "\tMsg=" . $api->errorMessage . "\n";
                                wp_mail( 'jloosli@gmail.com', 'Addprogram email subscribe error', $err );
                            }
                        }
                        break;
                }

            }
            if ( !isset( $_POST['nojson'] ) ) {
                $result = json_encode( $result );
            }
            die( $result );
        }

        return true;

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
        wp_enqueue_script( 'pom_addPrograms', plugin_dir_url( __FILE__ ) . 'js/addPrograms.js', array( 'jquery' ), "0.2", true );

        wp_localize_script( 'pom_affirmations', 'pom_aff',
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

