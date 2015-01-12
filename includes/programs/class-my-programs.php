<?php

/**
 * Helper utilities for Wishlist Helper
 */
class POM_My_Programs {

    public static $settingsInstance;

    function __construct($parent = '') {
//        $this->parent = $parent;
        require_once( __DIR__ . '/../../../wishlist-member/core/api-helper/class-api-methods.php' );
        $api_methods = new WLMAPIMethods();
        $this->api   = $api_methods->loadAPI();

        //Actions
        add_action('user_register', array(&$this, 'add_to_registered_users'), 10, 1);
        //Filters
        //Short codes
        add_shortcode( "pomprograms", array( &$this, "show_programs" ) );
        //Scripts

    }

    /**
     * Create shortcode for POM Programs
     */
    function show_programs( $atts ) {
        /** @var $showtitle bool
         * @var $title           string
         * @var $notloggedin     string
         * @var $nosubscriptions string
         */
        extract( shortcode_atts( array(
            'showtitle'       => "true",
            'title'           => 'My Programs',
            'notloggedin'     => "Sorry. You need to log in to view your programs.",
            'nosubscriptions' => "You haven't subscribed to any programs. Go check out some of <a href='/store'>our programs</a> and see what may be of use to you."
        ), $atts ) );
        $title  = $showtitle == "true" ? "<h2>$title</h2>" : "";
        $output = "<div id='pom_userprograms'>$title\n";
        $output .= "<style>#pom_userprograms div {float: left; width: 100%}</style>";
        if ( is_user_logged_in() ) {
            $progs = $this->getCurrentUserPrograms( get_current_user_id() );
            if ( $progs ) {
                foreach ( $progs as $prog ) {
                    $image = '';
                    if(!empty($prog['image'])) {
                        $image = sprintf("<img class='alignleft' src='%s' width='88' height='88' />", $prog['image']);
                    }
                    $output .= sprintf( "<div><a href='%s'>%s%s</a></div>", $prog['link'], $image, $prog['name'] );
                }
                $output .= "</div>";
            } else {
                $output .= "<div class='message'>$nosubscriptions</div>";
            }
        } else {
            $output .= "<div class='message'>$notloggedin</div>";
        }
        $output .= "</div>";

        return $output;
    }

    private function getCurrentUserPrograms( $user_id ) {
        $the_programs = [];
        if(function_exists('wlmapi_get_member_levels')) {
            $programs = wlmapi_get_member_levels( $user_id );

            $the_programs = array_map( function ( $program ) {
                $theLevel = wlmapi_get_level( $program->Level_ID );
                $theLink  = '';
                $theImage = '';
                if ( is_numeric( $theLevel['level']['after_registration_redirect'] ) ) {
                    $theLink = get_page_link( $theLevel['level']['after_registration_redirect'] );
                    $theImage = wp_get_attachment_thumb_url(get_post_thumbnail_id($theLevel['level']['after_registration_redirect']));
                };

                return array(
                    'name' => $program->Name,
                    'link' => $theLink,
                    'image'=> $theImage
                );
            }, $programs );
        }

        return $the_programs;
    }

    /*
     * Add user to "Registered Users" level once they're registered.
     */
    public function add_to_registered_users($user_id) {
        $level_id = 1415908207; // Registered Users
        $this->api->return_format='php';

        $data = array(
            'Users' => array($user_id)
        );
        $response = $this->api->post("/levels/{$level_id}/members", $data);
    }
}

