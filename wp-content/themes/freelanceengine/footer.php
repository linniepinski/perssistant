<?php
wp_reset_query();
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */
?>
<?php 
if( is_active_sidebar( 'fre-footer-1' )    || is_active_sidebar( 'fre-footer-2' ) 
    || is_active_sidebar( 'fre-footer-3' ) || is_active_sidebar( 'fre-footer-4' )
    )
{$flag=true; ?>
<!-- FOOTER -->
<footer> 
	<div class="container">
    	<div class="row">
            <div class="col-md-3 col-sm-6">
                <?php if( is_active_sidebar( 'fre-footer-1' ) ) dynamic_sidebar( 'fre-footer-1' );?>
            </div>
            <div class="col-md-3 col-sm-6">
                <?php if( is_active_sidebar( 'fre-footer-2' ) ) dynamic_sidebar( 'fre-footer-2' );?>
            </div>
            <div class="col-md-3 col-sm-6">
                <?php if( is_active_sidebar( 'fre-footer-3' ) ) dynamic_sidebar( 'fre-footer-3' );?>
            </div>
            <div class="col-md-3 col-sm-6">
                <?php if( is_active_sidebar( 'fre-footer-4' ) ) dynamic_sidebar( 'fre-footer-4' );?>
            </div>
        </div>
    </div>
</footer>
<?php }else{ $flag = false;} ?>   
<div class="copyright-wrapper <?php if(!$flag){ echo 'copyright-wrapper-margin-top'; } ?> ">
<?php 
    $copyright = ae_get_option('copyright');
    $has_nav_menu = has_nav_menu( 'et_footer' );
    $col = 'col-md-6';
    if($has_nav_menu) {
        $col = 'col-md-4';
    }
?>
	<div class="container">
        <div class="row">
            <div class="<?php echo $col ?> col-sm-4">
            	<a href="<?php echo home_url(); ?>" class="logo-footer"><?php fre_logo('site_logo_white') ?></a>
            </div>
            <?php if($has_nav_menu){ ?>
            <div class="col-md-4 col-sm-4">
                <?php
                    wp_nav_menu( array('theme_location' =>'et_footer') );
                ?>
            </div>
            <?php }?>
            <div class="<?php echo $col;?> col-sm-4">
            	<p class="text-copyright">
                    <?php 
                        if($copyright){ echo $copyright; } 
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER / END -->

<?php
    
    if(!is_page_template( 'page-auth.php' )){
    	/* ======= modal register template ======= */ 
    	//get_template_part( 'template-js/modal' , 'register' );
    	/* ======= modal register template / end  ======= */
    	/* ======= modal register template ======= */ 
    	get_template_part( 'template-js/modal' , 'login' );
    	/* ======= modal register template / end  ======= */
        
        /* ======= modal forgot pass template ======= */ 
        get_template_part( 'template-js/modal' , 'forgot-pass' );
        /* ======= modal forgot pass template / end  ======= */
    }

	if(is_page_template( 'page-profile.php' )){
    	/* ======= modal add portfolio template ======= */ 
    	get_template_part( 'template-js/modal' , 'add-portfolio' );
    	get_template_part( 'template-js/modal' , 'edit-portfolio' );
    	/* ======= modal add portfolio template / end  ======= */
	}
	/* ======= modal change password template ======= */ 
	get_template_part( 'template-js/modal' , 'change-pass' );
	/* ======= modal change password template / end  ======= */
	
	/* ======= modal change password template ======= */ 
	get_template_part( 'template-js/modal' , 'add-cv' );
	/* ======= modal change password template / end  ======= */

	get_template_part( 'template-js/post' , 'item' );
	get_template_part( 'template-js/project' , 'item' );
	get_template_part( 'template-js/user' , 'bid-item' );
	get_template_part( 'template-js/profile' , 'item' );
	get_template_part( 'template-js/portfolio' , 'item' );
	get_template_part( 'template-js/work-history', 'item' );
	get_template_part( 'template-js/skill' , 'item' );

	if(is_singular('project')){

		get_template_part( 'template-js/bid' , 'item' );        
        get_template_part( 'template-js/modal' , 'review');   
        get_template_part( 'template-js/modal' , 'bid' );
        get_template_part( 'template-js/modal' , 'bid-update' );
        get_template_part( 'template-js/modal' , 'accept-bid' );
              
	}
    
    if(is_author()){
        get_template_part( 'template-js/author-project' , 'item' );
    }
	//print modal contact template 
	if( is_singular( PROFILE ) || is_author() ){
		get_template_part( 'template-js/modal' , 'contact' );
        /* ======= modal invite template ======= */ 
        get_template_part( 'template-js/modal' , 'invite' );
	}
	
	/* ======= modal invite template / end  ======= */

    // modal edit project
    if( (get_query_var( 'author' ) == $user_ID && is_author() )
        ||  current_user_can('manage_options') || is_post_type_archive(PROJECT) 
        || is_page_template('page-profile.php') || is_singular( PROJECT )
    ){
        get_template_part( 'template-js/modal' , 'edit-project' );
        get_template_part( 'template-js/modal' , 'reject' );
    }

    if(is_singular( PROJECT )) {
        get_template_part( 'template-js/message' , 'item' );   
        get_template_part( 'template-js/report' , 'item' );   
    }

	wp_footer(); 
?>
<script type="text/template" id="ae_carousel_template">
    <li class="image-item" id="{{= attach_id }}">
        <a href="#"><i class="fa fa-paperclip"></i> {{= name }}</a>
        <a href="" title="<?php _e("Delete", ET_DOMAIN); ?>" class="delete-img delete"><i class="fa fa-times"></i></a>
    </li>
</script>
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
                        <button type="submit" class="btn-submit btn-sumary btn-sub-create">
                            <?php _e('Quit', ET_DOMAIN) ?>
                        </button>
                    </div>
                </form> 
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!--// MODAL QUIT PROJECT-->


<!-- MODAL CLOSE PROJECT-->
<div class="modal fade" id="close_project_success" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="content-close-wrapper">
                    <p class="alert-close-text">
                        <?php _e("We will review the reports from both freelancer and employer to give the best decision. It will take 3-5 business days for reviewing after receiving two reports.", ET_DOMAIN) ?> 
                    </p>  
                    <button type="submit" class="btn btn-ok">
                        <?php _e('OK', ET_DOMAIN) ?>
                    </button> 
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!--// MODAL CLOSE PROJECT-->
<?php 
    $pagename = $post->post_name; 
    
    if ($pagename == 'home' || $pagename == 'contact') {
?>
<!--Start of Zopim Live Chat Script-->
<script type="text/javascript">
    window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
    d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
    _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
    $.src="//v2.zopim.com/?3GhVUmqfXb6qmaZjlfjf6OOeJoj0kg2i";z.t=+new Date;$.
    type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
</script>
<!--End of Zopim Live Chat Script-->
<?php
    }
?>
<script> 
    jQuery(document).ready(function () {

        new WOW().init();

        var xx= jQuery('#video-background-wrapper').height()-65;
        jQuery('.d-arrow').on('click',function () {
            jQuery('html, body').animate({scrollTop: xx}, 500);
        })
   
    })
</script>

<script type="text/javascript">
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new
    Date();a=s.createElement(o),

    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js
    ','ga');

    ga('create', 'UA-61047471-3', 'auto');
    ga('send', 'pageview');
</script>

</body>
</html>