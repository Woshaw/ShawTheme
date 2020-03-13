<?php
/**
 * <= Custom template tags =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

/******************
 * 1. Costom tags *
 ******************/
//# Get post terms.
function shawtheme_post_terms( $args = '' ) {
    $defaults = array(
        'format'    => 'name',
        'separator' => '/',
        'link'      => 1,
        'inclusive' => 1,
        'echo'      => 0,
    );
    $parsed_args = wp_parse_args( $args, $defaults );

    global $post;
    $the_type = $post->post_type;
    $the_obj  = get_post_type_object( $the_type );
    $the_taxs = $the_obj->taxonomies;
    foreach ( $the_taxs as $the_tax ) {
        $tax = get_taxonomy( $the_tax );
        if ( $tax->hierarchical ) { 
            $taxonomy = $the_tax;
            break;
        }
    }
    $terms      = get_the_terms( $post->ID, $taxonomy );
    $the_terms  = get_term_parents_list( $terms[0]->term_id, $terms[0]->taxonomy, array(
        'format'    => $parsed_args['format'],
        'separator' => $parsed_args['separator'],
        'link'      => $parsed_args['link'],
        'inclusive' => $parsed_args['inclusive'],
    ) );

    if ( !$terms || is_wp_error( $terms ) || is_wp_error( $the_terms ) ) {
        return false;
    }

    // $post_terms = str_replace( $the_type . $parsed_args['separator'], '', substr( $the_terms, 0, -1 ) );

    return $the_terms;
}

//# Custom time format.
function shawtheme_time_format( $ptime ) {
    $ptime = strtotime( $ptime );
    $etime = time() - $ptime;
    if ( $etime < 1 ) return __( 'just now', 'shawtheme' );
    $intervals = array (
        12 * 30 * 24 * 60 * 60  =>  __( 'years ago', 'shawtheme' ),
        30 * 24 * 60 * 60       =>  __( 'months ago', 'shawtheme' ),
        7 * 24 * 60 * 60        =>  __( 'weeks ago', 'shawtheme' ),
        24 * 60 * 60            =>  __( 'days ago', 'shawtheme' ),
        60 * 60                 =>  __( 'hours ago', 'shawtheme' ),
        60                      =>  __( 'minutes ago', 'shawtheme' ),
        1                       =>  __( 'seconds ago', 'shawtheme' )
    );
    foreach ( $intervals as $secs => $str ) {
        $d = $etime / $secs;
        if ( $d >= 1 ) {
            $r = round( $d );
            return $r . $str;
        }
    };
}

//# Custom number format
function shawtheme_number_format( $num ) {
    if ( $num >= 10000 ) {
        $num = round( $num / 10000 * 100 ) / 100 .'W';
    } elseif( $num >= 1000 ) {
        $num = round( $num / 1000 * 100 ) / 100 . 'K';
    } else {
        $num = $num;
    }
    return $num;
}

/***************
 * 2. Headline *
 ***************/
function shawtheme_headline() {
    global $wp_query;
    $the_id = $wp_query->get_queried_object_id();
    // $the_title = get_the_title( $the_id ) ? get_the_title( $the_id ) : single_post_title( '', false );
?>
    <div class="site-headline">
    <?php
        // Headline image/video media.
        if ( !( is_front_page() && get_header_video_url() ) && has_post_thumbnail( $the_id ) ) {

            printf( '<figure class="wp-custom-header" aria-hidden="true">%s</figure>',
                get_the_post_thumbnail( $the_id, 'header-thumbnail', array( 'alt' => get_the_title( $the_id ), 'loading' => 'eager' ) )
            );

        } elseif ( is_front_page() && get_header_video_url() || get_header_image() ) {

            the_custom_header_markup();
            
        } else {

            printf( '<div class="wp-custom-header" aria-hidden="true"><img src="%1$s" width="1200" height="300" %2$s></div>',
                get_template_directory_uri() . '/assets/img/header_image.jpg',
                sprintf( 'alt="%s" loading="eager"', esc_attr( get_bloginfo( 'name', 'display' ) ) )
            );

        }

        // Headline body.
        if ( is_search() ) : ?>
            <div class="headline-body">
                <h1 class="headline-title"><?php _e( 'Search', 'default' ); ?></h1>
                <div class="headline-summary">
                    <?php get_template_part( 'subparts/search', 'form' ); ?>
                </div><!-- .headline-summary -->
            </div><!-- .headline-body -->
    <?php
        elseif ( !( is_front_page() && get_header_video_url() ) && single_post_title( '', false ) ) : ?>
            <div class="headline-body">
                <?php
                    printf( '<h1 class="headline-title">%1$s</h1><div class="headline-summary"><p>%2$s</p></div><!-- .headline-summary -->',
                        single_post_title( '', false ),
                        get_the_excerpt( $the_id )
                    );
                ?>
            </div><!-- .headline-body -->
    <?php
        elseif ( is_archive() ) : ?>
            <div class="headline-body">
                <?php
                    the_archive_title( '<h1 class="headline-title">', '</h1>' );
                    the_archive_description( '<div class="headline-summary">', '</div><!-- .headline-summary -->' );
                ?>
            </div><!-- .headline-body -->
    <?php
        endif;
    ?>          
    </div><!-- .site-Headline -->
<?php
}

