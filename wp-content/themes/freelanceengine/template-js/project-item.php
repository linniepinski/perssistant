<script type="text/template" id="ae-project-loop">
    <div class="row">
        <div class="col-md-5 col-sm-5 col-xs-7 text-ellipsis">
            <a href="{{= author_url }}" class="title-project">
                {{= et_avatar }}
            </a>
            <a title="{{= post_title }}" href="{{= permalink }}" class="project-item-title">
                {{= post_title }}
            </a>
        </div>
        <div class="col-md-2 col-sm-3 hidden-xs">
            <# if(parseInt(et_featured)) { #>
                <span class="ribbon"><i class="fa fa-star"></i></span>
                <# } #>
                    <span><a href="{{= author_url }}"> {{=author_name}} </a></span>
        </div>
        <div class="col-md-2 col-sm-2 hidden-sm hidden-xs">
            <span>{{= post_date }}</span>
        </div>
        <div class="col-md-1 col-sm-2 col-xs-4 hidden-xs">
            <# if(type_budget == 'hourly_rate'){ #>
                <span class="budget-project">{{=budget}}/h</span>
                <# } else { #>
                    <span class="budget-project">{{=budget}}</span>
                    <# } #>
        </div>
        <# if(post_status == 'pending'){ #>
            <div class="col-md-2 col-sm-2 col-xs-5 text-right">
                <a href="#" class="action approve" data-action="approve">
                    <i class="fa fa-check"></i>
                </a> &nbsp;
                <a href="#" class="action reject" data-action="reject">
                    <i class="fa fa-times"></i>
                </a> &nbsp;
                <a href="#edit_place" class="action edit" data-target="#" data-action="edit">
                    <i class="fa fa-pencil"></i>
                </a> &nbsp;
            </div>

            <# } else { #>

                <div class="col-md-2 col-sm-2 col-xs-5">
                    <p class="wrapper-btn">
                        <a href="{{=permalink}}" class="btn-sumary btn-apply-project">
                        <?php _e('Apply',ET_DOMAIN);?>
                    </a>
                    </p>
                </div>
                <# } #>
    </div>
</script>