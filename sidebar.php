<?php
/**
 * <= The sidebar template file =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

if ( is_archive() || is_search() ) : ?>

    <aside id="page-minor" class="sidebar sidebar-flow sidebar-plural" role="complementary">
        <?php shawtheme_custom_sidebar(); ?>
    </aside><!-- .sidebar-plural -->

<?php elseif ( is_active_sidebar( 'single-sidebar' ) && is_singular( 'post' ) ) : ?>

    <aside id="post-minor" class="sidebar sidebar-flow sidebar-single widget-area" role="complementary">
        <?php dynamic_sidebar( 'single-sidebar' ); ?>
    </aside><!-- .sidebar-single -->

<?php endif; ?>

<aside id="supply" class="sidebar sidebar-global" role="complementary">
    <header class="sidebar-header">
        <h2 class="sidebar-title">
            <span class="screen-reader-text"><?php _e( 'Global sidebar', 'shawtheme' ); ?></span>
            <button id="sidebar-toggle-close" class="sidebar-toggle"><span class="screen-reader-text" aria-label="<?php esc_attr_e( 'Close', 'default' ); ?>">&times;</span></button>
        </h2>
    </header>

    <div id="sidebar-body" class="sidebar-body">
        <section class="author-info">
            <div class="author-profile">
                <h3 class="author-vcard">
                    <span class="screen-reader-text"><?php _e( 'Author: ', 'shawtheme' ); ?></span>
                    <a class="author-avatar" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo get_avatar( 1, 100, $default, __( 'Author&rsquo;s avatar', 'shawtheme' ), array( 'force_display' => true, ) ); ?></a>
                    <span class="author-name"><a class="author-link" href="<?php the_author_meta( 'user_url', 1); ?>"><?php the_author_meta( 'display_name', 1 ); ?></a></span>
                </h3>
                <p class="author-description">
                    <span class="screen-reader-text"><?php _e( 'Description: ', 'shawtheme' ); ?></span>
                    <?php the_author_meta( 'description', 1 ); ?>
                </p>
                <ul class="counts-list">
                    <?php
                        $post_types = get_post_types( array( 'public' => true, 'exclude_from_search'=> false ), 'objects' );
                        foreach ( $post_types as $post_type ) {
                            $type   = $post_type->name;
                            $url    = get_post_type_archive_link( $type );
                            $url    = $url ? $url : home_url( '/' );
                            $labels = get_post_type_labels( $post_type );
                            if ( $type === 'page' || $type === 'attachment' ) { // 排除“页面”和“媒体”内容类型
                               continue;
                            }
                            printf( '<li>%1$s: <a href="%2$s">%3$s</a></li>',
                                sprintf( __( 'Total number of %1$s', 'shawtheme' ), esc_html( $labels->name ) ),
                                esc_url( $url ),
                                count_user_posts( 1, $type, true )
                            );
                        }
                    ?>
                </ul>
                <a class="profile-link" href="<?php echo esc_url( get_author_posts_url( 1 ) ); ?>" rel="author"><?php _e( 'View Profile', 'shawtheme' ); ?></a>
            </div><!-- .author-profile -->

            <?php if ( has_nav_menu( 'social' ) ) : ?>
                <nav class="navigation social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Social Links Menu', 'shawtheme' ); ?>">
                    <?php
                        wp_nav_menu(
                            array(
                                'theme_location' => 'social',
                                'menu_class'     => 'social-links-menu',
                                'depth'          => 1,
                                'link_before'    => '<span class="screen-reader-text">',
                                'link_after'     => '</span>',
                            )
                        );
                    ?>
                </nav><!-- .social-navigation -->
            <?php endif; ?>
        </section><!-- .author-info -->

        <?php if ( is_active_sidebar( 'global-sidebar' ) ) : ?>
            <div class="widget-area">
                <?php dynamic_sidebar( 'global-sidebar' ); ?>
            </div><!-- .widget-area -->
        <?php endif; ?>
    </div><!-- .sidebar-body -->
</aside><!-- .sidebar-global -->