/******************
 * 3. Breadcrumbs *
 ******************/
function shawtheme_breadcrumbs() {
    if ( !is_front_page() || is_paged() ) {

        $delimiter = '<span class="separator" aria-hidden="true">&nbsp;&raquo;&nbsp;</span>';
        $before    = '<span class="current">';
        $output    = sprintf( '<nav id="crumbs" class="site-breadcrumb" role="navigation" aria-label="%1$s"><h2 class="screen-reader-text">%2$s</h2><div class="nav-links"><span class="nav-meta">%3$s: </span><a class="home-link" href="%4$s" rel="home">%5$s</a>%6$s',
            esc_attr__( 'Breadcrumb', 'shawtheme' ),
            __( 'Crumb Navigation', 'shawtheme' ),
            esc_html__( 'You are here', 'shawtheme' ),
            home_url(),
            esc_html__( 'Home', 'default' ),
            $delimiter
        );

        if ( is_search() || is_archive() ) {

            $the_object = get_queried_object();
            $the_id     = $the_object->term_id;

            if ( is_search() ) { // 搜索结果

                $output .= sprintf( '%1$s%2$s: <span class="search-keyword">%3$s</span>',
                    $before,
                    esc_html__( 'Searching for', 'shawtheme' ),
                    get_search_query()
                );

            } elseif ( is_category() ) { // 分类存档

                $the_cat    = get_category( $the_id );
                $the_parent = get_category( $the_cat->parent );
                if ( $the_cat->parent != 0 ) {
                    $the_cats = get_category_parents( $the_parent, true, $delimiter );
                    $output  .= str_replace( '<a', '<a class="category-link"', $the_cats );
                }
                $output .= $before . single_cat_title( '', false );

            } elseif ( is_tag() ) { // 标签存档

                $output .= $before . single_tag_title( '', false );

            } elseif ( is_author() ) { // 作者存档

                global $author;
                $userdata = get_userdata( $author );
                $output  .= sprintf( '%1$s<span class="archived">[%2$s]</span>%3$s',
                    $before,
                    esc_html__( 'Author', 'default' ),
                    $userdata->display_name
                );

            } elseif ( is_date() ) { // 日期存档

                $year_link = get_year_link( get_the_time( 'Y' ) );
                $the_year  = get_the_time( _x( 'Y', 'yearly archives date format', 'default' ) );
                $the_month = get_the_time( _x( 'm', 'monthly archives date format', 'shawtheme' ) );

                if ( is_day() ) { // 每日存档

                    $output .= sprintf( '<a class="year-link" href="%1$s">%2$s</a>%3$s<a class="month-link"  href="%4$s">%5$s</a>%3$s%6$s',
                        $year_link,
                        $the_year,
                        $delimiter,
                        get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ),
                        $the_month,
                        $before . get_the_time( _x( 'd', 'daily archives date format', 'shawtheme' ) )
                    );

                } elseif ( is_month() ) { // 月份存档

                    $output .= sprintf( '<a class="year-link" href="%1$s">%2$s</a>%3$s',
                        $year_link,
                        $the_year,
                        $delimiter . $before . $the_month
                    );

                } elseif ( is_year() ) { // 年份存档

                    $output .= $before . $the_year;

                }
                
            } elseif ( is_post_type_archive() ) {

                $output .= $before . post_type_archive_title( '', false );

            } elseif ( is_tax() ) { // 其它分类法

                // $the_type = get_post_type_object( get_post_type() );
                $the_term  = get_term( $the_id );
                $the_tax   = get_taxonomy( $the_object->taxonomy );
                $the_trems = get_term_parents_list( $the_id, $the_tax->name, array( 'separator' => $delimiter, 'inclusive' => false ) );
                if ( !is_wp_error( $the_trems ) && $the_term->parent != 0 ) {
                    $output .= str_replace( '<a', '<a class="taxonomy-link"', $the_trems );
                }
                $the_format = is_tax( 'post_format' ) ? sprintf( '<span class="formatted">[%s]</span>', _x( 'Formats', 'post format', 'default' ) ) : null; // 文章格式
                $output    .= $before . $the_format . single_term_title( '', false );

            }

            if ( !have_posts() ) { // 无内容
                $output .= sprintf( '<span class="voided">[%s]</span>', esc_html__( 'No Content', 'shawtheme' ) );
            }

        } elseif ( is_singular() ) {
            global $post;
            $parented = $post->post_parent;
            $the_type = $post->post_type;
            $the_obj  = get_post_type_object( $the_type );

            // if ( in_array( $the_type, array( 'post', 'tutorial', 'resource' ) ) ) { //# Post type of the post.
            //     $url     = get_post_type_archive_link( $the_type );
            //     $url     = $url ? $url : home_url( '/' );
            //     $name    = ( $the_type == 'post' ? __( 'Blog', 'shawtheme' ) : $the_obj->label );
            //     $output .= sprintf( '<a href="%1$s">%2$s</a>%3$s', $url, $name, $delimiter );
            // }

            $parent_post = get_post( $parented );
            $categories  = $parented ? get_the_category( $parent_post->ID ) : get_the_category();
            $category    = $categories[0];
            $parents     = get_category_parents( $category, true, $delimiter );
            if ( !is_wp_error( $parents ) ) { // Categories of single post.
                $output .= str_replace ( '<a', '<a class="category-link"', $parents );
            }

            if ( in_array( $the_type, array( 'tutorial', 'resource' ) ) ) { //# Taxonomy terms of custom post type.
                $post_terms = shawtheme_post_terms( array( 'separator' => $delimiter ) );
                if ( $post_terms ) { //# Terms of custom post type single post.
                    $output .= str_replace( '<a', '<a class="taxonomy-link"', $post_terms ); 
                }
            }

            if ( $parented ) { //# Parents of page or post with hierarchical post type.

                $pages = array();
                while ( $parented ) {
                    $parent_page = get_page( $parented );
                    $pages[]     = sprintf( '<a class="post-link" href="%1$s">%2$s</a>',
                        get_permalink( $parent_page->ID ),
                        get_the_title( $parent_page->ID )
                    );
                    $parented    = $parent_page->post_parent;
                }
                $pages = array_reverse( $pages );
                foreach ( $pages as $page ) {
                    $output .= $page . $delimiter;
                }
                // $output .= sprintf( '<a class="post-link" href="%1$s">%2$s</a>%3$s',
                //     get_permalink( $parent_post ),
                //     $parent_post->post_title,
                //     $delimiter
                // );
            }

            $output .= $before . single_post_title( '', false ); //# Current content title.

            if ( is_attachment() ) { // 附件
                $output .= sprintf( '<span class="attached">[%s]</span>', esc_html__( 'Attachment', 'shawtheme' ) );
            }

        } elseif ( is_home() ) { // Home paged or static home page.

            if ( is_front_page() && is_paged() ) {
                $output .= $before . __( 'Posts List', 'shawtheme' );
            } else {
                $output .= $before . single_post_title( '', false );
            }

        } elseif ( is_404() ) {

            $output .= $before . __( '404 Page', 'shawtheme' );

        }

        if ( is_paged() ) { // 分页
            $output .= sprintf( '<span class="paged">[%s]</span>', sprintf( __( 'Page %s', 'shawtheme' ), get_query_var( 'paged' ) ) );
        }

        $output .= '</span></div></nav>';

        echo $output;
    }
}

