<?php
/**
 * AE overview
 * show all post, payment, order status on site
 * @package AE
 * @version 1.0
 * @author Dakachi
 */
class AE_Wizard extends AE_Page
{
    
    public function __construct() {
        if (isset($_REQUEST['page']) && $_REQUEST['page'] == 'et-wizard') {
            $this->add_action('admin_enqueue_scripts', 'wizard_scripts');
        }
        $this->add_action('admin_notices', 'notice_after_installing_theme');  
        $this->add_ajax('et-insert-sample-data', 'insert_sample_data' , true , false);
        $this->add_ajax('et-delete-sample-data', 'delete_sample_data',true,false);        
    }
    // show url to wizard after active theme
    public function notice_after_installing_theme(){
        $wizard_status = get_option('option_sample_data', 0);
        if ( isset($wizard_status ) && !$wizard_status ){
        ?>
            <style type="text/css">
            .et-updated{
                background-color: lightYellow;
                border: 1px solid #E6DB55;
                border-radius: 3px;
                webkit-border-radius: 3px;
                moz-border-radius: 3px;
                margin: 20px 15px 0 0;
                padding: 0 10px;
            }
            </style>
            <div id="notice_wizard" class="et-updated">
                <p>
                    <?php printf( __("You have just installed DirectoryEngine theme, we recommend you follow through our <a href='%s'>setup wizard</a> to set up the basic configuration for your website! <a href='%s'>Close this message</a>", ET_DOMAIN), admin_url('admin.php?page=et-wizard'), add_query_arg('close_notices', '1'))?>
                </p>
            </div>
        <?php
        }
    }  
    public function insert_sample_data(){   
        
        $response = array('success' => false, 'updated_op' => get_option('option_sample_data'));

        if ( !$response['updated_op'] ) {
            update_option( 'option_sample_data', 1);         

            $import_xml = new DE_Import_XML();
            $import_xml->dispatch();
            //do_action( 'de_setup_default_theme' );

            $response = array(
                'success'    => true, 
                'redirect'   => admin_url('admin.php?page=revslider'), 
                'msg'        => __("Import Data Successfully.",ET_DOMAIN), 
                'updated_op' => true
            );

            do_action('ae_insert_sample_data_success');
        }
        
        wp_send_json($response);        
    }

    public function delete_sample_data(){
            
        $response = array('success' => false, 'updated_op' => get_option('option_sample_data'));
        if ( $response['updated_op'] ) {
            delete_option( 'option_sample_data');           

            $import_xml = new DE_Import_XML();          
            $import_xml->depatch();
            $response = array(
                'success'    => true, 
                'msg'        => __("Delete Data Successfully.",ET_DOMAIN), 
                'updated_op' => false
            );
        }       
        wp_send_json($response);
        
    }    
    function wizard_scripts(){
        $this->add_script('ae-wizard', ae_get_url() . '/assets/js/wizard.js', array(
                    'jquery',
                    'appengine'
                ));
        wp_localize_script( 
            'ae-wizard', 
            'ae_wizard', 
            array(
                'insert_sample_data' => __("Insert sample data", ET_DOMAIN),
                'delete_sample_data' => __("Delete sample data", ET_DOMAIN),
                'insert_fail'        => __('Insert sample data false',ET_DOMAIN),
                'delete_fail'        => __('Delete sample data false',ET_DOMAIN),
                'wr_uploading'       => __("It would take up to a few minutes for your images to be uploaded to the server. Please don't close or reload this page.")
                )
            );        
    }
    /**
     * render container element
     */
    function render() {
    ?>	
        <div class="et-main-content" id="overview_settings">
            <div class="et-main-right">
                <div class="et-main-main clearfix inner-content" id="wizard-sample">
                    
                    <div class="title font-quicksand" style="padding-top:0;">
                        <h3><?php _e('SAMPLE DATA',ET_DOMAIN) ?></h3>

                        <div class="desc small"><?php _e('The sample data include some items from the list below: places, comments, etc.',ET_DOMAIN) ?></div>

                        <div class="btn-language padding-top10 f-left-all" style="padding-bottom:15px;height:65px;margin:0;">
                        <?php  
                            $sample_data_op = get_option('option_sample_data');
                            if (!$sample_data_op) {
                                echo '<button class="primary-button" id="install_sample_data">'.__("Install sample data", ET_DOMAIN).'</button>';
                            }
                            else{
                                echo '<button class="primary-button" id="delete_sample_data">'.__("Delete sample data", ET_DOMAIN).'</button>';
                            }
                        ?>
                        </div>      
                    </div>

                    <div class="desc" style="padding-top:0px;">
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit.php?post_type=place' ); ?>" ><?php _e('Places',ET_DOMAIN) ?></a> <span class="description"><?php _e('Add new place types or modify the sample ones to suit your site style.',ET_DOMAIN) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit-tags.php?taxonomy=place_category&post_type=place' ); ?>" ><?php _e('Place Categories',ET_DOMAIN) ?></a> <span class="description"><?php _e('Add new categories or modify the sample data to match your directory business.',ET_DOMAIN) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit-tags.php?taxonomy=location&post_type=place' ); ?>" ><?php _e('Place Locations',ET_DOMAIN) ?></a> <span class="description"><?php _e('Add new locations or modify the sample data to match your directory business.',ET_DOMAIN) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit.php?post_type=page' ); ?>" ><?php _e('Pages',ET_DOMAIN) ?></a> <span class="description"><?php _e('Modify the sample "About us, Contact us, ..." pages or add your extra pages when needed.',ET_DOMAIN) ?></span>
                        </div>
                        <div class="title font-quicksand sample-title">
                            <a target="_blank" href="<?php echo admin_url( 'edit.php' ); ?>" ><?php _e('Posts',ET_DOMAIN) ?></a> <span class="description"><?php _e('A couple of news & event posts have been added for your review. You can delete it or add your own posts here.',ET_DOMAIN) ?></span>
                        </div>
                    </div>
                </div>

