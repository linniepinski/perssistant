<?php 
$author_id = get_query_var('author');
//if(fre_share_role() || ae_user_role($author_id) == FREELANCER ) { ?>
<script type="text/template" id="ae-bid-history-loop">
    <div class="info-project-top">
        <div class="avatar-author-project">
            {{= project_author_avatar }}
        </div>
        <h1 class="title-project">
            <a href="{{= project_link }}" title="{{= project_title }}">{{= project_title }}</a>
        </h1>
        <div class="clearfix"></div>
    </div>
    <div class="info-bottom">
        <# if(project_status == 'complete'){ #>
            <# if(typeof project_comment !== 'undefined' && project_comment){ #>
                <span class="comment-stt-project"><blockquote>{{= project_comment }}</blockquote></span>
            <# } #>
            <span class="star-project">
                <div class="rate-it" data-score="{{= rating_score }}"></div>
            </span>            
        <# } else if(project_status == 'close'){ #>
            <span class="status"><?php _e('Job is closed', ET_DOMAIN);?></span>
        <# } else { #>
            <span class="status"><?php _e('Job in process', ET_DOMAIN);?></span>
        <# } #>
        <div class="clearfix"></div>
    </div>
</script>

<?php //} 

//if(fre_share_role() || ae_user_role($author_id) != FREELANCER ) { ?>
    <script type="text/template" id="ae-work-history-loop">
    <div class="info-project-top">
        <div class="avatar-author-project">
            {{= et_avatar }}
        </div>
        <h1 class="title-project">
            <a href="{{= permalink }}" title="{{= post_title }}">{{= post_title }}</a>
        </h1>
        <div class="clearfix"></div>
    </div>
    <div class="info-bottom">
        <# if(post_status == 'complete'){ #>
            <# if(typeof project_comment !== 'undefined' && project_comment != ''){ #>
                <span class="comment-stt-project"><blockquote>{{= project_comment }}</blockquote></span>
            <# } #>
            <span class="star-project">
                <div class="rate-it" data-score="{{= rating_score }}"></div>
            </span>            
        <# } else if(post_status == 'close'){ #>
            <span class="status"><?php _e('Job is closed', ET_DOMAIN);?></span>
        <# } else { #>
            <span class="status"><?php _e('Job in process', ET_DOMAIN);?></span>
        <# } #>
    </div>
</script>
<?php //}