/*****************
 * 4. Post Entry *
 *****************/
//# Post thumbnail.
function shawtheme_thumbnail( string $ratio = '8to5' ) {

    if ( has_post_thumbnail() ) {

        $size = ( $ratio == '4to1' ) ? 'entry-thumbnail' : 'post-thumbnail';
        printf( '<a class="post-thumbnail ratio-%1$s-thumbnail" href="%2$s" aria-hidden="true">%3$s</a>',
            $ratio,
            esc_url( get_permalink() ),
            get_the_post_thumbnail( $post, $size, array( 'alt' => the_title_attribute( 'echo=0' ), 'loading' => 'lazy' ) )
        );

    } else {

        printf(
            '<a class="post-thumbnail ratio-%1$s-thumbnail" href="%2$s" aria-hidden="true"><img src="%3$s/assets/img/default_%1$s_thumbnail.png" class="no-thumbnail" alt="%4$s"></a>',
            $ratio,
            esc_url( get_permalink() ),
            esc_url( get_template_directory_uri() ),
            __( 'Default featured image', 'shawtheme' )
        );

    }

}

//# Post excerpt.
function new_excerpt_length( $length ) {
    return 100;
}
add_filter( 'excerpt_length', 'new_excerpt_length' );
function new_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'new_excerpt_more' );
function shawtheme_excerpt() {
    if ( !is_singular() ) {
?>
        <div class="entry-summary">
            <?php if ( post_password_required() && !is_search() ) {
                the_content();
            } else {
                the_excerpt();
            } ?>
        </div><!-- .entry-summary -->
<?php
    } elseif ( is_attachment() && in_the_loop() ) {
        $caption = has_excerpt() ? get_the_excerpt() : get_the_title();
        printf( '<figcaption class="entry-caption wp-caption-text">%s</figcaption>', $caption );
    }
}

