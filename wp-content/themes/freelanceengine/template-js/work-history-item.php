<?php 
$author_id = get_query_var('author');
//if(fre_share_role() || ae_user_role($author_id) == FREELANCER ) { ?>
<script type="text/template" id="ae-bid-history-loop">
    <div class="name-history">
        <a href="{{=author_url}}"><span class="avatar-bid-item">{{= project_author_avatar }} </span>  </a>
        <div class="content-bid-item-history">
            <h5><a href = "{{= project_link }}">{{= project_title }}</a>
            <# if(project_status == 'complete') { #>
                <div class="rate-it" data-score="{{= rating_score }}"></div>
            </h5>
            <span class="comment-author-history">{{= project_comment }}</span>
            
            <# } else if(project_status == 'publish'){ #>
            </h5>
            <span class="stt-in-process"><?php _e('Job in process', ET_DOMAIN);?></span> 
            <# }else if(post_status == 'close') { #>
            </h5>
            <span class="stt-in-process"><?php _e('Job is closed', ET_DOMAIN);?></span> 
            <# } #>
        </div>
    </div>
    <ul class="info-history">
        <li>{{= project_post_date }}</li>
        <# if(bid_budget) { #>
            <li><?php _e('Bid Budget :', ET_DOMAIN) ; ?> <span class="number-price-project-info"> {{= bid_budget_text }} </span></li>
        <# } #>
    </ul>
    <div class="clearfix"></div>
</script>

<?php //} 
//if(fre_share_role() || ae_user_role($author_id) != FREELANCER ) { ?>
    <script type="text/template" id="ae-work-history-loop">
    <div class="name-history">
        <span class="avatar-bid-item">{{= et_avatar }}</span>  
        <div class="content-bid-item-history">
            <h5><a href = "{{= permalink }}">{{= post_title }}</a>
            <# if(post_status == 'complete') { #>
            
                <div class="rate-it" data-score="{{= rating_score }}"></div>
            </h5>
            <span class="comment-author-history">{{=project_comment}}</span>
            
            <# } else if(post_status == 'publish'){ #>
            </h5>
            <span class="stt-in-process"><?php _e('Job in process', ET_DOMAIN);?></span> 
            
            <# }else if(post_status == 'close') { #>
            </h5>
            <span class="stt-in-process"><?php _e('Job is closed', ET_DOMAIN);?></span> 
            <# } #>
        </div>
    </div>
    <ul class="info-history">
        <li>{{= post_date }}</li>
        
        <# if(budget) { #>
            <li><?php _e('Budget :', ET_DOMAIN) ; ?> <span class="number-price-project-info"> {{= budget }} </span></li>
        <# } #>
        <li class="post-control"> 
            {{= status_text }} <span> &#9830; </span>
            <?php ae_js_edit_post_button(); ?>
        <# if(typeof workspace_link !== 'undefined') { #>
            <a href = "{{= workspace_link }}" title=" <?php _e( 'Open Workspace' , ET_DOMAIN ); ?>">
                <i class="fa fa-share-square-o"></i>
            </a>
            
        <# } #>
        </li>
    </ul>
    <div class="clearfix"></div>
</script>
<?php //}