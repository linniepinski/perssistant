<?php 
global $post, $user_ID;
$bid = get_post_meta( $post->ID, 'accepted', true );
$bid_author = get_post_field( 'post_author', $bid );
$bid_budget = get_post_meta( $bid, 'bid_budget', true );
$bid_author_name = get_the_author_meta( 'display_name', $bid_author );
$project = get_post($post->post_parent);
?>
<!-- MODAL FINISH PROJECT-->
<div class="modal fade" id="modal_review" role="dialog" aria-labelledby="modal_review" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title"><?php _e("Congratulation!", 'modal-review') ?></h4>
			</div>
			<div class="modal-body">
			<form id="review_form" class="review-form">
			<?php if($project->post_author == $user_ID) {  // employer finish project form ?>
				<input type="hidden" name="action" value="ae-employer-review" />
            	<label style="line-height:2.5;">

            		<?php _e( 'You are going to finish this project.' , 'modal-review' ); ?><br>
					<?php printf(__( 'Your payment will be sent when the freelancer confirms it has been done.' , 'modal-review' ), $bid_author_name ); ?>
				</label>
                <p>
                	<strong class="color-green"><?php echo fre_price_format($bid_budget); ?></strong>  
                	<strong class="color-green">
                		<i class="fa fa-check"></i>
                	</strong>
                </p>
                <div class="form-group">
                    <label for="post_content"><?php printf(__('Rate for "%s"' ,'modal-review'), $bid_author_name); ?> </label>
                    <div class="rating-it" style="cursor: pointer;"> <input type="hidden" name="score" > </div>
					<div class="row">
						<div class="col-xs-12 col-sm-8 col-md-6">
							<?php
							$args = array(
								'hide_empty' => false
							);
							$list_review_items =  get_terms('rating_taxonomy' , $args);
							if ( ! empty( $list_review_items ) && ! is_wp_error( $list_review_items ) ){
								echo '<ul class="rating-list">';
								foreach ( $list_review_items as $term ) {
									echo '<li>';
									echo '<label attr-slug="'. $term->slug.'">' . $term->name . '</label>';
									echo '<div class="rating-it_2" style="cursor: pointer;"></div>';
									echo '</li>';
								}
								echo '</ul>';
							}
							?>
						</div>
					</div>
				</div>
				<div class="form-group">
				 	<label for="user_login">
						<?php printf(__('Your review about %s', 'modal-review'), $bid_author_name); ?>
					</label>
					<textarea name="comment_content" ></textarea>
				</div>
                <div class="form-group">
                    <button type="submit" class="btn btn-ok">
						<?php _e('Finish', 'modal-review') ?>
                    </button>
				</div>
				
			 <?php } else { // freelancer finish project form
			 	$employer_name = get_the_author_meta( 'display_name', $project->post_author );
			  ?>
			  	<input type="hidden" name="action" value="ae-freelancer-review" />
            	<label style="line-height:2.5;">
            		<?php _e( 'You are going to finish this project.' , 'modal-review' ); ?><br>
					<?php printf(__( '%s has finish project. You will receive payment after you review him.' , 'modal-review' ), $employer_name); ?>
				</label>
                <p>
                	<strong class="color-green"><?php echo fre_price_format($bid_budget); ?></strong>
                	<strong class="color-green">
                		<i class="fa fa-check"></i>
                	</strong>
                </p>
                <div class="form-group">
                    <label for="post_content"><?php printf(__('Rate for "%s"','modal-review'),$employer_name); ?> </label>
					<div class="rating-it" style="cursor: pointer;"> <input type="hidden" name="score" > </div>
					<div class="row">
						<div class="col-xs-12 col-sm-8 col-md-6">
							<?php
							$args = array(
								'hide_empty' => false
							);
							$list_review_items =  get_terms('rating_taxonomy' , $args);
							//				var_dump($list_review_items);
							if ( ! empty( $list_review_items ) && ! is_wp_error( $list_review_items ) ){
								echo '<ul class="rating-list">';
								foreach ( $list_review_items as $term ) {
									echo '<li>';
									echo '<label attr-slug="'. $term->slug.'">' . $term->name . '</label>';
									echo '<div class="rating-it_2" style="cursor: pointer;"></div>';
									echo '</li>';
								}
								echo '</ul>';
							}
							?>
						</div>
					</div>

				</div>
				<div class="form-group">
				 	<label for="user_login">
						<?php printf(__('Your review about %s', 'modal-review'), $employer_name); ?>
					</label>
					<textarea name="comment_content"></textarea>
				</div>
                <div class="form-group">
                    <button type="submit" class="btn btn-ok">
						<?php _e('Finish', 'modal-review') ?>
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
					_e('Rate project and post your review here','modal-review'); 
				else  
					_e('Complete project and post your review here','modal-review');
				?></h4>
			</div>
			<div class="modal-body">

				<form id="review_form" class="review-form">
                	<div class="form-group rate">
                    	
                    	<label for="post_content"><?php _e('Rate for this profile','modal-review');?> </label>
                        <div class="rating-it" style="cursor: pointer;"> <input type="hidden" name="score" > </div>
					</div>
                    <div class="clearfix"></div>
                    <div class="form-group">
                    	
                    	<label for="post_content"><?php _e('Message review profile ','modal-review'); ?></label>
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
						<?php _e('Submit', 'modal-review') ?>
					</button>
				</form>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->