//# Post copyright.
function shawtheme_post_rights() {

    function is_reprint_post() {
        if ( get_post_meta( get_the_id(), '_rights_type', true ) == 'Reprint' ) return true; else return false;
    }
    $the_notice = is_reprint_post() ? __( 'Reprint content，ask the original author&rsquo;s permission before you reproduce this post.', 'shawtheme' ) : __( 'Original content, do not reproduce this post before you asked author&rsquo;s permission.', 'shawtheme' );
    $the_text   = is_reprint_post() ? __( 'Source link', 'shawtheme' ) : __( 'Post link', 'shawtheme' );
    $the_url    = is_reprint_post() ? get_post_meta( get_the_id(), '_source_link', true ) : get_permalink();

    printf( '<div class="post-copyright"><p class="post-copyright-text"><b>%1$s: </b>%2$s</p><p class="post-copyright-link"><b>%3$s: </b><input id="the-url" type="url" value="%4$s" size="60"><button id="the-copyist">%5$s</button></p></div>',
        __( 'Copyright notice', 'shawtheme' ),
        $the_notice,
        $the_text,
        esc_attr( $the_url ),
        __( 'Copy the link', 'shawtheme' )
    );

}

/****************
 * 5. Post Meta *
 ****************/
//# Post views.
function shawtheme_post_views() {
    if ( is_single() && in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
        global $post;
        if( $post->ID ) {
            $views = (int) get_post_meta( $post->ID, '_post_views', true );
            if( !update_post_meta( $post->ID, '_post_views', ( $views + 1 ) ) ) {
                add_post_meta( $post->ID, '_post_views', 1, true );
            }
        }
    }
}
add_action('wp_head', 'shawtheme_post_views');
function post_views_count( bool $echo = false ) {
    global $post;
    $views = get_post_meta( $post->ID, '_post_views', true );
    $count = $views ? number_format_i18n( $views ) : '0';
    if ( $echo == true ) echo $count;
    else return $count;
}

//# Post Likes.
function shawtheme_post_likes() {
    $the_id = $_POST['data_id'];
    $action = $_POST['data_action'];
    if ( $action == 'liking' ) {
        $likes  = get_post_meta( $the_id, '_post_likes', true );
        $expire = time() + 99999999;
        $domain = ( $_SERVER['HTTP_HOST'] != 'localhost' ) ? $_SERVER['HTTP_HOST'] : false; // make cookies work with localhost
        setcookie( 'post_likes_'. $the_id, $the_id, $expire, '/', $domain, false );
        if ( !$likes || !is_numeric( $likes ) ) {
            update_post_meta( $the_id, '_post_likes', 1 );
        } 
        else {
            update_post_meta( $the_id, '_post_likes', ( $likes + 1 ) );
        }
    }
    die;
}
add_action( 'wp_ajax_nopriv_post_likes', 'shawtheme_post_likes' );
add_action( 'wp_ajax_post_likes', 'shawtheme_post_likes' );
function get_likes_class() {
    global $post;
    if ( isset( $_COOKIE['post_likes_' . $post->ID] ) ) return 'liked';
}
function post_likes_count( bool $echo = false ) {
    global $post;
    $likes = get_post_meta( $post->ID, '_post_likes', true );
    $count = $likes ? number_format_i18n( $likes ) : '0';
    if ( $echo == true ) echo $count;
    else return $count;
}


//# Returns the comment user avatar.
function shawtheme_get_user_avatar( $id_or_email = null ) {

    if ( !isset( $id_or_email ) ) {
        $id_or_email = get_current_user_id();
    }

    return get_avatar( $id_or_email, 40 );
}

//# Returns comment user avatars markup.
function shawtheme_comment_avatars_markup( $comment_authors ) {
    if ( empty( $comment_authors ) ) {
        return;
    }
    $markup = '<span class="comment-author-avatars">';
    foreach ( $comment_authors as $id_or_email ) {
        $markup .= shawtheme_get_user_avatar( $id_or_email );
    }
    $markup .= '</span>';
    return $markup;
}

