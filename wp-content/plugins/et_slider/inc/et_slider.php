<?php

class ET_SLider {
    const POST_TYPE         = 'et_slider';
    const META_LINK         = 'et_slider_link';
    const META_REQUIRED     = 'et_slider_required';

    public function __construct(){
      add_action('init', array($this,'on_init'));
    }

    public function on_init(){
      
        register_post_type( 'et_slider', array(
            'labels' => array(
                'name'          => 'ET Slider',
                'singular_name' => 'ET Slider',
                'plural_name'   => 'ET Slider',
                'add_new'       => 'Add new',
                'add_new_item'  => 'Add new slider',
                'edit_item'     => 'Edit slider'
                ),
            'public'            => true,
            'publicly_queryable' => false,
            'show_ui'           => false, 
            'show_in_menu'      => false, 
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'et_slider' ),
            'capability_type'   => 'post',
            'has_archive'       => true, 
            'hierarchical'      => false,
            'menu_position'     => null,
            'supports'          => array( 'title', 'editor', 'author' )
        ) );   
  
    }   
    static function et_insert_slider($args){

        $post_id = wp_insert_post($args);

        if ( is_wp_error($post_id) ) 
            return $post_id;
        if($post_id){
            update_post_meta($post_id,'et_link',$args['et_link']);
            update_post_meta($post_id,'_thumbnail_id',$args['attach_id']);
          
        }
        return $post_id;
    }  

    static public function update_slider($id, $args){
        $params = array(
            'ID'            => $args['ID'],
            'post_title'    => $args['title'],
            'post_content'  => $args['content']
            );

        $result = wp_update_post($params);     

       

        if ( isset($args['et_link']) && !empty( $args['et_link'])  )
            update_post_meta( $result, self::META_LINK, $args['et_link'] );

        return $result;
    }
    public static function add_view($args){

        $error_wr = 'Current slider does not exists';
        ?>
            <div class="et-main-header">
                <div class="title font-quicksand"><?php echo $args->menu_title ?></div>               
            </div>
            <div class="et-main-main et-main-full no-menu clearfix inner-content" id="job-fields-add">
                <div class="title font-quicksand">
                    <a href="<?php echo remove_query_arg( array('action','id') ) ?>" class="back-link"> <span class="icon" data-icon="["></span>&nbsp;&nbsp;<?php _e("Back to  Sliders' List", ET_DOMAIN)?></a>
                    <?php if (isset($_GET['id'])) {
                        //_e('List thumbnail of %s slider ', get_the_title($_GET['id']));
                        printf(__("%s", ET_DOMAIN), get_the_title($_GET['id']) );
                    } 
                    ?>
                </div> 
                
            <?php 
                $slider_id = isset($_GET['id']) ? $_GET['id'] : '';
                if(empty($slider_id) || !is_numeric($_GET['id'])){                         
                    echo'<div class="desc">';
                    _e($error_wr,'ET_DOMAIN');
                    echo'</div>';
                    return ;
                }
                else if( ($slider_id instanceof WP_Error)){
                    echo'<div class="desc">';
                    _e($error_wr,'ET_DOMAIN');
                    echo'</div>';
                    return;
                }

                if (isset($_GET['id']) ){              
                    $post = get_post($_GET['id']);
                    if(empty($post)){
                         echo'<div class="desc">';
                        _e($error_wr,'ET_DOMAIN');
                        echo'</div>';
                        return;
                    } else if ( $post->post_parent != 0 || $post->post_type != 'et_slider') {
                            echo'<div class="desc">';
                            _e($error_wr,'ET_DOMAIN');
                            echo'</div>';
                            return ;
                    }                  
                }             
                    if(isset($_GET['action'])){?>  
                        <div class="desc">
                            <script type="application/json" id="list_slide_data">
                            <?php  $slides = et_refresh_slider(); echo json_encode( array_map('et_create_slider_response', array_values($slides)) ) ?>
                            </script>                                             
                            <?php 
                            if ( isset($_GET['id']) )
                                echo '<input type="hidden" name="ID" value="' . $_GET['id']. '">';

                            $args = array(
                               'post_type'      => 'et_slider',
                               'numberposts'    => -1,
                               'post_status'    => 'publish',
                               'orderby'        => 'menu_order date',
                               'order'          => 'DESC',
                               'post_parent'    => $_GET['id']
                              );
                            $attachments = get_posts( $args );
                            echo'<ul class="list-thumbnail sortable ui-sortable">';
                            if(count($attachments) >0)   {
                                if ( $attachments ) {


                                    foreach ( $attachments as $attachment ) {
                                        echo '<li class="item" data="'.$attachment->ID.'" id="slide_'.$attachment->ID.'">';
                                            echo '<div class="sort-handle"></div>';
                                            //echo wp_get_attachment_image( $attachment->ID, 'thumbnail' );
                                            echo $attachment->post_title;
                                                echo '<div class="actions">
                                                    <a data-icon="p" rel="'.$attachment->ID.'" class="icon act-edit" title="Edit" href="#"> </a>
                                                    <a data-icon="D" rel="'.$attachment->ID.'" class="icon act-del" title="Delete" href="#"></a>
                                                    </div>';
                                        echo '</li>';
                                      }

                                }
                            }
                            echo '</ul>';
                            ?>

                          <form action="#" method="post" id="fadd" class="et-form" data = "<?php echo $_GET['id'];?>"></form>
                            <div class="item">
                                <form class="engine-payment-form" action="#" id="et_slide_form" enctype="multipart/form-data">
                                    <input type="hidden" value = "<?php echo $_GET['id'];?>" name="slider_id" /> 
                                    <input type="hidden" name="et_ajaxnonce" value="<?php echo  wp_create_nonce( 'add_slider' ) ?>">
                                    <div class="form payment-plan">
                                        <div class="form-item">
                                         <div class="title font-quicksand"> Add a Slide</div>
                                         <div class="clearfix"></div>
                                        </div>
                                        <div class="form-item">
                                            <div class="label">Title</div>
                                            <input type="text" name="title" class="bg-grey-input not-empty" >
                                        </div>

                                        <div class="form-item">
                                            <div class="label"><?php _e('Slide text',ET_DOMAIN); ?></div>
                                            <?php  wp_editor('','description', je_editor_settings() ); ?>

                                        </div>
                                        <div class="form-item">
                                            <div class="label">Link</div>
                                            <input type="text" name="et_link" class="bg-grey-input not-empty" id="et_link" value="http://">
                                        </div>
                                        <div class="form-item">
                                            <div class="label"><?php _e('Read more text',ET_DOMAIN);?></div>
                                            <input type="text" name="read_more" class="bg-grey-input not-empty" >
                                        </div>

                                        <div id="slider_thumb_container" class="form-item">
                                            <div class="label">
                                                   <?php _e('Upload slide image',ET_DOMAIN); ?>    
                                            </div>
                                            <div class="container-thumb">
                                                <span id="slider_thumb_thumbnail" class="company-thumbs">
                                                </span>
                                            </div>
                                            <div class="input-file clearfix et_uploader">
                                                <span tabindex="8" id="slider_thumb_browse_button" class="btn-background border-radius button" style="z-index: 0;">
                                                    Browse...   <span data-icon="o" class="icon"></span>
                                                </span>

                                                <input type="text" class="not-empty" name="attach_id" id="attach_id"  />
                                                <div class="clearfix"></div>

                                                <span class="et_ajaxnonce" id="<?php echo wp_create_nonce('slider_thumb_et_uploader'); ?>"></span>
                                                <div class="filelist"></div>

                                            </div>

                                            <div id="p1859iirsr1g9k68qeemhl511hm0_html5_container" style="position: absolute; background: none repeat scroll 0% 0% transparent; width: 0px; height: 0px; overflow: hidden; z-index: -1; opacity: 0; top: 0px; left: 0px;" class="plupload html5"><input type="file" accept="image/jpeg,image/gif,image/png" style="font-size: 999px; position: absolute; width: 100%; height: 100%;" id="p1859iirsr1g9k68qeemhl511hm0_html5"></div>
                                        </div>
                                        <div class="submit">
                                            <button class="btn-button engine-submit-btn">
                                                <span>Save</span><span data-icon="+" class="icon"></span>
                                            </button>
                                        </div>
                                        <div class="hidden">
                                        <input type="reset" name="reset" id="reset">
                                        </div>
                                    </div>
                                </form>
                            </div> <!-- End ietm !-->
                    <?php
                    }
                    ?>
                </div>
            </div>
            <script type="text/template" id="template_edit_form">
                    <form action="" class="edit-plan engine-payment-form edit-attachment" autocomplete="off">
                        <input type="hidden" name="action" value="et_sync_resume_plan">
                        <input type="hidden" name="post_id" value="{{ id }}">

                        <div class="form payment-plan">
                            <div class="form-item">
                                <div class="label"><?php _e("Title",ET_DOMAIN);?></div>
                                <input class="bg-grey-input not-empty" name="title" type="text" value="{{ title }}" />
                            </div>

                            <div class="form-item">
                                <div class="label"><?php _e("Slide text",ET_DOMAIN);?></div>

                                <textarea class="bg-grey et_description" id = "et_description_{{ id }}" name="description" autocomplete="off">{{ description}}</textarea>
                            </div>
                            <div class="form-item">
                                <div class="label"><?php _e("Link ",ET_DOMAIN);?></div>
                                <input class="bg-grey-input " name="et_link" type="text" value="{{ et_link }}"  />
                            </div>
                            <div class="form-item">
                                <div class="label"><?php _e("Read more text ",ET_DOMAIN);?></div>
                                <input class="bg-grey-input " name="read_more" type="text" value="{{ read_more }}"  />
                            </div>
                            <div class="form-item" id="et_slider_container">
                            <div class="label">
                                Slide image
                            </div>
                            <div class="container-thumb">
                                <span class="company-thumbs" id="et_slider_thumbnail">
                                    <img src="{{ attach_url }}" id="et_slider_thumb" data="0">
                                </span>
                            </div>
                            <div class="input-file clearfix et_uploader">
                                <span style="z-index: 0;" class="btn-background border-radius button" id="et_slider_browse_button" tabindex="8">
                                    Browse...   <span class="icon" data-icon="o"></span>
                                </span>
                                <span class="et_ajaxnonce" id="<?php echo wp_create_nonce('slider_thumb_et_uploader'); ?>"></span>
                                <input type="hidden"  id="attach_id" name="attach_id" class="abc">
                                <div class="clearfix"></div>
                                <div class="filelist"></div>
                            </div>

                            <div class="submit">
                                <button id="save_resume_playment_plan" class="btn-button engine-submit-btn">
                                    <span><?php _e("Save ",ET_DOMAIN);?></span><span class="icon" data-icon="+"></span>
                                </button>
                                or <a href="#" class="cancel-edit"><?php _e("Cancel", ET_DOMAIN); ?></a>
                            </div>
                        </div>
                    </form>
                </script>
            <?php
        }


}