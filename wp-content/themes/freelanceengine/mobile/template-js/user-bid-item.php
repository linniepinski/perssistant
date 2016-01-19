<?php $currency =  ae_get_option('content_currency',array('align' => 'left', 'code' => 'USD', 'icon' => '$')); ?>
<script type="text/template" id="ae-user-bid-loop">

    <li class="post-259 bid type-bid status-publish hentry user-bid-item">
        <div class="row user-bid-item-list">
            <div class="col-md-6">
               {{= project_author_avatar }} 
               <a href= " {{=project_link}}"<span class="content-title-project-item">{{=project_title}}</span> </a>
            </div>
                <div class="col-md-6">
                <# if(post_status == 'publish') {#>
                <a class="btn btn-apply-project-item" href="{{=project_link}}">
                   <?php _e('Cancel',ET_DOMAIN);?>
                </a>
                <# } #>
                </div>
        </div>
                
        <div class="user-bid-item-info">
            <ul class="info-item">
                <li>
                    <span class="number-blue"> {{=total_bids}}</span> <# if(total_bids >1) { #> <?php _e('Bids',ET_DOMAIN);?> <# } else { #> <?php _e('Bid',ET_DOMAIN) ?> <# } #>  </li>
                <li>
                    <span class="number-blue">
                       {{=bid_average}}     </span> <?php printf(__('Avg Bid (%s)',ET_DOMAIN), $currency['code']);?>            </li>
                <li>
                    <span class="number-blue">
                        
                    </span> 
                </li>
                <li>
                    <span>
                        <?php _e('Bidding',ET_DOMAIN);?>: {{=bid_budget_text}}
                    </span>
                    <span class="number-blue"> 
                        
                    </span> in {{=bid_time}}  {{=type_time}}            </li>
            </ul>
        </div>
    </li>

</script>