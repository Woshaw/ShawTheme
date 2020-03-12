<?php
/**
 * <= The  single-post template file =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

get_header(); ?>

    <main id="main" class="site-main" role="main">

        <?php
        // $the_type  = $post->post_type;
        // $the_obj   = get_post_type_object( $the_type );
        // $the_taxs  = $the_obj->taxonomies;
        // foreach ( $the_taxs as $the_tax ) {
        //     $tax = get_taxonomy( $the_tax );
        //     if ( $tax->hierarchical ) { 
        //         $taxonomy = $the_tax;
        //         break;
        //     }
        // }
        // $terms      = get_the_terms( $post->ID, $taxonomy );
        // $the_terms  = get_term_parents_list( $terms[0]->term_id, $terms[0]->taxonomy, array( 'format' => 'slug', 'link' => false ) );
        // $post_terms = str_replace( $the_type . '/', '', substr( $the_terms, 0, -1 ) );

        // echo "<pre>";
        // print_r($post_terms);
        // echo "</pre>";


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