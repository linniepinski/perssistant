<div class="color-left">
    <div class="content-sub">
        <!--<h1><?php //fre_project_demonstration(true); ?></h1>-->
        <h1>The best way to<br>find a virtual <br /> assistant</h1>
        <p><a href="<?php echo et_get_page_link(array('page_type' => 'submit-project', 'post_title' => __("Post a Project", ET_DOMAIN))); ?>" class="btn-sumary btn-sub-post"><?php _e("Post a Project", ET_DOMAIN); ?></a></p>
    </div>
</div>
<div class="color-right">
    <div class="content-sub">
        <?php /* <h1><?php fre_profile_demonstration( true ); ?></h1> */ ?>
        <h1>Are you <br />looking<br />for work?</h1>
        <p>			
         <?php /*
          <a href="<?php echo et_get_page_link( array('page_type' => 'auth', 'post_title' => __("Create a Profile", ET_DOMAIN )) ); ?>" class="btn-sumary btn-sub-create">
          <?php _e("Create a Profile", ET_DOMAIN); ?>
          </a>			
        <?php */ ?>						
        <a href="<?php echo site_url(); ?>/projects/" class="btn-sumary btn-sub-create">                <?php _e("Find a Project", ET_DOMAIN); ?>            </a>
        </p>
    </div>
</div>
<div class="d-arrow"></div>
