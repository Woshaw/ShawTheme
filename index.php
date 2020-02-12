<?php
/**
 * <= The main template file =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

get_header(); ?>

    <main id="main" class="site-main" role="main">
        <?php

            // $queried_object = get_queried_object();
            // $this_id   = $queried_object->term_id;
            // $this_term = get_term( $this_id );
            // $the_tax = get_taxonomy( $queried_object->taxonomy );
            // if ( $this_term->parent != 0 ) {
            //     $term_name = get_term_parents_list( $this_id, $the_tax->name, array( 'separator' => $delimiter, 'inclusive' => false ) );
            //     $output  .= str_replace( '<a', '<a class="taxonomy-link"', $term_name );
            // }


            // if ( $queried_object ) {
            //     echo '<pre>';
            //     print_r($taxonomy);
            //     echo '</pre>';
            // }
        ?>

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