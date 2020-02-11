<?php
/**
 * <= Functions and definitions =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

/* === Table of Contents ===
 * 01. Required Files
 * 02. Theme Setup
 * 03. System Reset
 * 04. Core Register
 * 05. Enqueue Assets
 * 06. Custom Outputs
 * 07. Custom Classes
 * 08. 
 * 09. Modifies
 * 10. 
 * === Table of Contents === */

/**********************
 * 01. Required Files *
 **********************/
// Shawtheme only works in WordPress 5.2 or later.
if ( version_compare( $GLOBALS['wp_version'], '5.2', '<' ) ) {
    require get_template_directory() . '/includes/back-compat.php';
    return;
}

require get_template_directory() . '/includes/customizer.php';

require get_template_directory() . '/includes/function-tags.php';

require get_template_directory() . '/includes/class-comment-walker.php';

/*******************
 * 02. Theme Setup *
 *******************/
function shawtheme_setup() {
    // Setup theme textdomain for translation.
    load_theme_textdomain( 'shawtheme', get_template_directory() . '/languages' );

    // Add the document title to head.
    add_theme_support( 'title-tag' );

    // Add default posts and comments RSS feed links to head.
    add_theme_support( 'automatic-feed-links' );

    // Register navigation menus.
    register_nav_menus(
        array(
            'primary' => __( 'Primary Menu', 'shawtheme' ),
            'social'  => __( 'Social Links Menu', 'shawtheme' )
        )
    );

    // Enable support for Post Thumbnails and set image sizes.
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 800, 500, true );
    add_image_size( 'header-thumbnail', 1200, 300, true );
    add_image_size( 'entry-thumbnail', 600, 150, true );

    // Enable support for Post Formats.
    add_theme_support(
        'post-formats', array(
            'aside',
            'status',
            'image',
            'gallery',
            'audio',
            'video',
            'link',
        )
    );

    // Switch default core markup to output valid HTML5.
    add_theme_support(
        'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'script',
            'style',
        )
    );
}
add_action( 'after_setup_theme', 'shawtheme_setup' );

/********************
 * 03. System Reset *
 ********************/
//# Remove the unneeded tags in the document head.
add_filter( 'wp_resource_hints', function( $hints, $relation_type ) { if ( 'dns-prefetch' === $relation_type ) {
        return array_diff( wp_dependencies_unique_hosts(), $hints );
    }
    return $hints;
}, 10, 2 ); // Disable DNS prefetch(s.w.org).
// remove_action( 'wp_head', 'feed_links', 2 ); // Disable feed supports.
remove_action( 'wp_head', 'feed_links_extra', 3 ); // Disable single post feed.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); // Remove emoji related script.
remove_action( 'wp_print_styles', 'print_emoji_styles' ); // Remove emoji related style.
remove_action( 'wp_head', 'rest_output_link_wp_head' ); // Disable (wp-json) API link tag.
remove_action( 'wp_head','rsd_link' ); // Disable application/rsd+xml.
remove_action( 'wp_head', 'wlwmanifest_link' ); // Disable application/wlwmanifest+xml.
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' ); // Removes prev/next link tag.
remove_action( 'wp_head', 'wp_generator' ); // Remove wordpress version info.
remove_action( 'wp_head', 'rel_canonical' ); // Remove canonical link tag.
remove_action( 'wp_head', 'wp_shortlink_wp_head' ); // Remove shortlink.

//# Add pingback link tag to the document head.
// function shawtheme_pingback_header() {
//     if ( is_singular() && pings_open() ) {
//         printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
//     }
// }
// add_action( 'wp_head', 'shawtheme_pingback_header' );

//# Disable admin bar in the front-end.
add_filter( 'show_admin_bar', '__return_false' );

//# Disable unneeded default thumbnail sises.
function shawtheme_filter_image_sizes( $sizes) {
    unset( $sizes['thumbnail'] );
    unset( $sizes['medium'] );
    unset( $sizes['large'] );
    unset( $sizes['medium_large'] );

    return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'shawtheme_filter_image_sizes' );

