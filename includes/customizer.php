<?php
/**
 * <= Customizer functionality =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

/*****************************
 * Theme customizer supports *
 *****************************/
function shawtheme_customizer_supports() {
    // Enable support for custom logo, add header text display toggler.
    add_theme_support(
        'custom-logo', array(
            'width'       => 100,
            'height'      => 100,
            'header-text' => array( 'site-meta', 'site-title', 'site-tagline' )
        )
    );

    // Enable support for custom header.
    add_theme_support(
        'custom-header', 
        apply_filters(
            'shawtheme_custom_header_args',
            array(
                'default-image'          => get_parent_theme_file_uri( '/assets/img/header_image.jpg' ),
                'width'                  => 1200,
                'height'                 => 300,
                'random-default'         => true,
                'header-text'            => false,
                'video'                  => true
            )
        )
    );
}
add_action( 'after_setup_theme', 'shawtheme_customizer_supports' );

/*****************************
 * Theme customizer settings *
 *****************************/
function shawtheme_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'shawtheme_settings', array(
        'title'    => __( 'ShawTheme Settings', 'shawtheme' ),
        'priority' => 30
    ) );

    $wp_customize->add_setting( 'stereoscopic_option', array(
        'default' => true,
        'type'    => 'theme_mod'
    ) );

    $wp_customize->add_control( 'stereoscopic_option', array(
        'label'    => __( 'Turn on theme stereoscopic.', 'shawtheme' ),
        'section'  => 'shawtheme_settings',
        'priority' => 30,
        'type'     => 'checkbox'
    ) );
}
add_action( 'customize_register', 'shawtheme_customize_register', 11 );