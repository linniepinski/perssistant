<script type="text/template" id="ae-post-loop">
    <div class="row">
        <div class="col-md-3 col-xs-3">
            <div class="author-wrapper">
                <span class="avatar-author">
                    {{= avatar }}
                </span>
                <span class="date">
                    {{= author_name }}<br>
                    {{= post_date }}
                </span>
            </div>
        </div>
        <div class="col-md-9 col-xs-9">
            <div class="blog-content">
                <span class="tag">
                	{{= category_name }}
                </span>
                <span class="cmt">
                	<i class="fa fa-comments"></i> {{= comment_number }}
                </span>
                <h2 class="title-blog">
	                <a href="{{= permalink }}">
	               		{{= post_title }}
	                </a>
                </h2>
				<div class="post-excerpt">
					{{= post_excerpt }}
				</div>
                <a href="{{= permalink }}" class="read-more">
                    <?php _e("READ MORE",ET_DOMAIN) ?><i class="fa fa-arrow-circle-o-right"></i>
                </a>
            </div>
        </div>
    </div>  
</script>