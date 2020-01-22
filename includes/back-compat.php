<?php
/**
 * <= Back compat functionality =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

// Prevent switching to Shawtheme on old versions of WordPress.
function shawtheme_switch_theme() {
    switch_theme( WP_DEFAULT_THEME );
    unset( $_GET['activated'] );
    add_action( 'admin_notices', 'shawtheme_upgrade_notice' );
}
add_action( 'after_switch_theme', 'shawtheme_switch_theme' );

// Adds a message for unsuccessful theme switch.
function shawtheme_upgrade_notice() {
    printf( '<div class="error"><p>%s</p></div>', sprintf( __( 'This theme requires at least WordPress version 5.2. You are running version %s. Please upgrade and try again.', 'shawtheme' ), $GLOBALS['wp_version'] ) );
}

// Prevents the Customizer from being loaded on WordPress versions prior to 5.2.
function shawtheme_customize() {
    wp_die(
        sprintf( __( 'This theme requires at least WordPress version 5.2. You are running version %s. Please upgrade and try again.', 'shawtheme' ), $GLOBALS['wp_version'] ),
        '',
        array( 'back_link' => true )
    );
}
add_action( 'load-customize.php', 'shawtheme_customize' );

// Prevents the Theme Preview from being loaded on WordPress versions prior to 5.2.
function shawtheme_preview() {
    if ( isset( $_GET['preview'] ) ) {
        wp_die( sprintf( __( 'This theme requires at least WordPress version 5.2. You are running version %s. Please upgrade and try again.', 'shawtheme' ), $GLOBALS['wp_version'] ) );
    }
}
add_action( 'template_redirect', 'shawtheme_preview' );