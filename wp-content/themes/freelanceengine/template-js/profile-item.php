<script type="text/template" id="ae-profile-loop">
 	<div class="profile-content">
        <ul class="top-profile">
            <li class="img-avatar">
                <a href="{{= author_link }}"><span class="avatar-profile">{{= et_avatar}} </span></a>
            </li>
            <li class="info-profile">
                <a href="{{= author_link }}"> <span class="name-profile">{{= author_name }}</span></a>
                <span class="position-profile">{{= et_professional_title }}</span>
            </li>
            <li class="link-profile">
                <a href="{{= author_link }}" class="btn btn-view-profile" title="<?php _e("View Profile", ET_DOMAIN); ?>">
                <span><?php _e("View Profile", ET_DOMAIN); ?></span>
                </a>
            </li>
        </ul>  
        <ul class="bottom-profile">
            <li class="wrapper-achivement">
                <ul>
                    <li>
                        <div class="rate-it" data-score="{{= rating_score }}"></div>
                    </li>
                    <li><span>{{= hourly_rate_price }}</span></li>
                    <li><span>{{= experience }}</span></li>
                </ul>
            </li>
            <li class="list-skill-profile">
                <ul>
                {{= skill_list }}   
                </ul>
            </li>
        </ul> 
    </div>
</script>