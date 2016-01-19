<?php
/**
 * The template for displaying project description, comment, taxonomy and custom fields
 * @since 1.0
 * @package FreelanceEngine
 * @category Template
 */
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object    = $ae_post_factory->get(PROJECT);
$convert = $project = $post_object->current_post;
?>
<div class="info-project-item-details">
    <div class="row">
        <div class="col-md-8">
            <div class="content-require-project">
                <h4><?php _e('Project description:',ET_DOMAIN);?></h4>
                <?php the_content(); ?>
            </div>
            <?php if(!ae_get_option('disable_project_comment')) { ?>
            <div class="comments" id="project_comment">
                <?php comments_template('/comments.php', true)?> 
            </div>
            <?php } ?>

        </div>
        <div class="col-md-4">
            <div class="content-require-skill-project">                                           
            <?php 
                
                do_action('before_sidebar_single_project', $project);

                list_tax_of_project( get_the_ID(), __('Skills required:',ET_DOMAIN), 'skill' );                                                                                     
                list_tax_of_project( get_the_ID(), __('Category:',ET_DOMAIN)  );

                // list project attachment
                $attachment = get_children( array(
                        'numberposts' => -1,
                        'order' => 'ASC',
                        'post_parent' => $post->ID,
                        'post_type' => 'attachment'
                      ), OBJECT );
                if(!empty($attachment)) {
                    echo '<h3 class="title-content">'. __("Attachment:", ET_DOMAIN) .'</h3>';
                    echo '<ul class="list-file-attack-report">';
                    foreach ($attachment as $key => $att) {
                        $file_type = wp_check_filetype($att->post_title, array('jpg' => 'image/jpeg',
                                                                                'jpeg' => 'image/jpeg',
                                                                                'gif' => 'image/gif',
                                                                                'png' => 'image/png',
                                                                                'bmp' => 'image/bmp'
                                                                            )
                                                    );
                        $class="text-ellipsis";
                        
                        if(isset($file_type['ext']) && $file_type['ext']) $class="image-gallery text-ellipsis";
                        echo '<li>
                                <a class="'.$class.'" target="_blank" href="'.$att->guid.'"><i class="fa fa-paperclip"></i>'.$att->post_title.'</a>
                            </li>';
                    }
                    echo '</ul>';
                }
                
                if(function_exists('et_render_custom_field')) {
                    et_render_custom_field($project);
                }

                do_action('after_sidebar_single_project', $project);
            ?>
            </div>
            
        </div>
    </div>
</div>