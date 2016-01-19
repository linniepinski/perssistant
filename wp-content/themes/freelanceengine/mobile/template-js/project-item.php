<script type="text/template" id="ae-project-loop">
    <div class="info-project-top">
        <div class="avatar-author-project">
            <a href="{{= author_url }}" title="{{= author_name }}" >{{= et_avatar }} </a>
        </div>
        <a class="title-project" href="{{= permalink }}">{{= post_title }}</a>
        <# if(parseInt(et_featured)) { #>
            <span class="ribbon"><i class="fa fa-star"></i></span>
        <# } #>
        <div class="clearfix"></div>
    </div>
    <div class="info-bottom">
        <span class="name-author">{{= posted_by }}</span>
        <span class="price-project">{{= budget }}</span>
    </div>    
</script>
