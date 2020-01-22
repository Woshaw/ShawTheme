<?php
/**
 * <= The attachment template file =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

get_header(); ?>

	<main id="main" class="site-main" role="main">

		<?php
		// Start the loop.
		while ( have_posts() ) : the_post(); ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
				<div class="entry-content">
					<?php if ( wp_attachment_is_image() ) : ?>
						<figure class="entry-attachment wp-block-image">
							<?php
								// Filter the default image attachment size.
								$image_size = apply_filters( 'shawtheme_attachment_size', 'full' );
								echo wp_get_attachment_image( get_the_ID(), $image_size );

								// Image caption
								shawtheme_excerpt();
							?>
						</figure><!-- .entry-attachment -->

						<nav id="image-navigation" class="navigation image-navigation">
							<div class="nav-links">
								<div class="nav-previous"><?php previous_image_link( false, __( 'Previous Image', 'shawtheme' ) ); ?></div>
								<div class="nav-next"><?php next_image_link( false, __( 'Next Image', 'shawtheme' ) ); ?></div>
							</div><!-- .nav-links -->
						</nav><!-- .image-navigation -->
					<?php endif; ?>

					<?php
						the_content();
						wp_link_pages( array(
							'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'shawtheme' ) . '</span>',
							'after'       => '</div>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
							'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'shawtheme' ) . ' </span>%',
							'separator'   => '<span class="screen-reader-text">, </span>',
						));
					?>
				</div><!-- .entry-content -->

				<footer class="entry-footer">

					<?php shawtheme_entry_meta(); ?>

				</footer><!-- .entry-footer -->

			</article><!-- .entry -->

			<?php
			// Parent post navigation.
			the_post_navigation(
				array(
					'prev_text' => _x( '<span class="nav-meta">Published in</span><b class="post-title">%title</b>', 'Parent post link', 'shawtheme' ),
				)
			);

			// Check whether to load up the comment template or not.
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}

		// End the loop.
		endwhile; ?>

	</main><!-- .site-main -->

	<?php get_sidebar();

get_footer(); ?>