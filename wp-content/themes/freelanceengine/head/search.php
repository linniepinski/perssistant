<!-- SEARCH -->
<div class="search-fullscreen" id="search_container">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="select-search-top">
                    <select data-placeholder="<?php _e("Searching", ET_DOMAIN) ?>" class="search-filter chosen-select" data-chosen-width="280px" data-chosen-disable-search="true">
                        <option value="project"><?php _e("Searching Projects", ET_DOMAIN); ?></option>
                        <option value="profile"><?php _e("Searching Freelancers", ET_DOMAIN); ?></option>
                    </select>
                </div>
            </div>
            <!-- projects container -->
            <div class="col-md-12 projects-search-container">
                <p class="wrapper-input-search-top">
                    <input type="text" name="s" class="search field-search-top" autocomplete="off" placeholder="<?php _e("TYPE KEYWORD HERE", ET_DOMAIN) ?>">
                    <span class="search-text-press"><?php _e("Press Enter", ET_DOMAIN); ?></span>
                </p>
                <div class="row title-tab-project">
                    <div class="col-md-5 col-sm-5 col-xs-7">
                        <span><?php _e("PROJECT TITLE", ET_DOMAIN); ?></span>
                    </div>
                    <div class="col-md-2 col-sm-3 hidden-xs">
                        <span><?php _e("BY", ET_DOMAIN); ?></span>
                    </div>
                    <div class="col-md-2 col-sm-2 hidden-sm hidden-xs">
                        <span><?php _e("POSTED DATE", ET_DOMAIN); ?></span>
                    </div>
                    <div class="col-md-1 col-sm-2 hidden-xs">
                        <span><?php _e("BUDGET", ET_DOMAIN); ?></span>
                    </div>
                </div>
                <ul class="list-project col-md-12 list-project1 project-list-container1" id="projects_list"></ul>
                <div class="paginations-wrapper"></div>
            </div>
            <!-- profiles container -->
            <div class="col-md-12 profiles-search-container collapse">
                <p class="wrapper-input-search-top">
                    <input type="text" name="s" class="search field-search-top" autocomplete="off" placeholder="<?php _e("TYPE KEYWORD HERE", ET_DOMAIN); ?>">
                    <span class="search-text-press"><?php _e("Press Enter", ET_DOMAIN); ?></span>
                </p>
                <div class="list-profile profile-list-container row" id="profiles_list">
                    
                </div>                
                <div class="paginations-wrapper"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/template" id="profile-no-result">
    <div class="col-md-12 no-result">
        <p class="alert alert-info">
            <i class="fa fa-info-circle"></i>&nbsp;<?php _e("Sorry no results found.", ET_DOMAIN); ?>
        </p>
    </div>  
</script>
<script type="text/template" id="project-no-result">
    <li class="no-result">
        <p class="alert alert-info">
            <i class="fa fa-info-circle"></i>&nbsp;<?php _e("Sorry no results found.", ET_DOMAIN); ?>
        </p>
    </li>  
</script>
<!-- SEARCH / END -->