<?php 
global $post, $user_ID;
$bid = get_post_meta( $post->ID, 'accepted', true );
$bid_author = get_post_field( 'post_author', $bid );
$bid_budget = get_post_meta( $bid, 'bid_budget', true );
$bid_author_name = get_the_author_meta( 'display_name', $bid_author );
?>
<!-- MODAL FINISH PROJECT-->
<div class="modal fade" id="modal_review" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title"><?php _e("Congratulation!", ET_DOMAIN) ?></h4>
			</div>
			<div class="modal-body">
			<form role="form" id="review_form" class="review-form">	
			<?php if($post->post_author == $user_ID) {  // employer finish project form ?>
				<input type="hidden" name="action" value="ae-employer-review" />
            	<label style="line-height:2.5;">

            		<?php _e( 'You are going to finish this project.' , ET_DOMAIN ); ?><br>
					<?php printf(__( 'Your payment will be sent when the freelancer confirms it has been done.' , ET_DOMAIN ), $bid_author_name ); ?>
				</label>
                <p>
                	<strong class="color-green"><?php echo fre_price_format($bid_budget); ?></strong>  
                	<strong class="color-green">
                		<i class="fa fa-check"></i>
                	</strong>
                </p>
                <div class="form-group">
                    <label for="post_content"><?php printf(__('Rate for "%s"' ,ET_DOMAIN), $bid_author_name); ?> </label>
                    <div class="rating-it" style="cursor: pointer;"> <input type="hidden" name="score" > </div>
				</div>
				<div class="form-group">
				 	<label for="user_login">
						<?php printf(__('Your review about %s', ET_DOMAIN), $bid_author_name); ?>
					</label>
					<textarea name="comment_content" ></textarea>
				</div>
                <div class="form-group">
                    <button type="submit" class="btn btn-ok">
						<?php _e('Finish', ET_DOMAIN) ?>
                    </button>
				</div>
				
			 <?php }else { // freelancer finish project form
			 	$employer_name = get_the_author_meta( 'display_name', $post->post_author );
			  ?>
			  	<input type="hidden" name="action" value="ae-freelancer-review" />
            	<label style="line-height:2.5;">
            		<?php _e( 'You are going to finish this project.' , ET_DOMAIN ); ?><br>
					<?php printf(__( '%s has finish project. You will receive payment after you review him.' , ET_DOMAIN ), $employer_name); ?>
				</label>
                <p>
                	<strong class="color-green"><?php echo fre_price_format($bid_budget); ?></strong>
                	<strong class="color-green">
                		<i class="fa fa-check"></i>
                	</strong>
                </p>
                <div class="form-group">
                    <label for="post_content"><?php printf(__('Rate for "%s"',ET_DOMAIN),$employer_name); ?> </label>
                    <div class="rating-it" style="cursor: pointer;"> <input type="hidden" name="score" > </div>

				</div>
				<div class="form-group">
				 	<label for="user_login">
						<?php printf(__('Your review about %s', ET_DOMAIN), $employer_name); ?>
					</label>
					<textarea name="comment_content"></textarea>
				</div>
                <div class="form-group">
                    <button type="submit" class="btn btn-ok">
						<?php _e('Finish', ET_DOMAIN) ?>
                    </button>
				</div>
				<?php } ?>
				</form>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!-- MODAL FINISH PROJECT-->


<?php return; ?>
<!-- MODAL BIG -->
<div class="modal fade" id="modal_review">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">				
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title">

				<?php 
				if($status =='complete')
					_e('Rate project and post your review here',ET_DOMAIN); 
				else  
					_e('Complete project and post your review here',ET_DOMAIN);
				?></h4>
			</div>
			<div class="modal-body">

				<form role="form" id="review_form" class="review-form">				
                	<div class="form-group rate">
                    	
                    	<label for="post_content"><?php _e('Rate for this profile',ET_DOMAIN);?> </label>
                        <div class="rating-it" style="cursor: pointer;"> <input type="hidden" name="score" > </div>
					</div>
                    <div class="clearfix"></div>
                    <div class="form-group">
                    	
                    	<label for="post_content"><?php _e('Message review profile ',ET_DOMAIN); ?></label>
                        <?php wp_editor( '', 'comment_content', ae_editor_settings() );  ?>
					</div>                  
					<input type="hidden" name="project_id" value="<?php the_ID(); ?>" />					
					<?php if($status =='complete'){?>						
						<input type="hidden" name="action" value="ae-freelancer-review" />
						<?php 
					} else { ?>					
						<input type="hidden" name="action" value="ae-employer-review" />
					<?php } ?>

					<?php do_action('after_review_form'); ?>	
                    <div class="clearfix"></div>
					<button type="submit" class="btn-submit btn-sumary btn-sub-create">
						<?php _e('Submit', ET_DOMAIN) ?>
					</button>
				</form>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->