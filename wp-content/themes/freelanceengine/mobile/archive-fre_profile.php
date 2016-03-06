<?php
$user_role = ae_user_role();
et_get_mobile_header();
?>
<section class="section-wrapper section-profile section-archive-profile">
	<div class="list-link-tabs-page">
    	<div class="container">
            <?php if($user_role != "employer"): ?>
            <a href="<?php echo get_post_type_archive_link(PROJECT) ?>" ><?php _e("Projects", ET_DOMAIN); ?></a>
            <?php endif; ?>
            
            <?php if($user_role != "freelancer"): ?>
            <a href="<?php echo get_post_type_archive_link(PROFILE) ?>" class="active"><?php _e("Profiles", ET_DOMAIN); ?></a>
            <?php endif; ?>
        </div>
    </div>
    <div class="advanced-search-wrapper">
        <div class="search-normal-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-8">
                        <span class="title-advance-search"><?php _e("Search Advanced", ET_DOMAIN); ?></span>
                    </div>
                    <div class="col-xs-4"><a href="#" class="hide-search-advance"><?php _e("Cancel", ET_DOMAIN); ?></a></div>
                </div>
            </div>
        </div>

    </div>
    <div class="profiles-wrapper">
        <div class="search-normal-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-8">
                        <span class="icon-form-search icon-search"></span>
                        <input type="text" name="s" value="" placeholder="<?php _e("Type keyword", ET_DOMAIN); ?>" class="search-normal-input keyword search">
                    </div>
                    <div class="col-xs-4"><a href="#" class="show-search-advance"><?php _e("Advanced", ET_DOMAIN); ?></a></div>
                    <div class="col-xs-4" style="display:none;"><a href="#" class="hide-search-advance"><?php _e("Cancel", ET_DOMAIN); ?></a></div>
                </div>
            </div>
            <div class="container" id="advance-search" style="display:none; margin-top: 5px;">
                <?php
                    get_template_part('mobile/search', 'profiles-form');
                ?>
            </div>
            <div class="container">
                <div class="form-group">
<!--                    <input type="button" value="--><?php //_e("Search", ET_DOMAIN); ?><!--" class="btn-sumary btn-search-advance search-mobile">-->
                    <button class="btn-sumary btn-search-advance search-mobile"><?php _e("Search", ET_DOMAIN); ?></button>

                </div>
            </div>
        </div>
        
        <div class="list-profiles-wrapper">
            <?php get_template_part('mobile/list', 'profiles'); ?>
        </div>
        <script type="text/template" id="profile-no-result">
            <div class="col-md-12 no-result">
                <p class="alert alert-info">
                    <i class="fa fa-info-circle"></i>&nbsp;<?php _e("Sorry no results found.", ET_DOMAIN); ?>
                </p>
            </div>  
        </script>
    </div>
</section>
<?php
	et_get_mobile_footer();
?>