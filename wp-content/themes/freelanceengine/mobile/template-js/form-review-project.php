<?php
global $post;
$status  = $post->post_status;
$review = isset($_GET['review']) ? $_GET['review'] : 0;
?>
<form role="form" id="review_form" class="review-form review-form-mobile" <?php if($review != 1) echo 'style ="display:none"';?> >             
    <div class="form-group rate">
        
        <label for="post_content"><?php _e('Rate for this profile',ET_DOMAIN);?> </label>
        <div class="rating-it" style="cursor: pointer;"> <input type="hidden" name="score" > </div>
    </div>
    <div class="clearfix"></div>
    <div class="form-group">
        
        <label for="post_content"><?php _e('Message review profile ',ET_DOMAIN); ?></label>
        <?php wp_editor( '', 'comment_content', ae_editor_settings() );  ?>
    </div>                  
    <input type="hidden" name="project_id" value="<?php the_ID(); ?>" />                    
    <?php if($status =='complete'){?>                       
        <input type="hidden" name="action" value="ae-freelancer-review" />
        <?php 
    } else { ?>                 
        <input type="hidden" name="action" value="ae-employer-review" />
    <?php } ?>

    <?php do_action('after_review_form'); ?>    
    <div class="clearfix"></div>
    <button type="submit" class="btn-submit btn btn-primary btn-sumary btn-sub-create">
        <?php _e('Submit', ET_DOMAIN) ?>
    </button>
</form> 