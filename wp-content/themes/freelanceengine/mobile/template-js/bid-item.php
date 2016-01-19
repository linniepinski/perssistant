<script type="text/template" id="ae-bid-loop">
    <div class="bid-item-{{=post_status}}">
        <div class="info-author-bidders">
            <div class="avatar-proflie">
                <a href="{{=author_url}}"><span class="avatar-profile">  {{= et_avatar }}<</span></a>
            </div>
            <div class="user-proflie">
                <span class="name">{{=profile_display}}</span>
                <span class="position">{{=et_professional_title}}</span>
            </div>
        </div>
        <ul class="wrapper-achivement">
            <li>          
                <div class="rate-it" data-score= "{{=rating_score }}" ></div> 
            </li>
            <li><span><# if(experience){ #> {{=experience}} <# } #> </span> </li>
        </ul>
        <div class="clearfix"></div>
        <div class="bid-price-wrapper">
            <div class="bid-price">
                <span class="number">{{= bid_budget_text }}</span> {{= bid_time_text }}
            </div>
            <p class="btn-warpper-bid col-md-3 number-price-project">
            <# if( post_status =='publish' &&  current_user == project_author  ){ #>
                    <button class="btn-sumary btn-accept-bid btn-bid-status btn-bid" rel="{{= post_parent }}" href="#"> 
                        <?php _e('Accept', ET_DOMAIN); ?> 
                    </button> 
                    <span class="confirm"></span>
            <# } else if( (post_status == 'close' || post_status == 'complete') && accepted == ID){ #>                
                <div class="ribbon"><i class="fa fa-trophy"></i></div>
            <# } #>
           
            </p>
        </div>
        <div class="clearfix"></div>
    </div>
</script>