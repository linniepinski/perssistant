	<?php
		get_template_part( 'mobile/template-js/post' , 'item' );
		get_template_part( 'mobile/template-js/project' , 'item' );
		get_template_part( 'mobile/template-js/user' , 'bid-item' );
		get_template_part( 'mobile/template-js/profile' , 'item' );
		get_template_part( 'mobile/template-js/portfolio' , 'item' );
		get_template_part( 'mobile/template-js/work-history', 'item' );
		get_template_part( 'mobile/template-js/skill' , 'item' );

		//if( is_page_template( 'page-profile.php' ) ){
			get_template_part( 'mobile/template-js/modal' , 'add-portfolio' );
		//}
		
		if(is_singular(PROJECT)){
			get_template_part( 'mobile/template-js/bid' , 'item' );
		}
		
		if(is_singular( PROJECT )) {
	        get_template_part( 'template-js/message' , 'item' );   
	        get_template_part( 'mobile/template-js/report' , 'item' );
	    }
		wp_footer();
	?>
	<!-- MODAL QUIT PROJECT-->
	<div class="modal fade" id="quit_project" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal">
	                    <i class="fa fa-times"></i>
	                </button>
	                <h4 class="modal-title alert-color"><?php _e("Awww! Why quit?", ET_DOMAIN) ?></h4>
	                <p class="alert-color">
	                    <?php _e("You're going to quit this project, you won't be able to access the workspace anymore.", ET_DOMAIN); ?>
	                </p>
	            </div>
	            <div class="modal-body">
	                <form role="form" id="quit_project_form" class="quit_project_form">
	                    <div class="form-group">
	                        <label for="user_login"><?php _e('Please give us a clear report', ET_DOMAIN) ?></label>
	                        <textarea name="comment_content"></textarea>
	                    </div>
	                    <div class="clearfix"></div>
	                    <div class="form-group">
	                        <button type="submit" class="btn btn-submit btn-sumary btn-sub-create">
	                            <?php _e('Quit', ET_DOMAIN) ?>
	                        </button>
	                    </div>
	                </form> 
	            </div>
	        </div><!-- /.modal-content -->
	    </div><!-- /.modal-dialog login -->
	</div><!-- /.modal -->
	<!--// MODAL QUIT PROJECT-->
	<script type="text/template" id="ae_carousel_template">
	    <li class="image-item" id="{{= attach_id }}">
	        <a href="#"><i class="fa fa-paperclip"></i> {{= name }}</a>
	        <a href="" title="<?php _e("Delete", ET_DOMAIN); ?>" class="delete-img delete"><i class="fa fa-times"></i></a>
	    </li>
	</script>
	</body>
</html>