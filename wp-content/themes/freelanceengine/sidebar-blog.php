<?php
/**
 * The Sidebar containing widget area on static page left side
 *
 * @package FreelanceEngine
 * @since 1.0
 */
?>

<?php if ( is_active_sidebar( 'sidebar-blog' ) ) : ?>
<div class="primary-sidebar widget-area" role="complementary">
	<?php dynamic_sidebar( 'sidebar-blog' ); ?>
</div><!-- #primary-sidebar -->
<?php endif; ?>

