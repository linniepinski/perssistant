<form role="form" id="bid_form" class="bid-form bid-form-mobile" <?php if(!isset($_GET['bid'])) echo 'style="display:none"';?> >
               
    <?php
    if(!is_user_logged_in()){ ?>
    <p><?php printf(__('You must <a href="%s">login</a> to bid on this project',ET_DOMAIN), et_get_page_link( array('page_type' => 'auth', 'post_title' => __("Login page", ET_DOMAIN ) ) ).'?redirect='.get_permalink() ); ?> </p>
    <?php } else{?>
    <div class="form-group">
        <h3 class="title-content"> <?php _e('Set your bid:',ET_DOMAIN); ?> </h3>
    </div>

    <div class="form-group">
        <label for="bid_budget"><?php _e('Budget',ET_DOMAIN);?></label>
        <input type="number" name="bid_budget" id="bid_budget" class="form-control required number" />
    </div>

    <div class="clearfix"></div>

    <div class="form-group">
        <label for="bid_time"><?php _e('Deadline',ET_DOMAIN);?></label>
        <input type="number" name="bid_time" id="bid_time" class="form-control required number" />      
        <div class="clearfix" style="height:12px;"></div>                
        <select name="type_time">                           
            <option value="day"><?php _e('days',ET_DOMAIN);?></option>
            <option value="week"><?php _e('week',ET_DOMAIN);?></option>
        </select>
    </div>

    <div class="clearfix"></div>

    <div class="form-group">
        <label for="post_content"><?php _e('Notes',ET_DOMAIN); ?></label>
        <textarea id="bid_content" name="bid_content"></textarea>
    </div>      

    <div class="clearfix"></div>

    <input type="hidden" name="post_parent" value="<?php the_ID(); ?>" />
    <input type="hidden" name="method" value="create" />
    <input type="hidden" name="action" value="ae-sync-bid" />

    <?php do_action('after_bid_form'); ?>   

    <button type="submit" class="btn btn-primary btn-submit btn-sub-create">
        <?php _e('Submit', ET_DOMAIN) ?>
    </button>
    <?php } ?>
</form> 