//# Returns information about the current post's discussion, with cache support.
function shawtheme_get_discussion_data() {
    static $discussion, $post_id;

    $current_post_id = get_the_ID();
    if ( $current_post_id === $post_id ) {
        return $discussion; // If we have discussion information for post ID, return cached object.
    } else {
        $post_id = $current_post_id;
    }

    $comments = get_comments(
        array(
            'post_id' => $current_post_id,
            'orderby' => 'comment_date_gmt',
            'order'   => get_option( 'comment_order', 'asc' ), // Respect comment order from Settings » Discussion.
            'status'  => 'approve',
            'number'  => 20, // Only retrieve the last 20 comments, as the end goal is just 6 unique authors.
        )
    );

    $authors = array();
    foreach ( $comments as $comment ) {
        $authors[] = ( (int) $comment->user_id > 0 ) ? (int) $comment->user_id : $comment->comment_author_email;
    }

    $authors    = array_unique( $authors );
    $discussion = (object) array(
        'authors'   => array_slice( $authors, 0, 6 ),           // Six unique authors commenting on the post.
        'responses' => get_comments_number( $current_post_id ), // Number of responses.
    );

    return $discussion;
}

//# Post meta checker.
function shawtheme_has_meta( $metakey ) {
    if ( $metakey == 'views' ) {
        $count = post_views_count();
    } elseif ( $metakey == 'likes' ) {
        $count = post_likes_count();
    } elseif ( $metakey == 'comments' ) {
        $count = get_comments_number();
    }

    if ( $count > 0 ) return true; else return false;
}

