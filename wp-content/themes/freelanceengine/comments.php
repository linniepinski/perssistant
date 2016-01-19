<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains comments and the comment form.
 *
 * @package FreelanceEngine
 * @since FreelanceEngine 1.0
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */

global $post;
if($post->post_type == PROJECT) {
    get_template_part('template/comment' , 'project');
    return ;
}

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area et-comments-area">

	<?php if ( have_comments() ) : ?>

	<h3 class="title et-comments-title">
		<?php 
			comments_number (
				__('0 Comments on this article', ET_DOMAIN), 
				__('1 Comment on this article', ET_DOMAIN), 
				__('% Comments on this article', ET_DOMAIN)
			);
		?>
	</h3>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-above" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Comment navigation', ET_DOMAIN ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', ET_DOMAIN ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', ET_DOMAIN ) ); ?></div>
	</nav><!-- #comment-nav-above -->
	<?php endif; // Check for comment navigation. ?>

	<ol class="comment-list">
		<?php
			wp_list_comments( array(
				'style'       => 'ul',
				'short_ping'  => true,
				'callback'    => 'fre_comment_callback',
			) );
		?>
	</ol><!-- .comment-list -->

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Comment navigation', ET_DOMAIN ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', ET_DOMAIN ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', ET_DOMAIN ) ); ?></div>
	</nav><!-- #comment-nav-below -->
	<?php endif; // Check for comment navigation. ?>

	<?php endif; // have_comments() ?>
	
	<?php if ( ! comments_open() ) : ?>
	<p class="no-comments et-comments-title">
		<?php _e( 'Comments are closed.', ET_DOMAIN ); ?>
	</p>
	<?php endif; ?>
	<div class="btm-comment-form">
		<?php 
			comment_form ( array(
							'comment_field'        => ' <div class="form-item"><label for="comment">' . __( 'Your Comment', ET_DOMAIN ) . '</label><div class="input"><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></div></div>',
							'comment_notes_before' => '',
							'comment_notes_after'  => '',
							'id_form'              => 'commentform',
							'id_submit'            => 'submit',
							'title_reply'          => __( "Comment", ET_DOMAIN),
							'title_reply_to'       => __( 'Leave a Reply to %s', ET_DOMAIN),
							'cancel_reply_link'    => __( 'Cancel reply',ET_DOMAIN ),
							'label_submit'         => __( 'Comment', ET_DOMAIN ),
					) );
		?>
	</div>

</div><!-- #comments -->

<?php
function fre_comment_callback( $comment, $args, $depth ){
    $GLOBALS['comment'] = $comment;
?>
    <li class="media et-comment" id="li-comment-<?php comment_ID();?>">
        <div id="comment-<?php comment_ID(); ?>">
            <a class="pull-left avatar-comment" href="#">
				<?php echo get_avatar( $comment->comment_author_email, 40 );?>
            </a>
            <div class="media-body">
                <h4 class="media-heading">
                <?php 
                    comment_author();
                ?>
                </h4>
                <div class="comment-text">
                	<?php comment_text(); ?>
                </div>
                <span class="time-review">
                	<i class="fa fa-clock-o"></i>
                	<time>
                		<?php echo ae_the_time( strtotime($comment->comment_date)); ?>
                	</time>
                </span>
                <?php 
                    comment_reply_link(array_merge($args, array(
						'reply_text' => __( '&nbsp;&nbsp;|&nbsp;&nbsp; Reply ', ET_DOMAIN ).'<i class="fa fa-edit"></i>',
						'depth'      => $depth,
						'max_depth'  => $args['max_depth'] 
                    )));
                ?>
            </div>
        </div>
<?php
}