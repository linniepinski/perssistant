<?php
$country_list = ae_country_list();

?>
<form class="form-search-wrapper">
    <div class="form-group">
        <label><?php _e("Category", 'search-profiles-form-mobile'); ?></label>
        <?php
        ae_tax_dropdown('project_category', array('attr' => 'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="' . __("Choose categories", 'search-profiles-form-mobile') . '"',
            'class' => 'cat-filter chosen-select',
            'hide_empty' => false,
            'hierarchical' => true,
            'id' => 'project_category',
            'show_option_all' => __("All categories", 'search-profiles-form-mobile'),
            'value' => 'slug'
                )
        );
        ?>
    </div>
    <div class="form-group">
        <label><?php _e('Location', 'search-profiles-form-mobile') ?></label>
        <select data-chosen-width="100%" data-chosen-disable-search=""
                data-placeholder="Choose categories" name="country" id="country"
                class="location-filter chosen-select" style="display: none;">
            <option value="" class="level-0" selected><?php _e('Choose country', 'search-profiles-form-mobile') ?></option>
            <?php
            if (!empty($country_list)) {
                foreach ($country_list as $key => $value) {
                    echo '<option value="' . $value->country_name . '" class=" ' . $value->country_name . '  level-0">' . $value->country_name . '</option>';
                }

            }
            ?>
        </select>
    </div>
    <?php
    
    $range = get_profiles_price_range();
    $max_value = (int)$range->max_price;
    $min_value = (int)$range->min_price;
    
//    var_dump($range);
    
    $max_slider = ae_get_option('fre_slide_max_budget', $max_value);

    ?>
    <div class="form-group">
        <label><?php _e("Hourly Rate", 'search-profiles-form-mobile'); ?></label>
        <input id="hour_rate" type="text" name="hour_rate" class="slider-ranger" value="" data-slider-min="<?php echo $min_value; ?>" 
               data-slider-max="<?php echo $max_slider; ?>" data-slider-step="5"
               data-slider-value="[<?php echo $min_value; ?>,<?php echo $max_value; ?>]"
               /> 
        <input type="hidden" name="et_hour_rate" id="et_hour_rate" value= "" />
    </div>
    <div class="form-group">
        <label><?php _e("Skills", 'search-profiles-form-mobile'); ?></label>
        <div class="skill-control">
            <input class="form-control skill" type="text" id="skill" placeholder="<?php _e("Type and enter", 'search-profiles-form-mobile'); ?>" name=""  autocomplete="off" spellcheck="false" >
            <input type="hidden" class="skill_filter" name="filter_skill" value="1">
            <ul class="skills-list" id="skills_list"></ul>
        </div>
    </div>
    <div class="form-group">
        <button class="btn-sumary btn-search-advance reset-mobile"><?php _e("Reset", 'search-profiles-form-mobile'); ?></button>
    </div>
<!--    <div class="form-group">-->
<!--        <input type="submit" name="submit" value="--><?php //_e("HIDE ADVANCED SEARCH", 'search-profiles-form-mobile'); ?><!--" class="hide-advance-search btn-sumary btn-search-advance">-->
<!--    </div>-->
</form>