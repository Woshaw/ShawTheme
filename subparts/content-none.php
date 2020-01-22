<?php
/**
 * <= The template part for no posts found =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */
?>

<section class="<?php echo is_404() ? 'error-404' : 'no-results'; ?> not-found area">
    <header class="page-header">
        <h1 class="page-title"><?php _e( 'Nothing Found', 'shawtheme' ); ?></h1>
    </header><!-- .page-header -->

    <div class="page-content">
        <?php if( is_404() ) : ?>

            <p><?php _e( 'Sorry, the page you&rsquo;re looking for does not exist.', 'shawtheme' ); ?></p>

        <?php elseif ( is_search() ) : ?>

            <p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'shawtheme' ); ?></p>

        <?php elseif ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

            <p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'shawtheme' ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

        <?php else : ?>

            <p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'shawtheme' ); ?></p>

        <?php endif; ?>

            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">&laquo; <?php _e( 'Back to home', 'shawtheme' ); ?></a>

            || <a href="javascript: history.back(-1);"><?php _e( 'Go Back', 'shawtheme' ); ?></a> ||

            <a href="<?php echo esc_url( home_url( '/' ) ); ?>?s="><?php _e( 'Go to search', 'shawtheme' ); ?> &raquo;</a>

    </div><!-- .page-content -->
</section><!-- .no-results -->