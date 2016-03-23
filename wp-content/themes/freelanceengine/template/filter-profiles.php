<?php 
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get(PROFILE);
$currency    = ae_get_option('content_currency',array('align' => 'left', 'code' => 'USD', 'icon' => '$'));
$country_list = ae_country_list();

?>
<div class="header-sub-wrapper">
    <div class="container box-shadow-style-theme search-form-top">
        <div class="row">
            <div class="col-md-2 col-sm-6">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top"><?php _e('Category', 'filter-profiles'); ?></h2>
<!--                    <p>-->
<!--                        --><?php //
//                            ae_tax_dropdown( 'project_category' ,
//                                  array(  'attr' => 'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="'.__("Choose categories", 'filter-profiles').'"',
//                                          'class' => 'cat-filter chosen-select',
//                                          'hide_empty' => false,
//                                          'hierarchical' => true ,
//                                          'id' => 'project_category' ,
//                                          'show_option_all' => __("All categories", 'filter-profiles'),
//                                          'value' => 'slug'
//                                      )
//                            );
//                        ?>
<!--                    </p>-->
                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal"
                            data-target="#category-modal">
<?php _e('Select the category', 'filter-profiles'); ?>
                    </button>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="content-search-form-top-wrapper">
                    <div class="search-control">
                        <h2 class="title-search-form-top">
                            <?php _e('Location', 'filter-profiles') ?>
                        </h2>
                        <select data-chosen-width="100%" data-chosen-disable-search=""
                                data-placeholder="Choose categories" name="country" id="country"
                                class="location-filter chosen-select" style="display: none;">
                            <option value="" class="level-0" selected><?php _e('Choose country', 'filter-profiles') ?></option>
                            <?php
                            if (!empty($country_list)) {
                                foreach ($country_list as $key => $value) {
                                    echo '<option value="' . $value->country_name . '" class=" ' . $value->country_name . '  level-0">' . $value->country_name . '</option>';
                                }

                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
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
//            $max_slider = ae_get_option('fre_slide_max_budget', 2000);
            $range = get_profiles_price_range();
            $max_value = (int) $range->max_price;
            $min_value = (int) $range->min_price;
            ?>
            <div class="col-md-2 col-sm-6 ">
                <div class="content-search-form-top-wrapper">
                    <h2 class="title-search-form-top">
                        <?php _e('Hourly rate', 'filter-profiles');?>
                    </h2>
                    <input id="hour_rate" type="text" name="hour_rate" class="slider-ranger" value="" data-slider-min="0" 
                        data-slider-max="<?php echo $max_value; ?>" data-slider-step="5"
                        data-slider-value="[0,<?php echo $max_value; ?>]"
                    /> 
                    <b class="currency"><?php echo fre_price_format($max_value) ?></b>
                    <input type="hidden" name="et_hour_rate" id="et_hour_rate" value= "" />
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="content-search-form-top-wrapper">
                    <div class="skill-control">
                        <h2 class="title-search-form-top">
                            <?php _e('Skills Required', 'filter-profiles') ?>
                        </h2>
                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal"
                                data-target="#skillsmodal">
                            <?php _e('Select skills', 'filter-profiles'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="content-search-form-top-wrapper">
                    <div class="reset-control">
                        <h2 class="title-search-form-top"><?php _e('Reset filters', 'filter-profiles') ?></h2>
                        <button type="button" class="btn btn-primary btn-block btn-reset-filters">
<?php _e('Reset', 'filter-profiles'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <div class="category-filters-list text-justify">
                    <ul>
                        <?php
                        /*
                         *
                         * */
                        ?>

                    </ul>
                </div>
                <button class="btn btn-info open-filter-list-project hidden">Open</button>
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
<div class="modal fade" id="skillsmodal" tabindex="-1" role="dialog" aria-labelledby="skillsmodal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php _e('Select skills', 'filter-profiles'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="skill-control">
                    <div class="row">
                        <div class="col-xs-6">
                            <input class="form-control skill" type="text" id="skill"
                                   placeholder="<?php _e("Type here", 'filter-profiles'); ?>" name="" autocomplete="off"
                                   spellcheck="false">
                        </div>
                        <div class="col-xs-6">
                            <input type="hidden" class="skill_filter" name="filter_skill" value="1">
                            <ul class="skills-list skills-list-modal " id="skills_list"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php _e('Ok', 'filter-profiles'); ?></button>

            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-example-modal-lg" id="category-modal" tabindex="-1" role="dialog"
     aria-labelledby="category-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php _e('Select the category', 'filter-profiles'); ?></h4>
            </div>
            <div class="modal-body">
                <?php

                ae_tax_dropdown('project_category',
                    array('attr' => 'data-chosen-width="70%" data-chosen-disable-search="" multiple data-placeholder="' . __("Choose categories", 'filter-profiles') . '"',
                        'class' => 'cat-filter hidden',
                        'hide_empty' => false,
                        'hierarchical' => true,
                        'id' => 'project_category',
                        'show_option_all' => __("All categories", 'filter-profiles'),
                        'value' => 'slug'
                    )
                );

                ?>
                <div class="row">
                    <div id="category-all" class="col-xs-12 col-md-12 text-left"></div>
                </div>
                <div id="category-parent-checkbox" class="row">
                    <div id="column-1" class="col-xs-12 col-md-6"></div>
                    <div id="column-2" class="col-xs-12 col-md-6"></div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><?php _e('Ok', 'filter-profiles'); ?></button>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    var half_count = Math.round(jQuery("#project_category > option").length / 2);
    var parent_category = '';
    jQuery("#project_category > option").each(function (index) {
        select = '';
        if (jQuery(this).attr("selected")) {
            select = 'checked';
        }
        if (index < half_count) {
            if (index == 0) {
                var allcategory = '<div class="checkbox ' + '"><label><input type="checkbox" name="' + jQuery(this).attr('value') + '" value="' + '" ' + select + '>' + jQuery(this).text() + '</label></div>';
            }
            else {
                var current = jQuery(this).attr('class').split(" ");
                if (current[3] == 'level-0') {
                    parent_category = current[1];
                    jQuery('#category-parent-checkbox > #column-1').append('<div class="checkbox ' + jQuery(this).attr('class') + '"><label><input type="checkbox" name="' + jQuery(this).attr('value') + '" value="' + jQuery(this).attr('value') + '" ' + select + '>' + jQuery(this).text() + '</label></div>');
                }
                else {
                    jQuery('#category-parent-checkbox > #column-1').append('<div class="checkbox ' + jQuery(this).attr('class') + '"><label><input type="checkbox" data-parent="' + parent_category + '" name="' + jQuery(this).attr('value') + '" value="' + jQuery(this).attr('value') + '" ' + select + '>' + jQuery(this).text() + '</label></div>');
                }
            }
        }
        else {
            var current = jQuery(this).attr('class').split(" ");
            if (current[3] == 'level-0') {
                parent_category = current[1];
                jQuery('#category-parent-checkbox > #column-2').append('<div class="checkbox ' + jQuery(this).attr('class') + '"><label><input type="checkbox" name="' + jQuery(this).attr('value') + '" value="' + jQuery(this).attr('value') + '" ' + select + '>' + jQuery(this).text() + '</label></div>');
            }
            else {
                jQuery('#category-parent-checkbox > #column-2').append('<div class="checkbox ' + jQuery(this).attr('class') + '"><label><input type="checkbox" data-parent="' + parent_category + '" name="' + jQuery(this).attr('value') + '" value="' + jQuery(this).attr('value') + '" ' + select + '>' + jQuery(this).text() + '</label></div>');
            }
        }
//        else {
//            var current = jQuery(this).attr('class').split(" ");
//            if (current[3] == 'level-0') {
//                parent_category = current[1];
//                jQuery('#category-parent-checkbox > #column-3').append('<div class="checkbox ' + jQuery(this).attr('class') + '"><label><input type="checkbox" name="' + jQuery(this).attr('value') + '" value="' + jQuery(this).attr('value') + '" ' + select + '>' + jQuery(this).text() + '</label></div>');
//            }
//            else {
//                jQuery('#category-parent-checkbox > #column-3').append('<div class="checkbox ' + jQuery(this).attr('class') + '"><label><input type="checkbox" data-parent="' + parent_category + '" name="' + jQuery(this).attr('value') + '" value="' + jQuery(this).attr('value') + '" ' + select + '>' + jQuery(this).text() + '</label></div>');
//            }
//        }
        jQuery('#category-all').append(allcategory);
    });
    jQuery("#category-parent-checkbox :checkbox").change(function () {
        jQuery("#category-all :checkbox").removeAttr("checked");

        if (jQuery(this).attr('checked') == 'checked') {
            if (jQuery(this).parent().parent().hasClass('level-0')) {
                var current_value = jQuery(this).attr('value');
                jQuery("input[data-parent='" + current_value + "'").each(function (index) {
                    jQuery(this).attr('checked', 'checked');
                });
            }
            else {
                var thischeckbox = jQuery(this);
                var current_value = jQuery(this).attr('data-parent');
                var countsss = 0;
                var count_child = jQuery("input[data-parent='" + jQuery(this).attr('data-parent') + "'").length;
                jQuery("input[data-parent='" + thischeckbox.attr('data-parent') + "']:checked").each(function (index) {
                    countsss++;
                });
                if (countsss == count_child) {
                    jQuery("input[value='" + current_value + "'").attr('checked', 'checked');
                }
            }
        }
        else {
            if (jQuery(this).parent().parent().hasClass('level-1')) {
                var current_value = jQuery(this).attr('data-parent');
                jQuery("input[value='" + current_value + "'").each(function (index) {
                    jQuery(this).removeAttr('checked');
                });
            }
            else {
                var current_value = jQuery(this).attr('value');
                jQuery("input[data-parent='" + current_value + "'").each(function (index) {
                    jQuery(this).removeAttr('checked');
                });
            }
        }
        if (jQuery("#category-parent-checkbox :checked").length == 0) {
            jQuery("#category-all :checkbox").attr('checked', 'checked');
        }
    });

    jQuery("#category-all :checkbox").change(function () {
        if (jQuery(this).attr('checked') == 'checked') {
            var optionthis = jQuery("#project_category > option");
            jQuery("#category-parent-checkbox :checkbox").removeAttr("checked");
            optionthis.removeAttr("selected");
        }
        if (jQuery("#category-parent-checkbox :checked").length == 0) {
            jQuery("#category-all :checkbox").attr('checked', 'checked');
        }
    });

    jQuery('#category-modal').on('hidden.bs.modal', function () {
        var count = jQuery("#category-parent-checkbox :checkbox").length;
        var count_checked = jQuery("#category-all :checked,#category-parent-checkbox :checked").length;
        var htmloutput_filters = '';
        if (count_checked == 1 && jQuery("#category-all :checked").length == 1) {
            var options = jQuery('#project_category > option');
            options.each(function (index) {
                jQuery(this).removeAttr('selected');
            });
            jQuery('#project_category').change();
        } else {
            jQuery("#category-parent-checkbox :checkbox").each(function (index) {
                var optionthis = jQuery("option[value='" + jQuery(this).attr('value') + "']");
                var current_value = jQuery(this).attr('value');
                if (jQuery(this).attr('checked') == 'checked') {
                    varthis = jQuery("option[value='" + current_value + "']");
                    varthis.attr("selected", "selected");
                    htmloutput_filters += '<li ' + 'data-parent="' + current_value + '"' + '><span>' + varthis.text() + '</span><a href="javascript:void(0);" class="delete" onclick="del_filter(\'' + current_value + '\')"><i class="fa fa-times"></i></a></li>'
                    //console.log(jQuery("option[value='" + current_value + "']").text());
                } else {
                    jQuery("option[value='" + current_value + "']").removeAttr("selected");
                }
                if (index === count - 1) {
                    optionthis.change();
                }
            });
        }

        jQuery('.category-filters-list > ul').html(htmloutput_filters);
        jQuery('.open-filter-list-project').text('Open');
        if (jQuery('.category-filters-list > ul').height() < 40) {
            jQuery('.open-filter-list-project').addClass('hidden');
            jQuery('.category-filters-list > ul').removeClass('all-hidden');
        } else {
            jQuery('.open-filter-list-project').removeClass('hidden');
            jQuery('.category-filters-list > ul').addClass('all-hidden');
        }

        AE.pubsub.trigger('ae:notification', {
            msg: <?php _e('Query processing, please wait a bit.', 'filter-profiles'); ?>,
            notice_type: 'success'
        });
    });
    //    jQuery('.category-filters-list').bind("DOMSubtreeModified", function() {
    ////        alert("tree changed");
    //    });
    jQuery('.open-filter-list-project').on('click', function () {
        jQuery('.category-filters-list > ul').toggleClass('all-hidden');
        if (jQuery('.category-filters-list > ul').hasClass('all-hidden')) {
            jQuery(this).text('Open')
        } else {
            jQuery(this).text('Hide')
        }
    });
    function del_filter(val) {

        jQuery("option[value='" + val + "']").removeAttr("selected").change();
        jQuery("input[value='" + val + "'").removeAttr('checked');
        jQuery("li[data-parent='" + val + "']").remove();
    }

    jQuery('.btn-reset-filters').on('click', function () {
        //reset location
        var countries = jQuery('#country > option');
        countries.each(function (index) {
            jQuery(this).removeAttr('selected');
        });
        jQuery("#country > option[value='']").attr('selected', 'selected');
        jQuery('#country').trigger('chosen:updated');

        //reset category list
        var categories = jQuery('#project_category > option');
        categories.each(function (index) {
            jQuery(this).removeAttr('selected');
        });

        jQuery('#category-all :checkbox').attr('checked', 'checked');
        jQuery("#category-parent-checkbox :checked").each(function (index) {
            jQuery(this).removeAttr('checked');
        });
        jQuery('.category-filters-list > ul').html('');
        //reset search
        jQuery('.header-sub-wrapper').find('.search').removeAttr('value').text('').keyup();
        ;
        //reset types
        jQuery("#project_type > option").each(function (index) {
            jQuery(this).removeAttr('selected');
        });
        jQuery("#project_type > option[value='']").attr('selected', 'selected');
        jQuery('#project_type_chosen > a > span').text('All types');
        //reset range values
        jQuery('.slider-track').contents().first()
            .css("left", "0%")
            .css("width", "100%")
            .next()
            .css("left", "0%")
            .next()
            .css("left", "100%");
        var for_tooltip_left = (jQuery('.slider').width() - jQuery('.slider > .tooltip').width()) / 2;
        var input_range_budget = jQuery('#et_budget');
        jQuery('.slider > .tooltip')
            .css("top", "-30")
            .css("left", for_tooltip_left);
        jQuery('.slider').find('.tooltip-inner').text(input_range_budget.attr('data-slider-min') + '  :  ' + input_range_budget.attr('data-slider-max'));
        jQuery('#et_budget').removeAttr('value').change();
        //reset skills
        jQuery('.skill-item a.delete').click();
        jQuery('#skills_list').html('');
        //reset init
        jQuery('#country').change();
        jQuery('#project_category').change();

    });
</script>