                <div class="et-main-main clearfix inner-content <?php if (!$sample_data_op) echo 'hide'; ?>" id="overview-listplaces">

                    <div class="title font-quicksand" style="padding-bottom:60px;">
                        <h3><?php _e('MORE SETTINGS',ET_DOMAIN) ?></h3>
                        <div class="desc small"><?php _e('Enhance your site by customizing these other features',ET_DOMAIN) ?></div>
                    </div>

                    <div style="clear:both;"></div>

                    <div class="title font-quicksand  sample-title">
                        <a target="_blank"  href="admin.php?page=et-settings" ><?php _e('General Settings',ET_DOMAIN) ?></a> <span class="description"><?php _e('Modify your site information, social links, analytics script, or add a language, etc.',ET_DOMAIN) ?></span>
                    </div>

                    <div class="title font-quicksand sample-title">
                        <a target="_blank"  href="edit.php?post_type=page" ><?php _e('Front Page',ET_DOMAIN) ?></a> <span class="description"><?php _e('Rearrange content elements or add more information in your front page to suit your needs.',ET_DOMAIN) ?></span>
                    </div>

                    <div class="title font-quicksand sample-title">
                        <a target="_blank" href="nav-menus.php" ><?php _e('Menus',ET_DOMAIN) ?></a> <span class="description"><?php _e('Edit all available menus in your site here.',ET_DOMAIN) ?></span>
                    </div>

                    <div class="title font-quicksand sample-title">
                        <a href="widgets.php" target="_blank"><?php _e('Sidebars & Widgets',ET_DOMAIN) ?></a> <span class="description"><?php _e('Add or remove widgets in sidebars throughout the site to best suit your need.',ET_DOMAIN) ?></span>
                    </div>

                </div>
            </div>
        </div>
        <style type="text/css">
        .hide{display: none;}.et-main-left .title, .et-main-main .title {text-transform: none;}.et-main-main{margin-left:0;}.title.font-quicksand h3{margin-bottom:0;margin-top:0;}.desc.small,span.description{font-family:Arial, sans-serif!important;font-weight:400;font-size:16px!important;color:#9d9d9d;font-style:normal; margin-top:10px; }span.description{margin-left:30px;}.sample-title{color:#427bab!important;padding-left:20px!important;font-size:18px!important;}.title.font-quicksand{padding-top:15px;}a.primary-button{right:50px;position:absolute;text-decoration:none;color:#ff9b78;}.et-main-main .title{padding-left:20px;}.sample-title a{text-decoration: none;}
        </style>
    <?php
    }
}

if(class_exists('ET_Import_XML')) {
    class DE_Import_XML extends ET_Import_XML {
        function __construct () {
            $this->file = TEMPLATEPATH . '/sampledata/sample_data.xml';
        }
    }
}