//# Post meta.
function shawtheme_entry_meta() {
    $post_link = esc_url( get_permalink() );
    $output    = '<div class="entry-meta">';

    if ( post_type_supports( get_post_type(), 'author' ) && is_multi_author() && ( is_archive() || is_single() ) ) { // Author of the post.

        $class   = is_archive() ? 'has-avatar' : null;
        $author  = '<span class="author-name">' . get_the_author() . '</span>';
        $author  = is_archive() ? get_avatar( get_the_author_meta( 'user_email' ), 40 ) . $author : $author;
        $output .= sprintf( '<span class="post-author %1$s"><span class="screen-reader-text">%2$s: </span><a class="url fn n" rel="author" href="%3$s">%4$s</a></span> ',
            $class,
            _x( 'Author', 'Used before post author name.', 'shawtheme' ),
            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
            $author
        );

    }

    if (  is_search() || is_date() || is_single() ) { // Publish date of the post.

        $the_date = '<time class="entry-date published" datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . get_the_date() . '</time>';
        $the_date =  is_singular( 'post' ) ? '<a href="' . get_month_link( get_the_time('Y'), get_the_time('m') ) . '">' . $the_date . '</a>' : $the_date;
        $output  .= sprintf( '<span class="post-date"><span class="screen-reader-text">%1$s: </span>%2$s</span> ',
            _x( 'Publish date', 'Used before publish date.', 'shawtheme' ),
            $the_date
        );

    }

    if ( is_home() || is_post_type_archive() ) { // Publish time of the post.

        $output .= sprintf( '<span class="post-time"><span class="screen-reader-text">%1$s: </span><a  rel="bookmark" href="%2$s">%3$s</a></span> ',
            _x( 'Publish time', 'Used before publish time.', 'shawtheme' ),
            $post_link,
            shawtheme_time_format( get_gmt_from_date( get_the_time( 'Y-m-d G:i:s' ) ) )
        );

    }

    if ( is_search() ) { // Type of the post.

        $the_type   = get_post_type();
        $the_link   = get_post_type_archive_link( $the_type ) ? get_post_type_archive_link( $the_type ) : $post_link;
        $the_object = get_post_type_object( $the_type );
        $output    .= sprintf( '<span class="post-type"><span class="screen-reader-text">%1$s: </span><a  rel="bookmark" href="%2$s">%3$s</a></span> ',
            _x( 'Post type', 'Used before post type.', 'shawtheme' ),
            $the_link,
            $the_object->labels->name
        );

    }

    if ( has_post_format() && ( is_search() || is_tax( 'post_format' ) || is_single() ) ) { // Format of the post.

        $format     = get_post_format();
        $the_string = get_post_format_string( $format );
        $the_link   = esc_url( get_post_format_link( $format ) );
        $the_format = is_tax( 'post_format' ) && !is_search() ? $the_string : sprintf( '<a href="%1$s">%2$s</a>', $the_link, $the_string );
        $output    .= sprintf( '<span class="post-format"><span class="screen-reader-text">%1$s: </span>%2$s</span> ',
            _x( 'Format', 'Used before post format.', 'shawtheme' ),
            $the_format
        );

    }

    $category_list = get_the_category_list( ',' );
    $taxonomy_name = get_post_type() == 'tutorial' ? 'subject' : 'genre';
    $taxonomy_list = get_the_term_list( $post->ID, $taxonomy_name, '', ',' );
    $cats_list     = has_category() ? $category_list : $taxonomy_list;
    if ( $cats_list && !is_wp_error( $cats_list ) && ( is_category() || is_tax( $taxonomy_name ) || is_search() || is_single() ) ) { // Categories of the post.

        $cats    = explode( ',', $cats_list );
        $output .= sprintf( '<span class="post-category"><span class="screen-reader-text">%1$s: </span>%2$s</span> ',
            _x( 'Category', 'Used before category name.', 'shawtheme' ),
            reset( $cats )
        );

    }

    $split_symbol = _x( ', ', 'Used between tag list items, there is a space after the comma.', 'shawtheme' );
    $tag_list     = get_the_tag_list( '', $split_symbol );
    $label_list   = get_the_term_list( $post->ID, 'label', '', $split_symbol );
    $tags_list    = has_tag() ? $tag_list : $label_list;
    if ( $tags_list && !is_wp_error( $tags_list ) && ( is_tag() || is_tax( 'label' ) || is_single() ) ) { // Tags of the post.

        $tags_arr = explode( $split_symbol, $tags_list );
        $tags_num = count( $tags_arr );
        if ( $tags_num > 3 ) {
            for( $i = 0; $i < 3; $i ++ ) {
                $tags .= $tags_arr[$i];
                if ( $i < 2 ) {
                    $tags .= $split_symbol;
                }
            }
        } else {
            $tags = $tags_list;
        }
        $output .= sprintf( '<span class="post-tags"><span class="screen-reader-text">%1$s: </span>%2$s</span> ',
            _x( 'Tags', 'Used before tag names.', 'shawtheme' ),
            $tags
        );

    }

    if ( is_attachment() && wp_attachment_is_image() ) { // Size of the image attachment.

        $output .= sprintf( '<span class="image-size"><span class="screen-reader-text">%1$s: </span><a target="_blank" href="%2$s">%3$s&times;%4$s</a></span> ',
            esc_html_x( 'Full size', 'Used before full size attachment link.', 'shawtheme' ),
            esc_url( wp_get_attachment_url() ),
            absint( wp_get_attachment_metadata()['width'] ),
            absint( wp_get_attachment_metadata()['height'] )
        );

    }

    $seize_symbol = _x( '?', 'Used as a counts placeholder for protected posts.', 'shawtheme' );

    if ( shawtheme_has_meta( 'views' ) ) { // Views count of the post.

        $the_count = !post_password_required() ? post_views_count() : $seize_symbol;
        $the_views = __( 'Views', 'shawtheme' ) . '(' . $the_count . ')';
        $the_views = !is_single() ? '<a href="' . $post_link . '#content">' . $the_views . '</a>' : $the_views;
        $output   .= sprintf( '<span class="post-views"><span class="screen-reader-text">%1$s: </span>%2$s</span> ',
            _x( 'Views count', 'Used before post views.', 'shawtheme' ),
            $the_views
        );

    }

    if ( shawtheme_has_meta( 'likes' ) ) { // Likes count of the post.

        $the_count = !post_password_required() ? post_likes_count() : $seize_symbol;
        $the_likes = __( 'Likes', 'shawtheme' ) . '(<span class="count">' . $the_count . '</span>)';
        $the_likes = !post_password_required() ? '<a href="' . $post_link . '#post-likes">' . $the_likes . '</a>' : $the_likes;
        $output   .= sprintf( '<span class="post-likes"><span class="screen-reader-text">%1$s: </span>%2$s</span> ',
            _x( 'Likes count', 'Used before post likes.', 'shawtheme' ),
            $the_likes
        );

    }

    if ( post_type_supports( get_post_type(), 'comments' ) && ( comments_open() || get_comments_number() || !is_singular() ) ) { // Comments count of the post.

        if ( get_post_type() == 'page' && !comments_open() && !get_comments_number() ) {
            $output .= '';
        } else {
            $discussion  = shawtheme_get_discussion_data();
            $switch_a    = is_singular() && !empty( $discussion ) && shawtheme_has_meta( 'comments' ) ? true : false;
            $switch_b    = !post_password_required() && ( comments_open() || get_comments_number() ) ? true : false;
            $class       = $switch_a ? 'has-avatar' : null;
            $the_avatars = $switch_a ? shawtheme_comment_avatars_markup( $discussion->authors ) : null;
            if ( !comments_open() && !post_password_required() ) { $seize_symbol = '&times;'; }
            $anchor      = shawtheme_has_meta( 'comments' ) ? '#comments' : '#respond';
            $the_count   = $switch_b ? number_format_i18n( get_comments_number() ) : $seize_symbol;
            $comments    = __( 'Comments', 'default' ) . '(' . $the_count . ')';
            $comments    = $switch_b ? '<a href="' . $post_link . $anchor . '">' . $comments . '</a>' : $comments;
            $output     .= sprintf('<span class="post-comments %1$s"><span class="screen-reader-text">%2$s: </span>%3$s</span>',
                $class,
                _x( 'Comments count', 'Used before post comments.', 'shawtheme' ),
                $the_avatars . $comments
            );
        }

    }

    if ( current_user_can( 'edit_post' ) ) { // Edit link of the post.

        $output .= sprintf( '<span class="post-editor"><span class="screen-reader-text">%1$s: </span><a class="post-edit-link" href="%2$s">%3$s<span class="screen-reader-text">%4$s</span></a></span>',
            __( 'Edit link', 'shawtheme' ),
            get_edit_post_link(),
            __( 'Edit', 'default' ),
            get_the_title()
        );

    }

    $output .= '</div>';
    echo $output;
}

