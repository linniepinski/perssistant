<?php 
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get(PROFILE);
$currency    = ae_get_option('content_currency',array('align' => 'left', 'code' => 'USD', 'icon' => '$'));
?>
<div class="header-sub-wrapper">
    <div class="container box-shadow-style-theme search-form-top">
        <div class="row">
            <div class="col-md-15 col-sm-6">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top"><?php _e('Category', 'filter-profiles'); ?></h2>
                    <p>
                        <?php 
                            ae_tax_dropdown( 'project_category' , 
                                  array(  'attr' => 'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="'.__("Choose categories", 'filter-profiles').'"', 
                                          'class' => 'cat-filter chosen-select', 
                                          'hide_empty' => true, 
                                          'hierarchical' => true , 
                                          'id' => 'project_category' , 
                                          'show_option_all' => __("All categories", 'filter-profiles'),
                                          'value' => 'slug'
                                      ) 
                            );
                        ?>
                    </p>
                </div>
            </div>
            <div class="col-md-15 col-sm-6">
                <div class="content-search-form-top-wrapper">
                    <div class="search-control">
                        <h2 class="title-search-form-top">
                            <?php _e('Location', 'filter-profiles') ?>
                        </h2>
                        <p>
                            <?php 
                                ae_tax_dropdown( 'country' , 
                                      array(  'attr' => 'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="'.__("Choose categories", 'filter-profiles').'"', 
                                              'class' => 'cat-filter chosen-select', 
                                              'hide_empty' => true, 
                                              'hierarchical' => true , 
                                              'id' => 'country' , 
                                              'show_option_all' => __("All locations", 'filter-profiles'),
                                              'value' => 'slug'
                                          ) 
                                );
                            //var_dump($GLOBALS['wp_query']->request);

                            ?> 
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-15 col-sm-6">
                <div class="content-search-form-top-wrapper">
                    <div class="search-control">
                        <h2 class="title-search-form-top">
                            <?php _e('Keyword', 'filter-profiles') ?>
                        </h2>
                        <div class="skills-wrap">
                            <input class="form-control keyword search" type="text" id="s" placeholder="<?php _e("Keyword", 'filter-profiles'); ?>" name="s"  autocomplete="off" spellcheck="false" >
                            <i class="fa fa-search"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="clearfix hidden-lg hidden-md visible-xs-block "></div>
            
            <?php 
            $max_slider = ae_get_option('fre_slide_max_budget', 2000);
            ?>
            <div class="col-md-15 col-sm-6 ">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top">
                        <?php _e('Hourly rate', 'filter-profiles');?>
                    </h2>
                    <input id="hour_rate" type="text" name="hour_rate" class="slider-ranger" value="" data-slider-min="0" 
                        data-slider-max="<?php echo $max_slider; ?>" data-slider-step="5" 
                        data-slider-value="[0,<?php echo $max_slider; ?>]"
                    /> 
                    <b class="currency"><?php echo fre_price_format($max_slider) ?></b>
                    <input type="hidden" name="et_hour_rate" id="et_hour_rate" value= "" />
                </div>
            </div>

            <div class="col-md-15 col-sm-6">
                <div class="content-search-form-top-wrapper">
                    <div class="skill-control">
                        <h2 class="title-search-form-top">
                            <?php _e('Skills Required', 'filter-profiles') ?>
                        </h2>
                        <div class="skills-wrap">
                            <input type="text" class="form-control skill" id="skill" placeholder="<?php _e("Keyword", 'filter-profiles'); ?>" name=""  autocomplete="off" spellcheck="false" />
                            <i class="fa fa-search"></i>
                        </div>
                        <input type="hidden" class="skill_filter" name="filter_skill" value="1" />
                        <ul class="skills-list" id="skills_list"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="number-profile-wrapper">
      <div class="container">
          <div class="row">
              <div class="col-md-12">
                  <h2 class="number-profile">
                  <?php 
                        $found_posts = '<span class="found_post">'.$wp_query->found_posts.'</span>';
                        $plural = sprintf(__('%s Profiles ','filter-profiles'), $found_posts);
                        $singular = sprintf(__('%s Profile','filter-profiles'),$found_posts);
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