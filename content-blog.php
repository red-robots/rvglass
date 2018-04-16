<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package presscore
 * @since presscore 1.0
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h2 class="entry-title"><?php the_title(); ?></h2>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_excerpt(); ?>
		<?php edit_post_link( __( 'Edit', LANGUAGE_ZONE ), '<span class="edit-link">', '</span>' ); ?>
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