/*****************
 * 6. Pages list *
 *****************/
function shawtheme_list_pages( array $args ) {
    // Default parameter.
    $defaults = array(
        'tag'     => '',
    );

    $parsed_args = wp_parse_args( $args, $defaults );

    global $post;
    $parented   = $post->post_parent;
    $the_id     = $parented ? array_reverse( get_post_ancestors( $post->ID ) )[0] : $post->ID;
    $the_type   = get_post_type( $post->ID );
    $type_obj   = get_post_type_object( $the_type );
    $pages_list = wp_list_pages( array(
        'post_type'    => $the_type,
        'child_of'     => $the_id,
        // 'authors'      => get_the_author(),
        // 'date_format'  => 'Y年n月j日 H:i',
        // 'depth'        => 0,
        'echo'         => 0,
        // 'include'      => $post->ID,
        // 'link_before'  => '<span class="number">[1]</span><span class="screen-reader-text">',
        // 'link_after'   => '</span>',
        // 'show_date'    => 'modified',
        'sort_column'  => 'menu_order', // or 'post_parent'
        'title_li'     => '',
        'item_spacing' => 'discard'
    ) );
    if ( $pages_list ) {
        $item_class = $parented ? 'current_page_ancestor' : 'current_page_item';
        $the_class  = ( $parented == $the_id ) ? ' current_page_parent' : null;
        if ( $parsed_args['tag'] == 'nav' ) {
            $the_before = sprintf( '<nav class="navigation page-navigation" role="navigation" aria-label="%1$s"><h2 class="screen-reader-text">%s</h2>',
                __( 'Page Navigation', 'shawtheme' )
            );
            $the_after  = '</nav>';
        } elseif ( $parsed_args['tag'] == 'section' ) {
            $the_before = sprintf( '<section class="post-parts area"><h3 class="widget-title">%1$s</h3>',
                sprintf( _x( '%s Parts', 'Used in pages list title.', 'shawtheme' ), $type_obj->label )
            );
            $the_after  = '</section>';
        }
        printf(
            '%1$s<ul class="pages-list"><li class="page_item page-item-%2$s page_item_has_children %3$s"><a href="%4$s">%5$s</a><ul class="children children_main">%6$s</ul></li></ul>%7$s',
            $the_before,
            $the_id,
            $item_class . $the_class,
            esc_url( get_permalink( $the_id ) ),
            get_the_title( $the_id ),
            $pages_list,
            $the_after
        );
    }
}

/*********************
 * 7. Custom Sidebar *
 *********************/
