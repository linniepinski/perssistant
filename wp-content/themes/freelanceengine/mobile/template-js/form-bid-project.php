<form id="bid_form" class="bid-form bid-form-mobile" <?php if(!isset($_GET['bid'])) echo 'style="display:none"';?> >
               
    <?php
    if(!is_user_logged_in()){ ?>
    <p><?php printf(__('You must <a href="%s">login</a> to bid on this project','form-bid-project-mobile'), et_get_page_link( array('page_type' => 'auth', 'post_title' => __("Login page", 'form-bid-project-mobile' ) ) ).'?redirect='.get_permalink() ); ?> </p>
    <?php } else{?>
    <div class="form-group">
        <h3 class="title-content"> <?php _e('Set your bid:','form-bid-project-mobile'); ?> </h3>
    </div>

    <div class="form-group">
        <label for="bid_budget"><?php _e('Budget','form-bid-project-mobile');?></label>
        <input type="number" name="bid_budget" id="bid_budget" class="form-control required number" />
    </div>

    <div class="clearfix"></div>

    <div class="form-group">
        <label for="bid_time"><?php _e('Deadline','form-bid-project-mobile');?></label>
        <input type="number" name="bid_time" id="bid_time" class="form-control required number" />      
        <div class="clearfix" style="height:12px;"></div>                
        <select name="type_time">                           
            <option value="day"><?php _e('days','form-bid-project-mobile');?></option>
            <option value="week"><?php _e('week','form-bid-project-mobile');?></option>
        </select>
    </div>

    <div class="clearfix"></div>

    <div class="form-group">
        <label for="post_content"><?php _e('Notes','form-bid-project-mobile'); ?></label>
        <textarea id="bid_content" name="bid_content"></textarea>
    </div>      

    <div class="clearfix"></div>

    <input type="hidden" name="post_parent" value="<?php the_ID(); ?>" />
    <input type="hidden" name="method" value="create" />
    <input type="hidden" name="action" value="ae-sync-bid" />

    <?php do_action('after_bid_form'); ?>   

    <button type="submit" class="btn btn-primary btn-submit btn-sub-create">
        <?php _e('Submit', 'form-bid-project-mobile') ?>
    </button>
    <?php } ?>
</form> 