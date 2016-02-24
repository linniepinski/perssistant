<div class="color-left">
<div class="content-sub">
    <?php _e("<h1>The best way to<br>find a professional</h1>", 'header-employer'); ?>
    <p><a href="<?php echo et_get_page_link(array('page_type' => 'submit-project', 'post_title' => __("Post a Project", 'header-employer'))); ?>"
                class="btn-sumary btn-sub-post"><?php _e("Post a Project", 'header-employer'); ?></a></p></div>
</div>
<div class="color-right">
    <div class="content-sub">
        <?php _e("<h1>Need a job?<br>Tell us your story</h1>", 'header-employer'); ?>
        <p><a href="<?php echo get_post_type_archive_link(PROFILE) ?>"
                class="btn-sumary btn-sub-create"><?php _e("Find a Virtual Assistant", 'header-employer'); ?></a>
        </p></div>
</div>
<div class="d-arrow"></div>