//# Disable oEmbed related setting.
function disable_embeds_init() {
    global $wp;
    $wp->public_query_vars = array_diff( $wp->public_query_vars, array(
    'embed',
    ) );
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    add_filter( 'embed_oembed_discover', '__return_false' );
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    add_filter( 'tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin' );
    add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
}
add_action( 'init', 'disable_embeds_init', 9999 );
function disable_embeds_tiny_mce_plugin( $plugins ) {
    return array_diff( $plugins, array( 'wpembed' ) );
}
function disable_embeds_rewrites( $rules ) {
    foreach ( $rules as $rule => $rewrite ) {
        if ( false !== strpos( $rewrite, 'embed=true' ) ) {
            unset( $rules[ $rule ] );
        }
    }
    return $rules;
}
function disable_embeds_remove_rewrite_rules() {
    add_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'disable_embeds_remove_rewrite_rules' );
function disable_embeds_flush_rewrite_rules() {
    remove_filter( 'rewrite_rules_array', 'disable_embeds_rewrites' );
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'disable_embeds_flush_rewrite_rules' );

//# Rewites post url of custom post types.
// function custom_tutorial_link( $link, $post = 0 ) {

//     $the_cat  = get_the_category( $post->ID );
//     $the_id   = $the_cat[0]->category_parent;
//     $the_obj  = get_term( $the_id, 'category' );
//     $cat_slug = !empty( $the_id ) ? $the_obj->slug . '/' . $the_cat[0]->slug : $the_cat[0]->slug;
//     $the_str  = $cat_slug . '/' . $post->post_name;


//     if ( $post->post_type == 'tutorial' ) {
//         return home_url( $the_str );
//     } else {
//         return $link;
//     }
// }
// add_filter( 'post_type_link', 'custom_tutorial_link', 1, 2 );
// function tutorial_rewrites_init() {
//     add_rewrite_rule(
//         'tutorial/([\w&%\-\/\?]+)',
//         'index.php?post_type=tutorial&p=$matches[1]',
//         'top'
//     );
//     add_rewrite_rule(
//         'tutorial/([\w&%\-\/\?]+)/comment-page-([0-9]{1,})$',
//         'index.php?post_type=tutorial&p=$matches[1]&cpage=$matches[2]',
//         'top'
//     );
// }
// add_action( 'init', 'tutorial_rewrites_init' );

/*********************
 * 04. Core Register *
 *********************/
