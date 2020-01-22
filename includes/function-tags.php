<?php
/**
 * <= Custom template tags =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

/***************
 * 1. Headline *
 ***************/
function shawtheme_headline() {
?>
    <div class="site-headline">
    <?php
        // Headline image/video media.
        if ( is_singular() && has_post_thumbnail() ) {

            shawtheme_thumbnail();

        } elseif ( get_header_image() ) {

            the_custom_header_markup();

        } else {
            printf( '<div class="wp-custom-header" aria-hidden="true"><img src="%1$s" width="1200" height="300" alt="%2$s"></div>',
                get_template_directory_uri() . '/assets/img/header_image.jpg',
                esc_attr( get_bloginfo( 'name', 'display' ) )
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
        elseif ( is_archive() ) : ?>
            <div class="headline-body">
                <?php
                    the_archive_title( '<h1 class="headline-title">', '</h1>' );
                    the_archive_description( '<div class="headline-summary">', '</div><!-- .headline-summary -->' );
                ?>
            </div><!-- .headline-body -->
    <?php
        elseif ( is_singular() ) : ?>
            <div class="headline-body">
                <?php
                    the_title( '<h1 class="headline-title">', '</h1>' );
                    shawtheme_excerpt();
                ?>
            </div><!-- .headline-body -->
    <?php
        endif;
    ?>          
    </div><!-- .site-Headline -->
<?php
}

/******************
 * 2. Breadcrumbs *
 ******************/
function shawtheme_breadcrumbs() {
    if ( !is_front_page() && !is_home() || is_paged() ) {

        $delimiter = '<span class="separator" aria-hidden="true">&nbsp;&raquo;&nbsp;</span>';
        $before    = '<span class="current">';
        $output    = '<nav id="crumbs" class="site-breadcrumb" role="navigation" aria-label="' . esc_attr__( 'Breadcrumb', 'shawtheme' ) . '"><h2 class="screen-reader-text">' . __( 'Crumb Navigation', 'shawtheme' ) . '</h2><div class="nav-links"><span class="nav-meta">' . esc_html__( 'You are here', 'shawtheme' ) . ': </span><a class="home-link" href="' . home_url() . '" rel="home">' . esc_html__( 'Home', 'default' ) . '</a>' . $delimiter;

        if ( is_search() || is_archive() ) {

            if ( is_search() ) { // 搜索结果

                $output .= $before .  esc_html__( 'Searching for', 'shawtheme' ). ': <span class="search-keyword">' . get_search_query() . '</span>';

            } elseif ( is_category() ) { // 分类存档

                $query_obj  = get_queried_object();
                $this_id    = $query_obj->term_id;
                $this_cat   = get_category( $this_id );
                $parent_cat = get_category( $this_cat->parent );
                if ( $this_cat->parent != 0 ) {
                    $cat_name = get_category_parents( $parent_cat, true, $delimiter );
                    $output  .= str_replace( '<a', '<a class="category-link"', $cat_name );
                }
                $output .= $before . single_cat_title( '', false );

            } elseif ( is_tag() ) { // 标签存档

                $output .= $before . __( 'Tag Archives', 'shawtheme' ) . ': ' . single_tag_title( '', false );

            } elseif ( is_author() ) { // 作者存档

                global $author;
                $userdata = get_userdata( $author );
                $output  .= $before . __( 'Author Archives', 'shawtheme' ) . ': ' . $userdata->display_name;

            } elseif ( is_date() ) { // 日期存档

                $year_link = get_year_link( get_the_time( 'Y' ) );
                $the_year  = get_the_time( _x( 'Y', 'yearly archives date format', 'default' ) );
                $the_month = get_the_time( _x( 'm', 'monthly archives date format', 'shawtheme' ) );

                if ( is_day() ) { // 每日存档

                    $output .= '<a class="year-link" href="' . $year_link . '">'. $the_year .'</a>' . $delimiter . '<a class="month-link"  href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '">' . $the_month . '</a>' . $delimiter . $before . get_the_time( _x( 'd', 'daily archives date format', 'shawtheme' ) );

                } elseif ( is_month() ) { // 月份存档

                    $output .= '<a class="year-link" href="' . $year_link . '">' . $the_year . '</a>' . $delimiter . $before . $the_month;

                } elseif ( is_year() ) { // 年份存档

                    $output .= $before . $the_year;

                }
                
            } elseif ( current_theme_supports( 'post-formats', get_post_format() ) && is_tax( 'post_format' ) ) { //文章格式

                $output .= $before . __('Post Formats', 'shawtheme') . ': ' . get_post_format_string( get_post_format() );

            } elseif ( is_post_type_archive() ) {
                /* translators: Post type archive title. %s: Post type name. */
                // $title = sprintf( __( 'Archives: %s' ), post_type_archive_title( '', false ) );
            } elseif ( is_tax() ) {
                // $queried_object = get_queried_object();
                // if ( $queried_object ) {
                //     $tax = get_taxonomy( $queried_object->taxonomy );
                //      translators: Taxonomy term archive title. 1: Taxonomy singular name, 2: Current taxonomy term. 
                //     $title = sprintf( __( '%1$s: %2$s' ), $tax->labels->singular_name, single_term_title( '', false ) );
                // }
            }

            if ( !have_posts() ) { // 无内容
                $output .= '<span class="voided">[' .  esc_html__( 'No Content', 'shawtheme' ) . ']</span>';
            }

        } elseif ( is_singular() ) {
            global $post;
            $parented = $post->post_parent;

            if( is_single() ) { //# Posts/attachment or Non-hierarchical post type.

                $parent_post = get_post( $parented );
                $categories  = $parented ? get_the_category( $parent_post->ID ) : get_the_category();
                $category    = $categories[0];
                $parents     = get_category_parents( $category, true, $delimiter );
                if ( !is_wp_error( $parents ) ) { // Categories of single post.
                    $output .= str_replace ( '<a', '<a class="category-link"', $parents );
                }
                if ( $parented ) { // Parent post of attachment/single post.
                    $output .= '<a class="post-link" href="' . get_permalink( $parent_post ) . '">' . $parent_post->post_title . '</a>' . $delimiter;
                }

            } elseif ( $parented ) { //# Parents of page or hierarchical post type.

                $pages = array();
                while ( $parented ) {
                    $parent_page = get_page( $parented );
                    $pages[]     = '<a class="page-link" href="' . get_permalink( $parent_page->ID ) . '">' . get_the_title( $parent_page->ID ) . '</a>';
                    $parented    = $parent_page->post_parent;
                }
                $pages = array_reverse( $pages );
                foreach ( $pages as $page ) {
                    $output .= $page . $delimiter;
                }

            }

            $output .= $before . get_the_title(); //# Current content title.

            if ( is_attachment() ) { // 附件
                $output .= '<span class="attached">[' .  esc_html__( 'Attachment', 'shawtheme' ) . ']</span>';
            }

        }

        if ( is_home() ) { // Home paged.
            $output .= $before . __('Posts List', 'shawtheme');
        }

        if ( is_404() ) {
            $output .= $before . __('404 Page', 'shawtheme');
        }

        if ( is_paged() ) { // 分页
            $output .= '<span class="paged">[' . sprintf( __( 'Page %s', 'shawtheme' ), get_query_var( 'paged' ) ) . ']</span>';
        }

        $output   .= '</span></div></nav>';

        echo $output;
    }
}

/******************
 * 3. Format tags *
 ******************/
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

/*****************
 * 4. Post Entry *
 *****************/
//# Post thumbnail.
function shawtheme_thumbnail() {
    if ( is_singular() && has_post_thumbnail() ) {
    ?>
        <figure class="wp-custom-header" aria-hidden="true"><?php the_post_thumbnail( 'header-thumbnail' ); ?></figure>
    <?php
    } elseif ( !in_the_loop() && is_home() && is_sticky() && !is_paged() || is_search() ) {
        if ( has_post_thumbnail() ) {
    ?>
        <a class="post-thumbnail ratio-8to5-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true"><?php the_post_thumbnail( 'post-thumbnail', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?></a>
        <?php }
        else {
            printf(
                '<a class="post-thumbnail ratio-8to5-thumbnail" href="%1$s" aria-hidden="true"><img src="%2$s/assets/img/post_thumbnail.png" class="no-thumbnail" alt="%3$s"></a>',
                esc_url( get_permalink() ),
                esc_url( get_template_directory_uri() ),
                __( 'No featured image', 'shawtheme' )
            );
        }
    } elseif ( has_post_thumbnail() ) {
    ?>
        <a class="post-thumbnail ratio-4to1-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true"><?php the_post_thumbnail( 'entry-thumbnail', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?></a>
    <?php
    } else {
        printf(
            '<a class="post-thumbnail ratio-4to1-thumbnail" href="%1$s" aria-hidden="true"><img src="%2$s/assets/img/entry_thumbnail.png" class="no-thumbnail" alt="%3$s"></a>',
            esc_url( get_permalink() ),
            esc_url( get_template_directory_uri() ),
            __( 'No featured image', 'shawtheme' )
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
    if ( !is_singular() ) : ?>
        <div class="entry-summary">
            <?php if ( post_password_required() && !is_search() ) {
                the_content();
            } else {
                the_excerpt();
            } ?>
        </div><!-- .entry-summary -->
    <?php
    elseif ( !in_the_loop() && has_excerpt() ) : ?>
        <div class="headline-summary">
            <?php the_excerpt(); ?>
        </div><!-- .headline-summary -->
    <?php
    endif;
    
    if ( is_attachment() && in_the_loop() ) {
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
function post_views_count( $echo = false ) {
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
function post_likes_count( $echo = false ) {
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

//# Post meta.
function shawtheme_entry_meta() {
    $post_link = esc_url( get_permalink() );
    $output    = '<div class="entry-meta">';

    if ( post_type_supports( get_post_type(), 'author' ) && is_multi_author() && ( is_archive() || is_single() ) ) { // Author of the post.

        $class   = is_archive() ? ' has-avatar' : null;
        $author  = '<span class="author-name">' . get_the_author() . '</span>';
        $author  = is_archive() ? get_avatar( get_the_author_meta( 'user_email' ), 40 ) . $author : $author;
        $output .= '<span class="post-author' . $class . '"><span class="screen-reader-text">' . _x( 'Author', 'Used before post author name.', 'shawtheme' ) . ': </span><a class="url fn n" rel="author" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . $author . '</a></span> ';

    }

    if (  is_search() || is_date() || is_single() ) { // Publish date of the post.

        $the_date = '<time class="entry-date published" datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . get_the_date() . '</time>';
        $the_date =  get_post_type() == 'post' && !is_date() ? '<a href="' . get_month_link( get_the_time('Y'), get_the_time('m') ) . '">' . $the_date . '</a>' : $the_date;
        $output  .= '<span class="post-date"><span class="screen-reader-text">' . _x( 'Publish date', 'Used before publish date.', 'shawtheme' ) . ': </span>' . $the_date . '</span> ';

    }

    if ( is_home() || is_post_type_archive() ) { // Publish time of the post.

        $output .= '<span class="post-time"><span class="screen-reader-text">' . _x( 'Publish time', 'Used before publish time.', 'shawtheme' ) . ': </span><a  rel="bookmark" href="' . $post_link . '">' . shawtheme_time_format( get_gmt_from_date( get_the_time( 'Y-m-d G:i:s' ) ) ) . '</a></span> ';

    }

    if ( is_search() ) { // Type of the post.

        $the_type   = get_post_type();
        $the_link   = get_post_type_archive_link( $the_type ) ? get_post_type_archive_link( $the_type ) : $post_link;
        $the_object = get_post_type_object( $the_type );
        $output    .= '<span class="post-type"><span class="screen-reader-text">' . _x( 'Post type', 'Used before post type.', 'shawtheme' ) . ': </span><a  rel="bookmark" href="' . $the_link . '">' . $the_object->labels->name . '</a></span> ';

    }

    $format = get_post_format();
    if ( current_theme_supports( 'post-formats', $format ) && post_type_supports( get_post_type(), 'post-formats' ) && ( is_search() || is_tax( 'post_format' ) || is_single() ) ) { // Format of the post.

        $the_string = get_post_format_string( $format );
        $the_format = is_tax( 'post_format' ) ? $the_string : '<a href="' . esc_url( get_post_format_link( $format ) ) . '">' . $the_string . '</a>';
        $output    .= '<span class="post-format"><span class="screen-reader-text">' .  _x( 'Format', 'Used before post format.', 'shawtheme' ) . ': </span>'. $the_format . '</span> ';

    }

    $categories_list  = get_the_category_list( ',' );
    if ( $categories_list && ( is_category() || is_search() || is_single() ) ) { // Category of the post.

        $categories = explode( ',', $categories_list );
        $output    .= '<span class="post-category"><span class="screen-reader-text">' . _x( 'Category', 'Used before category name.', 'shawtheme' ) . ': </span>' . reset( $categories ) . '</span> ';

    }

    $split_symbol = _x( ', ', 'Used between list items, there is a space after the comma.', 'shawtheme' );
    $tags_list    = get_the_tag_list( '', $split_symbol );
    if ( $tags_list && ( is_tag() || is_single() ) ) { // Tags of the post.

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
        $output .= '<span class="post-tags"><span class="screen-reader-text">' . _x( 'Tags', 'Used before tag names.', 'shawtheme' ) . ': </span>' . $tags . '</span> ';

    }

    if ( is_attachment() && wp_attachment_is_image() ) { // Size of the image attachment.

         $output .= '<span class="image-size"><span class="screen-reader-text">' . esc_html_x( 'Full size', 'Used before full size attachment link.', 'shawtheme' ) . ': </span><a target="_blank" href="' . esc_url( wp_get_attachment_url() ) . '">' . absint( wp_get_attachment_metadata()['width'] ) . '&times;' . absint( wp_get_attachment_metadata()['height'] ) . '</a></span> ';

    }

    $seize_symbol = _x( '?', 'Used as a counts placeholder for protected posts.', 'shawtheme' );

    if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) { // Views count of the post.

        $the_count = !post_password_required() ? post_views_count() : $seize_symbol;
        $the_views = __( 'Views', 'shawtheme' ) . '(' . $the_count . ')';
        $the_views = !is_single() ? '<a href="' . $post_link . '#content">' . $the_views . '</a>' : $the_views;
        $output   .= '<span class="post-views"><span class="screen-reader-text">' . _x( 'Views count', 'Used before post views.', 'shawtheme' ) . ': </span>' . $the_views . '</span> ';

    }

    if ( in_array( get_post_type(), array( 'post' ) ) ) { // Likes count of the post.

        $the_count = !post_password_required() ? post_likes_count() : $seize_symbol;
        $the_likes = __( 'Likes', 'shawtheme' ) . '(<span class="count">' . $the_count . '</span>)';
        $the_likes = !post_password_required() ? '<a href="' . $post_link . '#post-likes">' . $the_likes . '</a>' : $the_likes;
        $output   .= '<span class="post-likes"><span class="screen-reader-text">' . _x( 'Likes count', 'Used before post likes.', 'shawtheme' ) . ': </span>' . $the_likes . '</span> ';

    }

    if ( post_type_supports( get_post_type(), 'comments' ) && ( comments_open() || get_comments_number() || !is_singular() ) ) { // Comments count of the post.

        if ( get_post_type() == 'page' && !comments_open() && !get_comments_number() ) {
            $output .= '';
        } else {
            $discussion  = shawtheme_get_discussion_data();
            $class       = !empty( $discussion ) && is_singular() ? ' has-avatar' : null;
            $the_avatars = !empty( $discussion ) && is_singular() ? shawtheme_comment_avatars_markup( $discussion->authors ) : null;
            if ( !comments_open() && !post_password_required() ) { $seize_symbol = '&times;'; }
            $anchor      = get_comments_number() > 0 ? '#comments' : '#respond';
            $the_count   = !post_password_required() && ( comments_open() || get_comments_number() ) ? number_format_i18n( get_comments_number() ) : $seize_symbol;
            $comments    = __( 'Comments', 'default' ) . '(' . $the_count . ')';
            $comments    = !post_password_required() && ( comments_open() || get_comments_number() ) ? '<a href="' . $post_link . $anchor . '">' . $comments . '</a>' : $comments;
            $output     .= '<span class="post-comments' . $class . '"><span class="screen-reader-text">' . _x( 'Comments count', 'Used before post comments.', 'shawtheme' ) . ': </span>' . $the_avatars . $comments . '</span> ';
        }

    }

    if ( current_user_can( 'edit_post' ) ) { // Edit link of the post.

        $output .= '<span class="post-editor"><span class="screen-reader-text">' . __( 'Edit link', 'shawtheme' ) . ': </span><a class="post-edit-link" href="' . get_edit_post_link() . '">' . __( 'Edit', 'default' ) . ' <span class="screen-reader-text">' . get_the_title() . '</span></a></span>';

    }

    $output .= '</div>';
    echo $output;
}

/**********************
 * 6. Page navigation *
 **********************/
//# Page navigation.
function shawtheme_page_navigation() {
    global $post;
    $item_class = $post->post_parent ? null : 'current_page_item';
    $the_id     = $post->post_parent ? $post->post_parent : $post->ID;
    $pages_list = wp_list_pages( 'title_li=&child_of=' . $the_id . '&item_spacing=discard&echo=0' );
    if ( $pages_list ) {
        printf(
            '<nav class="navigation page-navigation" role="navigation" aria-label="%1$s"><h2 class="screen-reader-text">%1$s</h2><ul class="page-list"><li class="page_item %2$s page_parent"><a href="%3$s">%4$s</a></li>%5$s</ul></nav>',
            __( 'Page Navigation', 'shawtheme' ),
            $item_class,
            esc_url( get_permalink( $the_id ) ),
            get_the_title( $the_id ),
            $pages_list
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
                'echo' => 0,
                'hierarchical' => 1,
                'separator' => '',
                'show_count' => 1,
                'title_li' => ''
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
        printf( '<section class="widget author-profile area"><table><thead><tr><th colspan="8">%1$s</th></tr></thead><tbody><tr><td colspan="2">%2$s:</td><td colspan="3">%3$s</td><td colspan="3" rowspan="3">%4$s</td></tr><tr><td colspan="2">%5$s:</td><td colspan="3">%6$s %7$s</td></tr><tr><td colspan="2">%8$s:</td><td colspan="3"><a href="mailto:%9$s">%9$s</a></td></tr><tr><td colspan="2">%10$s:</td><td colspan="6"><a target="_blank" href="%11$s">%11$s</a></td></tr><tr><td colspan="2">%12$s:</td><td colspan="6">%13$s</td></tr></tbody></table></section>',
            __( 'Author Profile', 'shawtheme' ),
            __( 'Nicename', 'shawtheme' ),
            $userdata->display_name,
            $avatar,
            __( 'Fullname', 'shawtheme' ),
            $userdata->first_name,
            $userdata->last_name,
            __( 'Email', 'shawtheme' ),
            $userdata->user_email,
            __( 'Website', 'shawtheme' ),
            $userdata->user_url,
            __( 'Description', 'shawtheme' ),
            $userdata->description
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

        echo '<section class="widget post-formats area"><h3 class="widget-title">' . __( 'Post Formats', 'shawtheme' ) . '</h3><div class="format-links">';
        $post_formats = get_theme_support( 'post-formats' );
        $post_formats = $post_formats[0];
        foreach ( $post_formats as $post_format ) {
            $format_name = ucfirst( $post_format );
            printf( '<a class="format-link" href="%1$s/type/%2$s"><img src="%3$s/assets/img/post_format.jpg" alt="%4$s"><span class="format-name"><span>%4$s</span></span></a> ',
                esc_url( home_url() ),
                $post_format,
                esc_url( get_template_directory_uri() ),
                _x( $format_name, 'Post format', 'default' )
            );
        }
        echo '</div></section>';
        
    } //elseif ( is_tax( get_post_type(). '_category' ) ) { //自定义Hierarchical taxonomy归档
    //     printf( '<section class="widget archive-meta"><h3 class="widget-title">%1$s</h3>%2$s</section>',
    //         __( '分类目录', 'shawtheme' ),
    //         '目录占位区'
    //     );
    // } elseif ( is_tax( get_post_type(). '_tag' ) ) { //自定义Non-hierarchical taxonomy归档
    //     printf( '<section class="widget archive-meta"><h3 class="widget-title">%1$s</h3>%2$s</section>',
    //         __( '标签', 'shawtheme' ),
    //         '标签占位区'
    //     );
    // }
}