<?php 
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get(PROFILE);
$currency    = ae_get_option('content_currency',array('align' => 'left', 'code' => 'USD', 'icon' => '$'));
?>
<div class="header-sub-wrapper">
    <div class="container box-shadow-style-theme search-form-top">
        <div class="row">
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top"><?php _e('Category', ET_DOMAIN); ?></h2>
                    <p>
                        <?php 
                            ae_tax_dropdown( 'project_category' , 
                                  array(  'attr' => 'data-chosen-width="90%" data-chosen-disable-search="" data-placeholder="'.__("Choose categories", ET_DOMAIN).'"', 
                                          'class' => 'cat-filter chosen-select', 
                                          'hide_empty' => true, 
                                          'hierarchical' => true , 
                                          'id' => 'project_category' , 
                                          'show_option_all' => __("All categories", ET_DOMAIN),
                                          'value' => 'slug'
                                      ) 
                            );
                        ?> 
                    </p>
                </div>
            </div>
    
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <div class="search-control">
                        <h2 class="title-search-form-top"><?php _e('Keyword', ET_DOMAIN) ?></h2>
                        <input class="form-control keyword search" type="text" id="s" placeholder="<?php _e("Keyword", ET_DOMAIN); ?>" name="s"  autocomplete="off" spellcheck="false" >
                    </div>
                </div>
            </div>
    
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top"><?php _e("Project Type", ET_DOMAIN); ?></h2>
                    <p>
                        <?php 
                            ae_tax_dropdown( 'project_type' , 
                                  array(  'attr' => 'data-chosen-width="90%" data-chosen-disable-search="1"  data-placeholder="'.__("All types", ET_DOMAIN).'"', 
                                          'class' => 'type-filter chosen-select', 
                                          'hide_empty' => true, 
                                          'hierarchical' => true , 
                                          'id' => 'project_type' , 
                                          'show_option_all' => __("All types", ET_DOMAIN),
                                          'value' => 'slug'
                                      ) 
                            );
                        ?> 
                    </p>
                </div>
            </div>
            
            <?php 
            $max_slider = ae_get_option('fre_slide_max_budget', 2000);
            ?>
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top"><?php _e("Budget", ET_DOMAIN); ?></h2>
                    <input id="et_budget" type="text" name="et_budget" class="slider-ranger" value="" data-slider-min="0" 
                        data-slider-max="<?php echo $max_slider; ?>" data-slider-step="5" 
                        data-slider-value="[0,<?php echo $max_slider; ?>]"
                    /> 
                    <b class="currency"><?php echo fre_price_format($max_slider) ?></b>
                    <input type="hidden" name="budget" id="budget" value= "" />
                </div>
            </div>
            <div class="col-md-15">
                <div class="content-search-form-top-wrapper">
                    <div class="skill-control">
                        <h2 class="title-search-form-top"><?php _e('Your Skills', ET_DOMAIN) ?></h2>
                        <input class="form-control skill" type="text" id="skill" placeholder="<?php _e("Type and enter", ET_DOMAIN); ?>" name=""  autocomplete="off" spellcheck="false" >
                        <input type="hidden" class="skill_filter" name="filter_skill" value="1">
                        <ul class="skills-list" id="skills_list"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="number-project-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="number-project">

                    <?php 
                        $found_posts = '<span class="found_post">'.$wp_query->found_posts.'</span>';
                        $plural = sprintf(__('%s Projects for you',ET_DOMAIN), $found_posts);
                        $singular = sprintf(__('%s Projects for you',ET_DOMAIN),$found_posts);
                    ?>
                        <span class="plural <?php if( $wp_query->found_posts <= 1 ) { echo 'hide'; } ?>" >
                            <?php echo $plural; ?>
                        </span>
                        <span class="singular <?php if( $wp_query->found_posts > 1 ) { echo 'hide'; } ?>">
                            <?php echo $singular; ?>
                        </span>
                    </h2>
                </div>
            </div>
        </div>
    </div>
</div>
