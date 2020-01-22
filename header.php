<?php
/**
 * <= The header template file =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */
?>

<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
        <?php wp_body_open(); ?>

        <header id="masthead" class="site-header" role="banner">
            <div id="site-navbar" class="site-navbar">
                <div class="site-brand">
                    <?php the_custom_logo(); ?>

                    <div class="site-meta">
                        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>

                        <?php if ( get_bloginfo( 'description', 'display' ) || is_customize_preview() ) : ?>
                            <p class="site-tagline"><?php bloginfo( 'description' ); ?></p>
                        <?php endif; ?>
                    </div><!-- .site-meta -->
                </div><!-- .site-brand -->

                <?php if ( has_nav_menu( 'primary' ) ) : ?>
                    <nav id="site-navigation" class="navigation main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'shawtheme' ); ?>">
                        <?php
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'primary',
                                    'menu_class' => 'primary-menu',
                                )
                            );
                        ?>
                    </nav><!-- .main-navigation -->
                <?php endif; ?>
                
                <div id="site-search" class="site-search">
                    <?php get_search_form(); ?>
                </div><!-- .site-search -->

                <a id="search-toggle" class="search-toggle" href="<?php echo esc_url( home_url( '/' ) ); ?>?s=" title="<?php esc_attr_e( 'Search', 'shawtheme' ); ?>" rel="search"><span class="screen-reader-text"><?php _e( 'Go to search', 'shawtheme' ); ?></span></a>

                <a id="sidebar-toggle-open" class="sidebar-toggle" href="#supply" title="<?php esc_attr_e( 'sidebar', 'shawtheme' ); ?>"><span class="screen-reader-text"><?php _e( 'Go to sidebar', 'shawtheme' ); ?></span></a>
            </div><!-- .site-navbar -->

            <?php shawtheme_headline(); ?>
        </header><!-- .site-header -->

        <div id="content" class="site-content">
            <?php // site breadcrumbs navigation.
            shawtheme_breadcrumbs(); ?>