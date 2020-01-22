<?php
/**
 * <= The main template file =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

get_header(); ?>

    <main id="main" class="site-main" role="main">

        <?php
            if ( have_posts() ) {

                //# Load posts loop.
                while ( have_posts() ) {
                    the_post();
                    get_template_part( 'subparts/content', get_post_format() ); // Include the Post-Format-specific template for the content.
                }

                //# Load posts pagination.
                the_posts_pagination(
                    array(
                        'end_size'           => 3,
                        'mid_size'           => 3,
                        'prev_text'          => __( 'Previous', 'shawtheme' ),
                        'next_text'          => __( 'Next', 'shawtheme' ),
                        'before_page_number' => '<span class="nav-meta screen-reader-text">' . __( 'No.', 'shawtheme' ) . '</span>',
                        'after_page_number'  => '<span class="nav-meta screen-reader-text">' . _x( 'page', 'Used after pagination page number.', 'shawtheme' ) . '</span>',
                    )
                );

            } else {

                get_template_part( 'subparts/content', 'none' ); // If no content, include the "No posts found" template.

            }
        ?>

    </main><!-- .site-main -->

    <?php get_sidebar();

get_footer(); ?>