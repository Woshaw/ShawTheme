<?php
/**
 * <= The page template file =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

get_header(); ?>


    <main id="main" class="site-main" role="main">

        <?php
        //# Start the loop.
        while ( have_posts() ) :

            the_post();

            // Pages list navigation.
            shawtheme_list_pages( array( 'tag' => 'nav' ) );

            // Include the page content template.
            get_template_part( 'subparts/content', 'page' );

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