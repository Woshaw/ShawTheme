<?php
/**
 * <= The template part for displaying page content =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="entry-content">
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

        <?php
        // Post meta.
        shawtheme_entry_meta();
        ?>

    </footer><!-- .entry-footer -->

</article><!-- .entry -->