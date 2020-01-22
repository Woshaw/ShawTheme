<?php
/**
 * <= The footer template file =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */
?>

        </div><!-- .site-content -->

        <footer id="colophon" class="site-footer" role="contentinfo">

            <div id="user-modal" class="site-modal user-modal">
                <header class="modal-header">
                    <h3 class="modal-title"><?php if ( is_user_logged_in() ) { _e( 'Manage Site', 'shawtheme' ); } else { _e( 'Login', 'shawtheme' ); } ?>
                        <button class="modal-toggle"><span class="screen-reader-text" aria-label="<?php esc_attr_e( 'Close', 'default' ); ?>">&times;</span></button>
                    </h3>
                </header>
                <div class="modal-body">
                    <?php if ( is_user_logged_in() ) {

                        $admin_options = array(
                            array( 
                                'link' => 'profile.php',
                                'text' => _x( 'Profile', 'Used in the admin list.', 'shawtheme' )
                            ),
                            array( 
                                'link' => 'themes.php',
                                'text' => _x( 'Theme', 'Used in the admin list.', 'shawtheme' )
                            ),
                            array( 
                                'link' => 'plugins.php',
                                'text' => _x( 'Plugin', 'Used in the admin list.', 'shawtheme' )
                            ),
                            array( 
                                'link' => 'edit.php',
                                'text' => _x( 'Post', 'Used in the admin list.', 'shawtheme' )
                            ),
                            array( 
                                'link' => 'edit.php?post_type=page',
                                'text' => _x( 'Page', 'Used in the admin list.', 'shawtheme' )
                            ),
                            array( 
                                'link' => 'edit-comments.php',
                                'text' => _x( 'Comment', 'Used in the admin list.', 'shawtheme' )
                            ),
                            array( 
                                'link' => 'edit-tags.php?taxonomy=category',
                                'text' => _x( 'Category', 'Used in the admin list.', 'shawtheme' )
                            ),
                            array( 
                                'link' => 'edit-tags.php?taxonomy=post_tag',
                                'text' => _x( 'Tag', 'Used in the admin list.', 'shawtheme' )
                            ),
                            array( 
                                'link' => 'upload.php',
                                'text' => _x( 'File', 'Used in the admin list.', 'shawtheme' )
                            ),
                            array( 
                                'link' => 'customize.php',
                                'text' => _x( 'Customize', 'Used in the admin list.', 'shawtheme' )
                            ),
                            array( 
                                'link' => 'export.php',
                                'text' => _x( 'Export', 'Used in the admin list.', 'shawtheme' )
                            ),
                            array( 
                                'link' => 'options-general.php',
                                'text' => _x( 'Setting', 'Used in the admin list.', 'shawtheme' )
                            ),
                        );

                        foreach( $admin_options as $value ) {
                            $list_items .= sprintf('<li><a href="%1$s">%2$s</a></li>', admin_url( $value['link'] ), $value['text'] );
                        }

                        printf( '<p class="loggedin-admin">%1$s</p><ul class="admin-list">%2$s</ul>',
                            sprintf( '%1$s<a href="%2$s">%3$s</a>, %4$s<a href="%5$s">%6$s</a>.',
                                __( 'Logged in as ', 'shawtheme' ),
                                wp_get_current_user()->user_url,
                                wp_get_current_user()->display_name,
                                __( 'Go to ', 'shawtheme' ),
                                admin_url(),
                                __( 'administration interface', 'shawtheme' )
                            ),
                            $list_items
                        );

                    } else {

                        wp_login_form( array(
                            'label_username' => __( 'Username:', 'shawtheme' ),
                            'label_password' => __( 'Password:', 'shawtheme' ),
                            'label_remember' => __( 'Remember my information.', 'shawtheme' ),
                            'value_remember' => true,
                        ));

                    } ?>
                </div><!-- .modal-body -->
                <footer class="modal-footer">
                    <?php if ( is_user_logged_in() ) {
                        printf( '<a href="%1$s" title="%2$s">%3$s</a>', wp_logout_url( home_url() ), __( 'Log out of this account', 'shawtheme' ), __( 'Log out?', 'shawtheme' ) );
                    } else {
                        printf( '<a rel="nofollow" href="%1$s">%2$s</a>', wp_lostpassword_url(), __( 'Lost your password?', 'shawtheme' ) );
                    } ?>
                </footer>
            </div><!-- .site-modal-->

            <p class="site-info">
                <span class="copyright">Copyright&nbsp;&copy;&nbsp;<?php echo date('Y'); ?>&nbsp;<span class="site-home"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span>&nbsp;<?php _e( 'All rights reserved.', 'shawtheme' ); ?></span>
                <?php
                    if ( get_option( 'zh_cn_l10n_icp_num' ) ) {
                        printf( '<span class="license"><a target="_blank" href="http://beian.miit.gov.cn/publish/query/indexFirst.action" rel="external nofollow">%s</a></span>', get_option( 'zh_cn_l10n_icp_num' ) );
                    }

                    the_privacy_policy_link( '<span class="privacy">', '</span>' );
                ?>
                <span class="administration"><a id="user-modal-toggle" class="modal-toggle" href="javascript:;"><?php _e( 'Admin', 'shawtheme' ); ?></a></span>
                <span class="statistics"><a target="_blank" href="https://new.cnzz.com/v1/login.php?siteid=1278197803" rel="external nofollow"><?php _e( 'Stats', 'shawtheme' ); ?></a></span>
            </p><!-- .site-info -->
        </footer><!-- .site-footer -->

        <?php wp_footer(); ?>
    </body>
</html>