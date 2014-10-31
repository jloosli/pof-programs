<?php
class POM_Wishlist_Helper_Settings {
    /**
     * Build settings fields
     * @return array Fields to be displayed on settings page
     */
    public function getSettings () {

        $settings = array(
            'title'					=> __( 'Wishlist Helper', 'power-of-moms-programs' ),
            'description'			=> __( 'Settings for the wishlist helper. Each of these can be overriden in the actual shortcode.
            Shortcode looks like: <br>
            <pre>[addprograms program_id="1234" text="Click here to add %s" after="Thank you for subscribing to %s" already="You\'re already subscribed!"]</pre>
            Only &ldquo;program_id&rdquo; is required. Other defaults are set below.', 'power-of-moms-programs' ),
            'fields'				=> array(
                array(
                    'id' 			=> 'wishlist_helper_default_text',
                    'label'			=> __( 'Default Button Text' , 'power-of-moms-programs' ),
                    'description'	=> __( 'Default message to display. Use %s for the name of the program.', 'power-of-moms-programs' ),
                    'type'			=> 'textarea',
                    'default'		=> __("Add %s to &ldquo;My Programs&rdquo;", 'wordpress-plugin-template'),
                    'placeholder'	=> __( 'Default button text', 'wordpress-plugin-template' )
                ),
                array(
                    'id' 			=> 'wishlist_helper_default_after',
                    'label'			=> __( 'Default After Message' , 'power-of-moms-programs' ),
                    'description'	=> __( 'Text to be displayed after selection. Use %s for the name of the program.', 'power-of-moms-programs' ),
                    'type'			=> 'textarea',
                    'default'		=> __("You're all set! Just click &ldquo;Go to My Programs&rdquo; at the top right of the page to access %s.", 'wordpress-plugin-template'),
                    'placeholder'	=> __( "Default after press text", 'wordpress-plugin-template' )
                ),
                array(
                    'id' 			=> 'wishlist_helper_default_already',
                    'label'			=> __( 'Default Already Subscribed Message.' , 'power-of-moms-programs' ),
                    'description'	=> __( 'Text to be displayed after selection.  Use %s for the name of the program.', 'power-of-moms-programs' ),
                    'type'			=> 'textarea',
                    'default'		=> __("<strong>You're already subscribed to %s.</strong> Just click &ldquo;Go to My Programs&rdquo; at the top of the page to access it.", 'wordpress-plugin-template'),
                    'placeholder'	=> __( "Default already subscribed text", 'wordpress-plugin-template' )
                ),
                array(
                    'id' 			=> 'wishlist_helper_not_logged_in',
                    'label'			=> __( 'Not Logged In Message' , 'power-of-moms-programs' ),
                    'description'	=> __( 'Text to be displayed if the user isn&lsquo;t logged in.', 'power-of-moms-programs' ),
                    'type'			=> 'textarea',
                    'default'		=> __(
                        "<h3>In order to access your new program here at Power of Moms:</h3>
(1) Login (or register as a new site member) at the top of the page<br />
(2) Then click the button that will magically appear in place of this message.<br />
(3) Finally, click \"Go to My Programs\" at the top right of the page where you logged in&hellip;and voila!&mdash;your program will appear!", 'wordpress-plugin-template'),
                    'placeholder'	=> __( "Default not logged in text", 'wordpress-plugin-template' )
                )
            )
        );

        return $settings;
    }
}