function shawtheme_custom_sidebar() {
    if ( is_search() ) {

        get_template_part( 'subparts/search', 'filter' );

    } elseif ( is_category() ) {

        printf( '<section class="widget widget_categories area"><h3 class="widget-title">%1$s</h3><ul class="categories-list" role="list">%2$s</ul></section>',
            __( 'All Categories', 'default' ),
            wp_list_categories( array(
                'echo'         => 0,
                'hierarchical' => 1,
                'separator'    => '',
                'show_count'   => 1,
                'title_li'     => ''
            ) )
        );

    } elseif ( is_tag() ) {

        printf( '<section class="widget widget_tag_cloud area"><h3 class="widget-title">%1$s</h3>%2$s</section>',
            __( 'All Tags', 'default' ),
            wp_tag_cloud( array(
                'smallest'   => 1, 
                'largest'    => 1,
                'unit'       => 'em',
                'number'     => 0,
                'format'     => 'list',
                'separator'  => '',
                'show_count' => 1,
                'echo'       => 0
            ) )
        );

    } elseif ( is_author() ) {

        global $author;
        $userdata = get_userdata( $author );
        $avatar   = get_avatar( $userdata->user_email, 120, $default, __( 'Author&rsquo;s avatar', 'shawtheme' ), array( 'force_display' => true, ) );
        printf( '<section class="widget author-profile area"><table>%1$s<tbody><tr>%2$s</tr><tr>%3$s</tr>%4$s<tr>%5$s</tr></tbody></table></section>',
            sprintf( '<thead><tr><th colspan="8">%s</th></tr></thead>',
                __( 'Author Profile', 'shawtheme' )
            ),
            sprintf( '<td colspan="2">%1$s:</td><td colspan="3">%2$s</td><td colspan="3" rowspan="3">%3$s</td>',
                __( 'Nicename', 'shawtheme' ),
                $userdata->display_name,
                $avatar
            ),
            sprintf( '<td colspan="2">%1$s:</td><td colspan="3">%2$s %3$s</td>',
                __( 'Fullname', 'shawtheme' ),
                $userdata->first_name,
                $userdata->last_name
            ),
            sprintf( '<tr><td colspan="2">%1$s:</td><td colspan="3"><a href="mailto:%2$s">%2$s</a></td></tr><tr><td colspan="2">%3$s:</td><td colspan="6"><a target="_blank" href="%4$s">%4$s</a></td></tr>',
                __( 'Email', 'shawtheme' ),
                $userdata->user_email,
                __( 'Website', 'shawtheme' ),
                $userdata->user_url
            ),
            sprintf( '<td colspan="2">%1$s:</td><td colspan="6">%2$s</td>',
                __( 'Description', 'shawtheme' ),
                $userdata->description
            )
        );

    } elseif ( is_date() ) {

        printf( '<section class="widget widget_archive area"><h3 class="widget-title">%1$s</h3><select name="archive-dropdown" onchange="document.location.href=this.options[this.selectedIndex].value;"><option value="">%2$s</option>%3$s</select></section>',
            __( 'Date Archive', 'shawtheme' ),
            esc_attr( __( 'Select Month', 'default' ) ),
            wp_get_archives( 'type=monthly&format=option&show_post_count=1&echo=0&post_type=post' )
        );
        printf( '<section class="widget widget_calendar area"><h3 class="widget-title">%1$s</h3>%2$s</section>',
            __( 'Blog Calendar', 'shawtheme' ),
            get_calendar( true, false )
        );

    } elseif ( is_tax( 'post_format' ) ) {

        $post_formats = get_theme_support( 'post-formats' );
        $post_formats = $post_formats[0];
        foreach ( $post_formats as $post_format ) {
            $format_name   = ucfirst( $post_format );
            $format_links .= sprintf( '<a class="format-link" href="%1$s"><img src="%2$s/assets/img/post_format.jpg" alt="%4$s"><span class="format-name"><span class="format-%3$s">%4$s</span></span></a> ',
                esc_url( get_post_format_link( $post_format ) ),
                esc_url( get_template_directory_uri() ),
                esc_attr( $post_format ),
                _x( $format_name, 'Post format', 'default' )
            );
        }
        printf( '<section class="widget post-formats area"><h3 class="widget-title">%1$s</h3><div class="format-links">%2$s</div></section>',
            __( 'Post Formats', 'shawtheme' ),
            $format_links
        );
        
    } /*elseif ( is_post_type_archive() ) {
        printf( '<section class="widget archive-meta area"><h3 class="widget-title">%1$s</h3><div>%2$s</div></section>',
            __( 'Archive', 'shawtheme' ),
            '存档页占位区'
        );
    }*/ elseif ( is_tax() ) { // 自定义分类法归档
        $the_object      = get_queried_object();
        $the_tax         = get_taxonomy( $the_object->taxonomy );
        $the_name        = $the_tax->name;
        $the_description = $the_tax->description;
        $the_description = $the_description ? sprintf( '<p class="tax-description">%s</p>', esc_html( $the_description ) ) : null;
        if ( $the_tax->hierarchical ) {
            printf( '<section class="widget widget_categories area"><h3 class="widget-title">%1$s</h3>%2$s<ul class="categories-list" role="list">%3$s</ul></section>',
                __( 'All Categories', 'default' ),
                $the_description,
                wp_list_categories( array(
                    'echo'         => 0,
                    'hierarchical' => 1,
                    'separator'    => '',
                    'show_count'   => 1,
                    // 'hide_empty'   => 0,
                    'taxonomy'     => $the_name,
                    'title_li'     => ''
                ) )
            );  
        } else {
            printf( '<section class="widget widget_tag_cloud area"><h3 class="widget-title">%1$s</h3>%2$s%3$s</section>',
                __( 'All Tags', 'default' ),
                $the_description,
                wp_tag_cloud( array(
                    'smallest'   => 1, 
                    'largest'    => 1,
                    'unit'       => 'em',
                    'number'     => 0,
                    'format'     => 'list',
                    'separator'  => '',
                    'show_count' => 1,
                    // 'hide_empty' => 0,
                    'taxonomy'   => $the_name,
                    'echo'       => 0
                ) )
            );
        }
    }
}