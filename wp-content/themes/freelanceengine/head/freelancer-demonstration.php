<div class="color-left">

    <div class="content-sub">

        <?php _e("<h1>Find your project.<br>Find it out!</h1>", 'header-freelancer'); ?>

            <p>

                <a href="<?php echo get_post_type_archive_link(PROJECT) ?>" class="btn-sumary btn-sub-post">

                    <?php _e("Find a Project", 'header-freelancer'); ?>

                </a>

            </p>

    </div>

</div>

<div class="color-right">

	<div class="content-sub">

        <?php _e("<h1>Need a job?<br>Tell us your story</h1>", 'header-freelancer'); ?>

        <p>

            <a href="<?php echo et_get_page_link( array('page_type' => 'profile', 'post_title' => __("Create a Profile", 'header-freelancer' )) ); ?>" class="btn-sumary btn-sub-create">

                <?php _e("Review your Profile", 'header-freelancer'); ?>

            </a>

        </p>

    </div>

</div>
<div class="d-arrow"></div>