<?php

/**
 * Helper utilities for Wishlist Helper
 */
class POM_My_Programs {

    public static $settingsInstance;

    function __construct() {

        //Actions
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
        $output = "<div id='pom_userprograms'>$title";
        if ( is_user_logged_in() ) {
            $progs = $this->getCurrentUserPrograms( get_current_user_id() );
            if ( $progs ) {
                $output .= "<dl>";
                foreach ( $progs as $prog ) {
                    $output .= "<dt>";
                    $output .= sprintf( "<dt><a href='%s'>%s</a></dt>", $prog['link'], $prog['name'] );
                }
                $output .= "</dl>";
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
        if(user_can($user_id, 'manage_options')){
            $programs = wlmapi_get_levels();
        } else {
            $programs     = wlmapi_get_member_levels( $user_id );
        }
        $the_programs = array_map( function ( $program ) {
            $theLevel = wlmapi_get_level( $program->Level_ID );
            $theLink  = '';
            if ( is_int( $theLevel['level']['after_registration_redirect'] ) ) {
                $theLink   = get_page_link($theLevel['level']['after_registration_redirect']);
            };

            return array(
                'name' => $program->Name,
                'link' => $theLink
            );
        }, $programs );

        return $the_programs;
    }
}

