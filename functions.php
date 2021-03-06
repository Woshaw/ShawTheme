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

// Custom permalink structures.
function shawtheme_permalink_structure() {
    global $wp_rewrite;
    // add_permastruct( 'subject', '%subject%' );
    if ( get_option( 'permalink_structure' ) ) {
        add_permastruct( 'tutorial', 'tutorial/%subject%/%tutorial%' );
        add_permastruct( 'resource', 'resource/%genre%/%resource%' );
    }
}
add_action( 'wp_loaded', 'shawtheme_permalink_structure' );

// Custom post type link.
function shawtheme_post_type_link( $post_link, $post ) {
    
    $the_type  = $post->post_type;

    if ( get_option( 'permalink_structure' ) && in_array( $the_type, array( 'tutorial', 'resource' ) ) ) {

        $the_obj   = get_post_type_object( $the_type );
        $the_taxs  = $the_obj->taxonomies;
        foreach ( $the_taxs as $the_tax ) {
            $tax = get_taxonomy( $the_tax );
            if ( $tax->hierarchical ) { 
                $taxonomy = $the_tax;
                break;
            }
        }

        $terms = get_the_terms( $post->ID, $taxonomy ); // 可用函数: shawtheme_post_terms().

        if ( !$terms )
            return str_replace( '%' . $taxonomy . '%', 'Uncategorized', $post_link );

        $post_terms = array();
        foreach ( $terms as $term )
            $post_terms[] = $term->slug;

        return str_replace( '%' . $taxonomy . '%', implode( '&', $post_terms ), $post_link );

    }

    return $post_link;

}
add_filter( 'post_type_link', 'shawtheme_post_type_link', 10, 2 );

// function shawtheme_rewrite_rules() {
//     add_rewrite_rule(
//         'tutorial/(\S+)',
//         'index.php?post_type=tutorial&p=$matches[1]',
//         'top'
//     );
//     add_rewrite_rule(
//         'tutorial/(\S+)/comment-page-([0-9]{1,})$',
//         'index.php?post_type=tutorial&p=$matches[1]&cpage=$matches[2]',
//         'top'
//     );
// }
// add_action( 'init', 'shawtheme_rewrite_rules' );



// // Make sure that all term links include their parents in the permalinks
// function add_term_parents_to_permalinks( $termlink, $term ) {
//     $term_parents = get_term_parents( $term );
//     foreach ( $term_parents as $term_parent )
//         $permlink = str_replace( $term->slug, $term_parent->slug . '/' . $term->slug, $termlink );
//     return $permlink;
// }
// add_filter( 'term_link', 'add_term_parents_to_permalinks', 10, 2 );

// // Helper function to get all parents of a term
// function get_term_parents( $term, &$parents = array() ) {
//     $parent = get_term( $term->parent, $term->taxonomy );
    
//     if ( is_wp_error( $parent ) )
//         return $parents;
    
