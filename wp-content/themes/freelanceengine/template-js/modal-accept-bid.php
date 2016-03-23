<div class="modal fade" id="acceptance_project" tabindex="-1" role="dialog" aria-labelledby="acceptance_project" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title">
					<?php _e("Bid acceptance", 'modal-accept-bid') ?>
				</h4>
			</div>
			<div class="modal-body">
				<form id="escrow_bid" class="">
					<div class="escrow-info">
		            	<!-- bid info content here -->
	                </div>
					<div class="form-group">
	                    <button type="submit" class="btn-submit btn-sumary btn-sub-create">
	                        <?php _e('Accept Bid', 'modal-accept-bid') ?>
	                    </button>
	                </div>
	            </form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!-- MODAL BID acceptance PROJECT-->
<script type="text/template" id="bid-info-template">
	<label style="line-height:2.5;"><?php _e( 'You are about to accept this bid for' , 'modal-accept-bid' ); ?></label>
	<p><strong class="color-green">{{=budget}}</strong><strong class="color-green"><i class="fa fa-check"></i></strong></p>
	<br>
	<label style="line-height:2.5;"><?php _e( 'You have to pay' , 'modal-accept-bid' ); ?><br></label>
	<p class="text-credit-small">
		<?php _e( 'Budget' , 'modal-accept-bid' ); ?> &nbsp;
		<strong>{{= budget }}</strong>
	</p>
	<# if(commission){ #>
	<p class="text-credit-small"><?php _e( 'Commission' , 'modal-accept-bid' ); ?> &nbsp;
		<strong style="color: #1faf67;">{{= commission }}</strong>
	</p>
	<# } #>
	<p class="text-credit-small"><?php _e( 'Total' , 'modal-accept-bid' ); ?> &nbsp;
		<strong style="color:#e74c3c;">{{=total}}</strong>
	</p>
	<br>
</script>