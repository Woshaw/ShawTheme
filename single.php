<?php
/**
 * <= The  single-post template file =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

get_header(); ?>

    <main id="main" class="site-main" role="main">

        <?php
        //# Start the loop.
        while ( have_posts() ) : the_post();

            // Include the single post content template.
            get_template_part( 'subparts/content', 'single' );

            if ( is_singular( 'post' ) ) {
                // Previous/next post navigation.
                the_post_navigation(
                    array(
                        'prev_text' => '<span class="nav-meta">' . _x( 'Previous', 'Used in post navigation.', 'shawtheme' ) . '</span> <b class="post-title">%title</b>',
                        'next_text' => '<span class="nav-meta">' . _x( 'Next', 'Used in post navigation.', 'shawtheme' ) . '</span> <b class="post-title">%title</b>',
                        // 'in_same_term' => true,
                        // 'taxonomy'     => '',
                        // 'screen_reader_text' => __( 'Post navigation', 'shawtheme' ),
                        // 'aria_label'   => __( 'Post', 'shawtheme' )
                    )
                );
            }

            // Check whether to load up the comment template or not.
            if ( comments_open() || get_comments_number() ) {
                comments_template();
            }

        //# End the loop.
        endwhile;
        ?>

    </main><!-- .site-main -->

    <?php get_sidebar();

get_footer(); ?>