//     $parents[] = $parent;
//     if ( $parent->parent )
//         get_term_parents( $parent, $parents );
//     return $parents;
// }



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
            'name'                     => __( 'Tutorials', 'shawtheme' ),
            'singular_name'            => __( 'Tutorial', 'shawtheme' ),
            'add_new'                  => __( 'Add Tutorial', 'shawtheme' ),
            'add_new_item'             => __( 'Add New Tutorial', 'shawtheme' ),
            'edit_item'                => __( 'Edit Tutorial', 'shawtheme' ),
            'view_item'                => __( 'View Tutorial', 'shawtheme' ),
            'view_items'               => __( 'View Tutorials', 'shawtheme' ),
            'search_items'             => __( 'Search Tutorials', 'shawtheme' ),
            'not_found'                => __( 'No tutorials found.', 'shawtheme' ),
            'not_found_in_trash'       => __( 'No tutorials found in Trash.', 'shawtheme' ),
            'parent_item_colon'        => __( 'Parent Tutorial:', 'shawtheme' ),
            'all_items'                => __( 'All Tutorials', 'shawtheme' ),
            'item_published'           => __( 'Tutorial published.', 'shawtheme' ),
            'item_published_privately' => __( 'Tutorial published privately.', 'shawtheme' ),
            'item_reverted_to_draft'   => __( 'Tutorial reverted to draft.', 'shawtheme' ),
            'item_scheduled'           => __( 'Tutorial scheduled.', 'shawtheme' ),
            'item_updated'             => __( 'Tutorial updated.', 'shawtheme' )
        ),
        'description'   => __( 'Teach skills, focus on IT industry tutorials.', 'shawtheme' ),
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
        'hierarchical'  => true,
        'supports'      => array(
            'title', 'editor', 'comments', 'excerpt', 'thumbnail', 'custom-fields', 'page-attributes', 'post-formats'
        )
    ) );
    register_post_type( 'resource', array(
        'labels'        => array(
            'name'                     => __( 'Resources', 'shawtheme' ),
            'singular_name'            => __( 'Resource', 'shawtheme' ),
            'add_new'                  => __( 'Add Resource', 'shawtheme' ),
            'add_new_item'             => __( 'Add New Resource', 'shawtheme' ),
            'edit_item'                => __( 'Edit Resource', 'shawtheme' ),
            'view_item'                => __( 'View Resource', 'shawtheme' ),
            'view_items'               => __( 'View Resources', 'shawtheme' ),
            'search_items'             => __( 'Search Resources', 'shawtheme' ),
            'not_found'                => __( 'No resources found.', 'shawtheme' ),
            'not_found_in_trash'       => __( 'No resources found in Trash.', 'shawtheme' ),
            'all_items'                => __( 'All Resources', 'shawtheme' ),
            'item_published'           => __( 'Resource published.', 'shawtheme' ),
            'item_published_privately' => __( 'Resource published privately.', 'shawtheme' ),
            'item_reverted_to_draft'   => __( 'Resource reverted to draft.', 'shawtheme' ),
            'item_scheduled'           => __( 'Resource scheduled.', 'shawtheme' ),
            'item_updated'             => __( 'Resource updated.', 'shawtheme' )
        ),
        'description'   => __( 'Assets sharing, provide high quality resources.', 'shawtheme' ),
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
            'edit_item'     => __( 'Edit Subject', 'shawtheme' ),
            'update_item'   => __( 'Update Subject', 'shawtheme' ),
            'add_new_item'  => __( 'Add New Subject', 'shawtheme' ),
            'new_item_name' => __( 'New Subject Name', 'shawtheme' ),
            'parent_item'   => __( 'Parent Subject', 'shawtheme' ),
            'search_items'  => __( 'Search Subjects', 'shawtheme' ),
            'not_found'     => __( 'No subjects found.', 'shawtheme' ),
            'back_to_items' => __( '← Back to subjects', 'shawtheme' )
        ),
        'description'       => __( 'Hierarchical taxonomy associated with tutorial.', 'shawtheme' ),
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array(
            // 'slug'          => 'tutorials/category',
            'hierarchical'  => true
        )
    ) );
    register_taxonomy( 'label', 'tutorial', array(
        'labels'            => array(
            'name'          => __( 'Labels', 'shawtheme' ),
            'singular_name' => __( 'Label', 'shawtheme' ),
            'edit_item'     => __( 'Edit Label', 'shawtheme' ),
            'update_item'   => __( 'Update Label', 'shawtheme' ),
            'add_new_item'  => __( 'Add New Label', 'shawtheme' ),
            'search_items'  => __( 'Search Labels', 'shawtheme' ),
            'not_found'     => __( 'No labels found.', 'shawtheme' ),
            'back_to_items' => __( '← Back to labels', 'shawtheme' )
        ),
        'description'       => __( 'Non-hierarchical taxonomy associated with tutorial.', 'shawtheme' ),
        'show_in_rest'      => true,
        'show_admin_column' => true,
        // 'show_tagcloud'     => true
    ) );
    register_taxonomy( 'genre', 'resource', array(
        'labels'            => array(
            'name'          => __( 'Genres', 'shawtheme' ),
            'singular_name' => __( 'Genre', 'shawtheme' ),
            'edit_item'     => __( 'Edit Genre', 'shawtheme' ),
            'update_item'   => __( 'Update Genre', 'shawtheme' ),
            'add_new_item'  => __( 'Add New Genre', 'shawtheme' ),
            'new_item_name' => __( 'New Genre Name', 'shawtheme' ),
            'parent_item'   => __( 'Parent Genre', 'shawtheme' ),
            'search_items'  => __( 'Search Genres', 'shawtheme' ),
            'not_found'     => __( 'No genres found.', 'shawtheme' ),
            'back_to_items' => __( '← Back to genres', 'shawtheme' )
        ),
        'description'       => __( 'Hierarchical taxonomy associated with resource.', 'shawtheme' ),
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array(
            // 'slug'          => 'resources/category',
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
function shawtheme_gutenberg_scripts() {
    wp_enqueue_script( 'shawtheme_gutenberg_script', get_template_directory_uri() . '/assets/js/custom-gutenberg-blocks.js', array( 'wp-blocks', 'wp-i18n', 'wp-editor', 'wp-element' ) );
}
add_action( 'enqueue_block_editor_assets', 'shawtheme_gutenberg_scripts' );

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
//# Change costom post type archive title output.
function shawtheme_archive_title( $title ) {
    if ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    } elseif ( is_tax() ) {
        $queried_object = get_queried_object();
        if ( $queried_object ) {
            $tax = get_taxonomy( $queried_object->taxonomy );
            $tit = single_term_title( '', false );
            if ( in_array( $tax->name, array( 'subject', 'genre' ) ) ) {
                $title = sprintf( __( 'Category: %s', 'default' ), $tit );
            } elseif ( $tax->name == 'label' ) {
                $title = sprintf( __( 'Tag: %s', 'default' ), $tit );
            }
        }
    }
    return $title;
}
add_filter( 'get_the_archive_title', 'shawtheme_archive_title' );

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

// Add classes to nav menu items.
function shawtheme_nav_menu_classes( $classes, $item, $args ) {
    if ( is_singular() && 'primary' === $args->theme_location ) {
        global $post;
        $the_url    = get_post_type_archive_link( $post->post_type );
        $the_parent = $item->menu_item_parent;
        $the_class  = 'current-menu-ancestor';
        $the_object = $item->object;
        
        if ( $item->url == $the_url ) { // Archive link items.
            if ( $the_parent ) {
                $classes[] = 'current-menu-parent';
            } else {
                $classes[] = $the_class;
            }
        }

        if ( $item->type == 'taxonomy' ) { // Taxonomy items.
            $terms     = get_the_terms( $post->ID, $the_object );
            $terms_one = array();
            $parents   = '';
            if ( $terms && !is_wp_error( $terms ) ) {
                foreach ( $terms as $term ) {
                    $term_id     = $term->term_id;
                    $terms_one[] = $term->name;
                    if ( $term->parent ) {
                        $parents .= get_term_parents_list( $term_id, $the_object, array( 'separator' => ', ', 'link' => false ) );
                    }
                }
            }
            $terms_two = explode( ', ', $parents );
            $the_terms = array_filter( array_unique( array_merge( $terms_one, $terms_two ) ) );
            if ( in_array( $item->title, $the_terms ) ) {
                $classes[] = $the_class;
            }
        }

        if ( $the_object == 'page' ) { // Page with 'hierarchical' feature items.
            $parented = $post->post_parent;
            $pages    = array();
            while ( $parented ) {
                $parent_page = get_page( $parented );
                $pages[]     = $parent_page->ID;
                $parented    = $parent_page->post_parent;
            }
            if ( !in_array( $the_class, $classes ) && in_array( $item->object_id, $pages ) ) {
                $classes[] = $the_class;
            }
        }
    }
    return $classes;
}
add_filter( 'nav_menu_css_class' , 'shawtheme_nav_menu_classes' , 10, 3 );

//# Add classes to body.
function shawtheme_body_classes( $classes ) {
    $classes[] = 'site';

    if (  have_posts() && ( is_search() || is_home() || is_archive() ) ) {
        $classes[] = 'plural';
    }

    if ( is_search() || is_archive() && !is_post_type_archive() || is_singular( 'post' ) && is_active_sidebar( 'single-sidebar' ) ) {
        $classes[] = 'has-flex-content';
    }

    if ( have_posts() && ( is_front_page() || is_home() || is_archive() && !is_search() ) ) {
        $classes[] = 'has-grid-main';
    }

    if ( is_search() || is_archive() && !is_post_type_archive() ) {
        $classes[] = 'has-order-main';
    }

    if ( is_search() || is_singular( 'post' ) && is_active_sidebar( 'single-sidebar' ) ) {
        $classes[] = 'has-ratio-main';
    }

    if ( !is_search() && is_archive() && !is_post_type_archive() ) {
        $classes[] = 'has-percent-main';
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

//# Limit post formats for specified post types.
function get_allowed_post_formats( $type = null ) {
    if ( $type == 'post' ) {
        return array( 'aside', 'status', 'gallery', 'audio', 'video' );
    } elseif ( $type == 'tutorial' ) {
        return array( 'video' );
    } elseif ( $type == 'resource' ) {
        return array( 'image', 'audio', 'video', 'link' );
    }
    return get_theme_support( 'post-formats' )[0];
}
function default_post_format_filter( $format ) {
    return in_array( $format, get_allowed_post_formats( get_post_type() ) ) ? $format : 'standard';
}
function shawtheme_post_formats_filter() {

    $post_type = get_current_screen()->post_type;

    // Bail if not on the projects screen.
    if ( empty( $post_type ) || !in_array( $post_type, array( 'post', 'tutorial', 'resource' ) ) )
        return;

    // Check if the current theme supports formats.
    if ( current_theme_supports( 'post-formats' ) ) {

        $formats = get_theme_support( 'post-formats' );

        // If we have formats, add theme support for only the allowed formats.
        if ( isset( $formats[0] ) ) {
            $new_formats = array_intersect( $formats[0], get_allowed_post_formats( $post_type ) );

            // Remove post formats support.
            remove_theme_support( 'post-formats' );

            // If the theme supports the allowed formats, add support for them.
            if ( $new_formats )
                add_theme_support( 'post-formats', $new_formats );
        }
    }

    // Filter the default post format.
    add_filter( 'option_default_post_format', 'default_post_format_filter', 95, 1 );
}
add_action( 'load-post.php',     'shawtheme_post_formats_filter' );
add_action( 'load-post-new.php', 'shawtheme_post_formats_filter' );
add_action( 'load-edit.php',     'shawtheme_post_formats_filter' );