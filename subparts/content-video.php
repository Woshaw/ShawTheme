<?php
/**
 * <= The template part for displaying video fromat content =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <header class="entry-header">
        <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
    </header><!-- .entry-header -->

    <div class="entry-preview">
        <?php
        // Post thumbnail
        shawtheme_thumbnail();
        ?>
    </div><!-- .entry-preview -->

    <footer class="entry-footer">

        <?php
        // Post meta.
        shawtheme_entry_meta();

        if ( is_singular( 'post' ) ) {
            shawtheme_post_rights();
        }
        ?>

    </footer><!-- .entry-footer -->

</article><!-- .entry -->