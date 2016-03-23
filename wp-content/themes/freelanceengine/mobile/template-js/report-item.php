<script type="text/template" id="ae-report-loop">
    <div class="form-group-work-place">
        <div class="info-avatar-report">
            <a href="#" class="avatar-employer-report">
               {{=avatar}}
            </a>
            <div class="info-report">
                <span class="name-report">{{= display_name }}</span>
                <div class="date-chat-report">
                    {{= message_time }}
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="content-report-wrapper">
            <div class="content-report">
                {{= comment_content }}
            </div>
            <# if(file_list){ #>
            <div class="title-attachment"><?php _e("Attachment", 'report-item-mobile'); ?></div>
            {{= file_list }}
            <# } #>
        </div>
    </div>
</script>

<div class="modal fade" id="acceptance_project" tabindex="-1" role="dialog" aria-labelledby="acceptance_project" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
                <h4 class="modal-title">
                    <?php _e("Bid acceptance", 'report-item-mobile') ?>
                </h4>
            </div>
            <div class="modal-body">
                <form id="escrow_bid" class="">
                    <div class="escrow-info">
                        <!-- bid info content here -->
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-submit btn btn-primary btn-sumary btn-sub-create">
                            <?php _e('Accept Bid', 'report-item-mobile') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!-- MODAL BID acceptance PROJECT-->
<script type="text/template" id="bid-info-template">
    <label style="line-height:2.5;"><?php _e( 'You are about to accept this bid for' , 'report-item-mobile' ); ?></label>
    <p><strong class="color-green">{{=budget}}</strong><strong class="color-green"><i class="fa fa-check"></i></strong></p>
    <br>
    <label style="line-height:2.5;"><?php _e( 'You have to pay' , 'report-item-mobile' ); ?><br></label>
    <p class="text-credit-small">
        <?php _e( 'Budget' , 'report-item-mobile' ); ?> &nbsp; 
        <strong>{{= budget }}</strong>
    </p>
    <# if(commission){ #>
    <p class="text-credit-small"><?php _e( 'Commission' , 'report-item-mobile' ); ?> &nbsp;
        <strong style="color: #1faf67;">{{= commission }}</strong>
    </p>
    <# } #>
    <p class="text-credit-small"><?php _e( 'Total' , 'report-item-mobile' ); ?> &nbsp;
        <strong style="color:#e74c3c;">{{=total}}</strong>
    </p>
    <br>
</script>