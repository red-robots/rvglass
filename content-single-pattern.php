<?php
/**
 * @package presscore
 * @since presscore 0.1
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $post;

// thumbnail visibility
$hide_thumbnail = (bool) get_post_meta($post->ID, '_dt_post_options_hide_thumbnail', true);
add_filter( 'presscore_post_navigation-args', 'presscore_show_navigation_next_prev_posts_titles', 15 );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class("template-single-pattern"); ?>>

	<?php do_action('presscore_before_post_content'); ?>
	<h1><?php the_title();?></h1>
	<?php if ( !post_password_required() ) : ?>

		<?php $manufacturer = (types_render_field( 'manufacturer'));
		$model = (types_render_field( 'model'));
		$year = (types_render_field( 'year'));
		$window_location = (types_render_field( 'window-location'));
		$dimensions = (types_render_field( 'dimensions'));
		$image = (types_render_field( 'image'));?>

		<?php the_content(); ?>

	<?php else: ?>

		<?php the_content(); ?>

	<?php endif; // !post_password_required ?>

	<?php do_action('presscore_after_post_content'); ?>

</article><!-- #post-<?php the_ID(); ?> -->
<?php remove_filter( 'presscore_post_navigation-args', 'presscore_show_navigation_next_prev_posts_titles', 15 ); ?>