//# Define init.
function shawtheme_custom_init() {
    // remove supoort for post revisions.
    remove_post_type_support( 'post', 'revisions' );

    // remove supoort for attachment author.
    remove_post_type_support( 'attachment', 'author' );

    // remove unneeded supoorts for page.
    remove_post_type_support( 'page', 'author' );
    remove_post_type_support( 'page', 'custom-fields' );
    remove_post_type_support( 'page', 'revisions' );

    // Add support for page excerpt.
    add_post_type_support( 'page', array( 'excerpt' ) );

    //# Add post types.
    register_post_type( 'tutorial', array(
        'labels'        => array(
            'name'          => __( 'Tutorials', 'shawtheme' ),
            'singular_name' => __( 'Tutorial', 'shawtheme' ),
            'add_new'       => __( 'Add Tutorial', 'shawtheme' ),
            'search_items'  => __( 'Search Tutorials', 'shawtheme' ),
            'not_found'     => __( 'No tutorials found.', 'shawtheme' ),
            'all_items'     => __( 'All Tutorials', 'shawtheme' ),
        ),
        'description'   => __( 'Technology sharing, IT industry tutorials.', 'shawtheme' ),
        'public'        => true,
        // 'show_in_nav_menus' => false,
        'menu_position' => 6,
        'menu_icon'     => 'dashicons-laptop',
        'has_archive'   => true,
        'taxonomies'    => array( 'subject', 'label' ),
        'show_in_rest'  => true,
        'rewrite'       => array(
            // 'slug'  => 'tutorials',
            'feeds' => false
        ),
        'supports'      => array(
            'title', 'editor', 'comments', 'excerpt', 'thumbnail', 'custom-fields', 'post-formats'
        )
    ) );
    register_post_type( 'resource', array(
        'labels'        => array(
            'name'          => __( 'Resources', 'shawtheme' ),
            'singular_name' => __( 'Resource', 'shawtheme' ),
            'add_new'       => __( 'Add Resource', 'shawtheme' ),
            'search_items'  => __( 'Search Resources', 'shawtheme' ),
            'not_found'     => __( 'No resources found.', 'shawtheme' ),
            'all_items'     => __( 'All Resources', 'shawtheme' ),
        ),
        'description'   => __( 'Provide hundreds of high quality materials.', 'shawtheme' ),
        'public'        => true,
        // 'show_in_nav_menus' => false,
        'menu_position' => 7,
        'menu_icon'     => 'dashicons-archive',
        'has_archive'   => true,
        'taxonomies'    => array( 'genre' ),
        'show_in_rest'  => true,
        'rewrite'       => array( 
            // 'slug'  => 'resources',
            'feeds' => false
        ),
        'supports'      => array(
            'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'post-formats'
        )
    ) );

    # Add taxonomies.
    register_taxonomy( 'subject', 'tutorial', array(
        'labels'            => array(
            'name'          => __( 'Subjects', 'shawtheme' ),
            'singular_name' => __( 'Subject', 'shawtheme' ),
            'add_new_item'  => __( 'Add New Subject', 'shawtheme' ),
            'new_item_name' => __( 'New Subject Name', 'shawtheme' ),
            'parent_item'   => __( 'Parent Subject', 'shawtheme' ),
            'search_items'  => __( 'Search Subjects', 'shawtheme' ),
            'not_found'     => __( 'No subjects found.', 'shawtheme' )
        ),
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array(
            'slug'          => 'tutorials/category',
            'hierarchical'  => true
        )
    ) );
    register_taxonomy( 'label', 'tutorial', array(
        'labels'            => array(
            'name'          => __( 'Labels', 'shawtheme' ),
            'singular_name' => __( 'Label', 'shawtheme' ),
            'add_new_item'  => __( 'Add New Label', 'shawtheme' ),
            'search_items'  => __( 'Search Labels', 'shawtheme' ),
            'not_found'     => __( 'No labels found.', 'shawtheme' )
        ),
        'show_in_rest'      => true,
        'show_admin_column' => true
    ) );
    register_taxonomy( 'genre', 'resource', array(
        'labels'            => array(
            'name'          => __( 'Genres', 'shawtheme' ),
            'singular_name' => __( 'Genre', 'shawtheme' ),
            'add_new_item'  => __( 'Add New Genre', 'shawtheme' ),
            'new_item_name' => __( 'New Genre Name', 'shawtheme' ),
            'parent_item'   => __( 'Parent Genre', 'shawtheme' ),
            'search_items'  => __( 'Search Genres', 'shawtheme' ),
            'not_found'     => __( 'No genres found.', 'shawtheme' )
        ),
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array(
            'slug'          => 'resources/category',
            'hierarchical'  => true
        )
    ) );

}
add_action( 'init', 'shawtheme_custom_init' );

