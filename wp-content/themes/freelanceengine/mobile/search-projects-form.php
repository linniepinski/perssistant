<form class="form-search-wrapper">
    <div class="form-group">
        <label><?php _e("Category", ET_DOMAIN); ?></label>
        <?php
        ae_tax_dropdown('project_category', array('attr' => 'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="' . __("Choose categories", ET_DOMAIN) . '"',
            'class' => 'cat-filter chosen-select',
            'hide_empty' => true,
            'hierarchical' => true,
            'id' => 'project_category',
            'show_option_all' => __("All categories", ET_DOMAIN),
            'value' => 'slug'
                )
        );
        ?>
    </div>
    <div class="form-group">
        <label><?php _e("Project Type", ET_DOMAIN); ?></label>
        <?php
        ae_tax_dropdown('project_type', array('attr' => 'data-chosen-width="100%" data-chosen-disable-search=""  data-placeholder="' . __("All types", ET_DOMAIN) . '"',
            'class' => 'type-filter chosen-select',
            'hide_empty' => true,
            'hierarchical' => true,
            'id' => 'project_type',
            'show_option_all' => __("All types", ET_DOMAIN),
            'value' => 'slug'
                )
        );
        ?> 
    </div>
        <?php
        $range = get_project_price_range();
        $max_value = (int) $range->max_price;
        $min_value = (int) $range->min_price;
        $max_slider = ae_get_option('fre_slide_max_budget', $max_value);
        ?>
    <div class="form-group">
        <label><?php _e("Budget", ET_DOMAIN); ?></label>
        <input id="et_budget" type="text" name="et_budget" class="slider-ranger" value="" data-slider-min="0" 
               data-slider-max="<?php echo $max_value; ?>" data-slider-step="5"
               data-slider-value="[0,<?php echo $max_value; ?>]"
               /> 
        <input type="hidden" name="budget" id="budget" value= "" />
    </div>
    <div class="form-group">
        <label><?php _e("Skills", ET_DOMAIN); ?></label>
        <div class="skill-control">
            <input class="form-control skill" type="text" id="skill" placeholder="<?php _e("Type and enter", ET_DOMAIN); ?>" name=""  autocomplete="off" spellcheck="false" >
            <input type="hidden" class="skill_filter" name="filter_skill" value="1">
            <ul class="skills-list" id="skills_list"></ul>
        </div>
    </div>

    <div class="form-group">
        <button class="btn-sumary btn-search-advance reset-mobile"><?php _e("Reset", ET_DOMAIN); ?></button>
    </div>

<!--    <div class="form-group">-->
<!--        <input type="submit" name="submit" value="--><?php //_e("HIDE ADVANCED SEARCH", ET_DOMAIN); ?><!--" class="hide-advance-search btn-sumary btn-search-advance">-->
<!--    </div>-->
</form>
