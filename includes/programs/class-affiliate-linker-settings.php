<?php

class POM_Affiliate_Linker_Settings {

    public function __construct() {
        add_action( 'pom_programs_settings_admin_end', array( $this, 'add_script' ) );
    }

    public function add_script() {
        $javascript = <<<JS
function pom_runAffiliateScript(me) {
    var message = document.createElement('span');
    me.parentNode.replaceChild(message,me);
    message.innerHTML = 'Working...';
    message.onclick = '';
    jQuery.post(ajaxurl, {action: 'pom_affiliates_run'}, function (response) {
        console.log(response);
        message.innerHTML = response.message;
    });
    return false;
}

JS;
        $javascript = preg_replace( "/[\n\t]/", "", $javascript );
        printf( "<script>%s</script>", $javascript );
    }

    /**
     * Build settings fields
     * @return array Fields to be displayed on settings page
     */
    public function getSettings() {


        $settings = array(
            'title'       => __( 'Amazon Affiliate Linker', 'power-of-moms-programs' ),
            'description' => __( 'Settings to add amazon affiliate linking', 'power-of-moms-programs' ),
            'fields'      => array(
                array(
                    'id'          => 'amazon_affiliate_id',
                    'label'       => __( 'Amazon Affiliate ID', 'power-of-moms-programs' ),
                    'description' => __( 'Enter the Amazon Affiliate ID (e.g. thpoofmo0fb-20)', 'power-of-moms-programs' ),
                    'type'        => 'text',
                    'placeholder' => __( 'Amazon Affiliate ID', 'wordpress-plugin-template' )
                ),
                array(
                    'id'    => 'pom_amazon_affiliate_run_now',
                    'label' => sprintf( '<a class="button" onclick="%s">%s</a>', 'pom_runAffiliateScript(this);', __( 'Run amazon affiliate script now', 'power-of-moms-programs' ) ),
                )
            )
        );

        return $settings;
    }
}