//# Register widget area.
function shawtheme_widgets_init() {
    register_sidebar(
        array(
            'name'          => __( 'Global Sidebar', 'shawtheme' ),
            'id'            => 'global-sidebar',
            'description'   => __( 'Add widgets here to appear in your site global right sidebar area.', 'shawtheme' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );

    register_sidebar(
        array(
            'name'          => __( 'Single Sidebar', 'shawtheme' ),
            'id'            => 'single-sidebar',
            'description'   => __( 'Add widgets here to appear in your single post right sidebar area.', 'shawtheme' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s area">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        )
    );
}
add_action( 'widgets_init', 'shawtheme_widgets_init' );

//# Add Meta Boxes.
function shawtheme_register_meta_boxes() {
    // Add "post rights" meta box for post.
    add_meta_box( 'post_rights_meta_box', __('Post Copyright', 'shawtheme' ), 'post_rights_meta_box_output', 'post', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'shawtheme_register_meta_boxes' );

function post_rights_meta_box_output( $post ) {
    wp_nonce_field( 'shawtheme_save_meta_data', 'shawtheme_save_meta_data_nonce' );

    $type_value = get_post_meta( $post->ID, '_rights_type', true );
    $link_value = get_permalink();
    if ( get_post_meta( $post->ID, '_source_link', true ) ) {
        $link_value = get_post_meta( $post->ID, '_source_link', true );
    }
    $options    = '<input type="radio" name="rights_type" value="Original" checked>' . __( 'Original', 'shawtheme' ) . '&nbsp;&nbsp;&nbsp;<input type="radio" name="rights_type" value="Reprint">' . __( 'Reprint', 'shawtheme' );
    if ( $type_value == 'Reprint' ) {
        $options = '<input type="radio" name="rights_type" value="Original">' . __( 'Original', 'shawtheme' ) . '&nbsp;&nbsp;&nbsp;<input type="radio" name="rights_type" value="Reprint" checked>' . __( 'Reprint', 'shawtheme' );
    }

    printf( '<p class="post-rights-type"><b>%1$s: </b>%2$s</p><p class="post-rights-link"><b>%3$s: </b><input id="post-rights-link" type="url" name="source_link" size="100" placeholder="http://" value="%4$s" required></p>',
        __( 'Rights Type', 'shawtheme' ),
        $options,
        __( 'Source Link', 'shawtheme' ),
        $link_value
    );
}

function shawtheme_save_meta_data( $post_id, $post ) {
    if ( 'post' === $post->post_type ) {
        if ( !isset( $_POST['shawtheme_save_meta_data_nonce'] ) || !wp_verify_nonce( $_POST['shawtheme_save_meta_data_nonce'], 'shawtheme_save_meta_data' ) ) {
            return;
        }

        $rights_type = 'Original';
        if ( isset( $_POST['rights_type'] ) ) {
            $rights_type = sanitize_text_field( $_POST['rights_type'] );
        }
        $source_link = '';
        if ( isset( $_POST['source_link'] ) ) {
            $source_link = sanitize_text_field( $_POST['source_link'] );
        }
        update_post_meta( $post_id, '_rights_type', $rights_type );
        update_post_meta( $post_id, '_source_link', $source_link );
    }
}
add_action( 'save_post', 'shawtheme_save_meta_data', 10, 2 );

/**********************
 * 05. Enqueue Assets *
 **********************/
function shawtheme_scripts() {
    wp_enqueue_style( 'shawicons', get_template_directory_uri() . '/assets/css/shawicons.css', array(), '1.0.0', 'all' );

    wp_enqueue_style( 'shawtheme', get_stylesheet_uri(), array( 'shawicons' ), wp_get_theme()->get( 'Version' ), 'all' );

    if ( get_theme_mod( 'stereoscopic_option' ) == true ) {

        wp_enqueue_style( 'stereoscopic', get_template_directory_uri() . '/assets/css/stereoscopic.css', array( 'shawtheme' ), '1.0.0', 'screen' );

    }


    if ( is_singular() ) {
        wp_enqueue_style( 'fancybox-style', get_template_directory_uri() . '/assets/css/jquery.fancybox.min.css', array(), '3.5.7' );

        if ( wp_attachment_is_image() ) {
            wp_enqueue_script( 'keyboard-image-navigation', get_template_directory_uri() . '/assets/js/keyboard-image-navigation.js', array( 'jquery' ), '1.0.0' );
        }

        if ( comments_open() ) {

            wp_enqueue_script( 'comment-form-toolbar-script', get_template_directory_uri() . '/assets/js/comment-form-toolbar.js', array( 'jquery' ), '1.0.0', true );
            wp_localize_script( 'comment-form-toolbar-script', 'localizeText', array(
                'themeURL'              => get_template_directory_uri(),
                'bold'                  => __( 'Bold', 'default' ),
                'italic'                => __( 'Italic', 'default' ),
                'strike'                => __( 'Deleted', 'shawtheme' ),
                'strikethrough'         => __( 'Deleted text (strikethrough)', 'default' ),
                'quote'                 => __( 'Quote', 'shawtheme' ),
                'code'                  => __( 'Code', 'default' ),
                'link'                  => __( 'Link', 'default' ),
                'insertLink'            => __( 'Insert link', 'default' ),
                'enterURL'              => __( 'Enter the URL', 'default' ),
                'image'                 => __( 'Image', 'default' ),
                'insertImage'           => __( 'Insert image', 'default' ),
                'enterImageURL'         => __( 'Enter the URL of the image', 'default' ),
                'enterImageDescription' => __( 'Enter a description of the image', 'default' ),
                'emoji'                 => __( 'Emoji', 'shawtheme' ),
                'insertEmoji'           => __( 'Insert emoji', 'shawtheme' ),
                'help'                  => __( 'Help', 'shawtheme' ),
                'close'                 => __( 'Close', 'default' ),
                'smile'                 => __( 'Smile', 'shawtheme' ),
                'sad'                   => __( 'Sad', 'shawtheme' ),
                'razz'                  => __( 'Razz', 'shawtheme' ),
                'roll'                  => __( 'Roll', 'shawtheme' ),
                'idea'                  => __( 'Surprise', 'shawtheme' ),
                'grin'                  => __( 'Grin', 'shawtheme' ),
                'cool'                  => __( 'Cool', 'shawtheme' ),
                'oops'                  => __( 'Oops', 'shawtheme' ),
                'cry'                   => __( 'Cry', 'shawtheme' ),
                'shock'                 => __( 'Shock', 'shawtheme' ),
                'neutral'               => __( 'Daze', 'shawtheme' ),
                'twisted'               => __( 'Beat', 'shawtheme' ),
                'lust'                  => __( 'Lust', 'shawtheme' ),
                'wink'                  => __( 'Wink', 'shawtheme' ),
                'eek'                   => __( 'Shut', 'shawtheme' ),
                'lol'                   => __( 'Giggle', 'shawtheme' ),
                'arrow'                 => __( 'Quit', 'shawtheme' ),
                'crazy'                 => __( 'Crazy', 'shawtheme' ),
                'mad'                   => __( 'Mad', 'shawtheme' ),
                'mrgreen'               => __( 'Dizzy', 'shawtheme' ),
                'question'              => __( 'Question', 'shawtheme' ),
                'evil'                  => __( 'Evil', 'shawtheme' ),
                'sleep'                 => __( 'Sleep', 'shawtheme' ),
                'hush'                  => __( 'Hush', 'shawtheme' ),
            ) );

            if ( get_option( 'thread_comments' ) )  { wp_enqueue_script( 'comment-reply' ); }
        }

        wp_enqueue_script( 'fancybox-script', get_template_directory_uri() . '/assets/js/jquery.fancybox.min.js', array( 'jquery' ), '3.5.7', false );
    }

    wp_enqueue_script( 'nicescroll-script', get_template_directory_uri() . '/assets/js/jquery.nicescroll.min.js', array( 'jquery' ), '3.7.6', false );

    wp_enqueue_script( 'shawtheme-script', get_template_directory_uri() . '/assets/js/global.js', array( 'jquery' ), '1.0.0', true );
    wp_localize_script( 'shawtheme-script', 'screenReaderText', array(
        'siteURL'     => get_site_url(),
        'menuOn'      => __( 'Expand Menu', 'shawtheme' ),
        'menuOff'     => __( 'Collapse Menu', 'shawtheme' ),
        'expand'      => __( 'Expand Child Menu', 'shawtheme' ),
        'collapse'    => __( 'Collapse Child Menu', 'shawtheme' ),
        'notes'       => __( 'Notes', 'shawtheme' ),
        'likedNotes'  => __( 'You already liked this post before.', 'shawtheme' ),
        'copiedNotes' => __( 'The link has been copied.', 'shawtheme' ),
        'ok'          => __( 'OK', 'shawtheme' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'shawtheme_scripts' );

/**********************
 * 06. Custom Outputs *
 **********************/
//# Change archive title output.
// function shawtheme_archive_title( $title ) {
//     if ( is_category() ) {
//         $title = single_cat_title( '', false );
//     } elseif ( is_tag() ) {
//         $title = single_tag_title( '', false );
//     } elseif ( is_author() ) {
//         $title = sprintf( __( '<span class="vcard">%s</span>' ), get_the_author() );
//     } elseif ( is_year() ) {
//         $title = get_the_date( _x( 'Y', 'yearly archives date format' ) );
//     } elseif ( is_month() ) {
//         $title = get_the_date( _x( 'F Y', 'monthly archives date format' ) );
//     } elseif ( is_day() ) {
//         $title = get_the_date( _x( 'F j, Y', 'daily archives date format' ) );
//     } elseif ( is_tax( 'post_format' ) ) {
//         if ( is_tax( 'post_format', 'post-format-aside' ) ) {
//             $title = _x( 'Asides', 'post format archive title' );
//         } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
//             $title = _x( 'Galleries', 'post format archive title' );
//         } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
//             $title = _x( 'Images', 'post format archive title' );
//         } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
//             $title = _x( 'Videos', 'post format archive title' );
//         } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
//             $title = _x( 'Quotes', 'post format archive title' );
//         } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
//             $title = _x( 'Links', 'post format archive title' );
//         } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
//             $title = _x( 'Statuses', 'post format archive title' );
//         } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
//             $title = _x( 'Audio', 'post format archive title' );
//         } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
//             $title = _x( 'Chats', 'post format archive title' );
//         }
//     } elseif ( is_post_type_archive() ) {
//         $title = post_type_archive_title( '', false );
//     } elseif ( is_tax() ) {
//         if ( get_queried_object() ) {
//             $title = single_term_title( '', false );
//         }
//     }
//     return $title;
// }
// add_filter( 'get_the_archive_title', 'shawtheme_archive_title' );

//# Modifies private/protected post title output.
function shawtheme_private_title_format( $format ) {
    return '[' . __( 'Private', 'shawtheme' ) . ']%s';
}
add_filter( 'private_title_format', 'shawtheme_private_title_format' );
function shawtheme_protected_title_format( $format ) {
    return '[' . __( 'Protected', 'shawtheme' ) . ']%s';
}
add_filter( 'protected_title_format', 'shawtheme_protected_title_format' );

//# Modifies post password form output.
function shawtheme_password_form() {
    global $post;
    $field_id = 'pwbox-' . ( empty($post->ID) ? rand() : $post->ID );
    $output   = '<form class="post-password-form" action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post"><p class="protected-post-notes">' . __( 'This content is password protected. To view it please enter the password below:', 'shawtheme' ) . '</p><p class="protected-post-form"><label for="' . $field_id . '"><span class="screen-reader-text">' . __( 'Password:', 'shawtheme' ) . '</span> <input id="' . $field_id . '" class="password-field" name="post_password" type="password" placeholder="' . esc_attr__( 'Enter the password here &hellip;', 'shawtheme' ) . '" size="20"></label> <input class="password-submit" name="Submit" type="submit" value="' . esc_attr_x( 'Enter', 'Used in post password form submit button', 'shawtheme' ) . '"></p></form>';
    return $output;
}
add_filter( 'the_password_form', 'shawtheme_password_form' );

//# Modifies tag cloud widget arguments to change tag cloud widget output.
function shawtheme_widget_tag_cloud_args( $args ) {
    $args['largest']  = 1;
    $args['smallest'] = 1;
    $args['unit']     = 'em';
    $args['format']   = 'list';
    return $args;
}
add_filter( 'widget_tag_cloud_args', 'shawtheme_widget_tag_cloud_args' );

/* //# Add background color to style tag cloud.
function shawtheme_colored_tag_cloud( $output ) {
    $output = preg_replace_callback('|<a (.+?)>|i', 'colored_tag_cloud_callback', $output);
    return $output;
}
function colored_tag_cloud_callback( $matches ) {
    $output = $matches[1];
    $colors = array( '#337ab7', '#5cb85c', '#5bc0de', '#f0ad4e', '#d9534f', '#37a7ff', '#d844f7', '#72db48' );
    $color= $colors[dechex( rand( 0, 7 ) )];
    $pattern = '/style=(\'|\")(.*)(\'|\")/i';
    $output = preg_replace( $pattern, "style=\"display: inline-block; margin: 0 0.5% 0.3125em; padding: 2px; width: 31.5%; text-align: center; color: #fff; border-radius: 3px; background-color: {$color};\"", $output );
    $pattern = '/style=(\'|\")(.*)(\'|\")/i';
    return "<a $output>";
}
add_filter( 'wp_tag_cloud', 'shawtheme_colored_tag_cloud', 1 , 1 );*/

//# Add "skip" links to the body top and bottom.
function to_content_skip_link() {
    echo '<a class="to-content skip-link screen-reader-text" href="#content">' . __( 'Skip to content', 'shawtheme' ) . '</a>';
}
add_action( 'wp_body_open', 'to_content_skip_link', 5 );
function to_top_skip_link() {
    echo '<a class="to-top skip-link" href="#masthead">' . __( 'Back to top', 'shawtheme' ) . '</a>';
}
add_action( 'wp_footer', 'to_top_skip_link', 1 );

/**********************
 * 07. Custom Classes *
 **********************/
 //# If we're missing JavaScript support, the HTML element will have a no-js class.
function shawtheme_no_js_class() {

    echo '<script>document.documentElement.className = document.documentElement.className.replace( "no-js", "js" );</script>';

}
add_action( 'wp_head', 'shawtheme_no_js_class' );

//# Add classes to body.
function shawtheme_body_classes( $classes ) {
    $classes[] = 'site';

    if (  have_posts() && ( is_search() || is_home() || is_archive() ) ) {
        $classes[] = 'plural';
    }

    if ( is_search() || is_archive() || is_singular( 'post' ) && is_active_sidebar( 'single-sidebar' ) ) {
        $classes[] = 'has-flex-content';
    }

    if ( have_posts() && ( is_home() || is_archive() && !is_search() ) ) {
        $classes[] = 'has-grid-main';
    }

    if ( is_search() || is_archive() ) {
        $classes[] = 'has-order-main';
    }

    if ( is_search() || is_singular( 'post' ) && is_active_sidebar( 'single-sidebar' ) ) {
        $classes[] = 'has-ratio-main';
    }

    if ( is_singular( 'post' ) && is_active_sidebar( 'single-sidebar' ) ) {
        $classes[] = 'has-single-sidebar';
    }

    if ( is_singular() ) {
        $classes[] = 'singular';
    }

    return $classes;
}
add_filter( 'body_class', 'shawtheme_body_classes' );

//# Add classes to posts.
function shawtheme_post_classes( $classes ) {
    $classes[] = 'entry';

    if ( in_the_loop() ) {
        $classes[] = 'post-area area';
    }

    if ( is_home() && !in_the_loop() ) {
        $classes[] = 'post-item';
    }
    
    return $classes;
}
add_filter( 'post_class', 'shawtheme_post_classes' );

/**********************
 * 08. Custom smileys *
 **********************/
//# Remove WordPress4.2+ version emoji hook.
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
// remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
// remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail' , 'wp_staticize_emoji_for_email' );

//# Replace directory of the smiley images.
function shawtheme_smilies_src( $img_src, $img ) {
    return get_stylesheet_directory_uri() . '/assets/img/smilies/' . $img;
}
add_filter( 'smilies_src', 'shawtheme_smilies_src', 10, 2 );

//# Convert text equivalent of smilies to images.
function shawtheme_init_smilies() {
    global $wpsmiliestrans;
    $wpsmiliestrans = array(
        ':smile:'   => 'weixiao.gif',
        ':grin:'    => 'ciya.gif',
        ':sad:'     => 'piezui.gif',
        ':eek:'     => 'bizui.gif',
        ':shock:'   => 'penxue.gif',
        ':???:'     => 'yiwen.gif',
        ':cool:'    => 'ku.gif',
        ':mad:'     => 'zhouma.gif',
        ':razz:'    => 'tiaopi.gif',
        ':neutral:' => 'fadai.gif',
        ':wink:'    => 'haixiu.gif',
        ':lol:'     => 'touxiao.gif',
        ':oops:'    => 'qiudale.gif',
        ':cry:'     => 'liulei.gif',
        ':evil:'    => 'yinxian.gif',
        ':twisted:' => 'qiaoda.gif',
        ':roll:'    => 'baiyan.gif',
        ':!:'       => 'zhuakuang.gif',
        ':?:'       => 'se.gif',
        ':idea:'    => 'jingxi.gif',
        ':arrow:'   => 'wunai.gif',
        ':mrgreen:' => 'yun.gif',
        ':|'        => 'shui.gif',
        ':x'        => 'xu.gif',
    );
}
add_action( 'init', 'shawtheme_init_smilies', 5 );

/**********
 * Others *
 **********/
//# Allow more HTML tags and attributes.
function shawtheme_allowedtags() {
    global $allowedtags;
    // $allowedtags['a'] = array( 'target' => true, 'href' => true, 'title' => true );
    $allowedtags['img'] = array( 'src' => true, 'alt' => true );
}
add_action( 'init', 'shawtheme_allowedtags' );

//# Add the custom logo to the login page.
function shawtheme_login_head() {
    if ( has_custom_logo() ) :
        $image = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );
        ?>
        <style type="text/css">
            .login h1 a {
                background-image: url(<?php echo esc_url( $image[0] ); ?>);
                -webkit-background-size: <?php echo absint( $image[1] )?>px;
                background-size: <?php echo absint( $image[1] ) ?>px;
                height: <?php echo absint( $image[2] ) ?>px;
                width: <?php echo absint( $image[1] ) ?>px;
            }
        </style>
        <?php
    endif;
}
add_action( 'login_head', 'shawtheme_login_head', 100 );

//# Author link will open in a new window.
function shawtheme_author_link() {
    $comment = get_comment( $comment_ID );
    $url     = get_comment_author_url( $comment );
    $author  = get_comment_author( $comment );
    if ( empty( $url ) || 'http://' == $url ) {
        return $author;
    } else {
        return "<a class='url' target='_blank' href='$url' rel='external nofollow'>$author</a>";
    }
}
add_filter( 'get_comment_author_link', 'shawtheme_author_link' );

// Highlight the search keyword in the search results page.
function search_keyword_replace( $text ) {
    if ( is_search() && in_the_loop() && !in_array( get_search_query(), array( 'p', 'p ' ) ) && ! ( empty( get_search_query() ) || ctype_space( get_search_query() ) ) ) {
        $keys = implode( '|', explode( ' ', get_search_query() ) );
        $text = preg_replace( '/(' . $keys . ')/iu', '<span class="search-keyword">\0</span>', $text );
    }
    return $text;
}
add_filter( 'the_title', 'search_keyword_replace' );
add_filter( 'the_excerpt', 'search_keyword_replace' );
// add_filter( 'the_content', 'search_keyword_replace' );

//# Set post query for main loop.
function shawtheme_get_posts( $query ) {
    // 首页主循环不显示“置顶文章”。
    if ( is_home() && $query->is_main_query() ) {
        $query->set( 'ignore_sticky_posts', true ); //主循环中不显示置顶post。
        return $query;
    }
    // 归档页显示自定义post type。
    // if ( ( is_category() || is_tag() || is_date() ) && !is_search() && $query->is_main_query() ) {
    //     $query->set( 'post_type', array( 'post', 'portfolio', 'tutorial', 'resource' ) );
    //     return $query;
    // }
    if ( is_tax( 'post_format' ) && !is_search() && $query->is_main_query() ) {
        $query->set( 'post_type', array( 'post', 'tutorial', 'resource' ) );
        return $query;
    }

    // if ( is_author() && $query->is_main_query() ) {
    //     $query->set( 'post_type', array( 'post', 'portfolio' ) );
    //     return $query;
    // }
}
add_action('pre_get_posts','shawtheme_get_posts');