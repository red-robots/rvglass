<?php
/* Template Name: Blog - list */

/**
 * Blog list layout.
 *
 * @package presscore
 * @since presscore 0.1
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $post;
$config = Presscore_Config::get_instance();
$config->set('template', 'blog');
$config->base_init();

add_action('presscore_before_main_container', 'presscore_page_content_controller', 15);

get_header(); ?>

		<?php if ( presscore_is_content_visible() ): ?>

			<!-- Content -->
			<div id="content" class="content" role="main">
				<header class="entry-header">
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header><!-- .entry-header -->

				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); // main loop ?>

					<?php do_action( 'presscore_before_loop' ); ?>

					<?php if ( !post_password_required() ) : ?>
						<?php the_content();?>
						&nbsp;
						<?php
						$ppp = get_post_meta($post->ID, '_dt_blog_options_ppp', true);
						$order = get_post_meta($post->ID, '_dt_blog_options_order', true);
						$orderby = get_post_meta($post->ID, '_dt_blog_options_orderby', true);
						$display = get_post_meta($post->ID, '_dt_blog_display', true);

						$blog_args = array(
							'post_type'	=> 'post',
							'post_status'	=> 'publish',
							'paged'		=> dt_get_paged_var(),
							'order'		=> $order,
							'orderby'	=> $orderby,
						);

						if ( $ppp ) {
							$blog_args['posts_per_page'] = intval($ppp);
						}

						if ( !empty($display['terms_ids']) ) {
							$terms_ids = array_values($display['terms_ids']);

							switch( $display['select'] ) {
								case 'only':
									$blog_args['category__in'] = $terms_ids;
									break;

								case 'except':
									$blog_args['category__not_in'] = $terms_ids;
							}

						}

						$blog_query = new WP_Query($blog_args);

						if ( $blog_query->have_posts() ): while( $blog_query->have_posts() ): $blog_query->the_post();
						?>

							<?php
							get_template_part( 'content', "blog" );
							?>

						<?php endwhile; wp_reset_postdata(); endif; ?>

						<?php dt_paginator($blog_query); ?>

					<?php else: ?>

						<?php the_content(); ?>

					<?php endif; ?>

					<?php do_action( 'presscore_after_loop' ); ?>

					<?php endwhile; ?>

				<?php endif; // main loop ?>

			</div><!-- #content -->

			<?php do_action('presscore_after_content'); ?>

		<?php endif; // if content visible ?>

<?php get_footer(); ?>