<?php
global $user_ID, $post;

?>
<form id="bid_form" class="bid-form bid-form-mobile" <?php if(!isset($_GET['bid'])) echo 'style="display:none"';?> >
               
    <?php
    if(!is_user_logged_in()){ ?>
    <p><?php printf(__('You must <a href="%s">login</a> to bid on this project','form-bid-project-mobile'), et_get_page_link( array('page_type' => 'auth', 'post_title' => __("Login page", 'form-bid-project-mobile' ) ) ).'?redirect='.get_permalink() ); ?> </p>
    <?php } else{?>
    <div class="form-group">
        <h3 class="title-content"> <?php _e('Set your bid:','form-bid-project-mobile'); ?> </h3>
    </div>

    <div class="form-group">
        <label for="bid_budget">
            <?php
            if (get_post_meta($post->ID, 'type_budget', true) == 'hourly_rate') {
                _e('Hourly rate', 'form-bid-project-mobile');
            } else {
                _e('Budget', 'form-bid-project-mobile');
            }
            ?></label>
        <div class="checkbox" style="display: inline-block;margin-left: 20px;">
            <label><input type="checkbox" name="decide_later" class="checkbox1">Decide
                later</label>
        </div>
        <script>
            jQuery(document).ready(function () {
                jQuery('.checkbox1').change(function () {
                    if (jQuery(this).is(':checked')) {
                        jQuery('#bid_budget').removeClass('required').removeAttr('value').parent().hide();
                    }
                    else {
                        jQuery('#bid_budget').addClass('required').removeAttr('value').parent().show();
                    }

                });

            });

        </script>
        <div class="form-group">
        <input type="number" name="bid_budget" id="bid_budget" class="form-control required number" />
            </div>
    </div>

    <div class="clearfix"></div>

    <div class="form-group">
        <label for="bid_time"><?php _e('Deadline','form-bid-project-mobile');?></label>
        <div class="row">
            <div class="col-xs-12">
                <div class="row ">
                    <div class="bid-time-group-addon">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-xs-6">
                                    <label class="sr-only" for="bid_time">Bid time</label>
                                    <input type="number" name="bid_time" id="bid_time" min="1"
                                           class="form-control required number"
                                           placeholder="number"/>
                                </div>
                                <div class="col-xs-6">
                                    <div class="input-group">
                                        <span class="input-group-addon">in</span>
                                        <label class="sr-only" for="type_time">Type time</label>
                                        <select name="type_time" class="form-control required">
                                            <option value="day">days</option>
                                            <option value="week">weeks</option>
                                            <option value="month">months</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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