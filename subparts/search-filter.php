<?php
/**
 * <= The template part for search filter =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */
?>

<section class="widget search-filter area">
    <h3 class="screen-reader-text"><?php _e( 'Post Filters', 'shawtheme' ); ?></h3>
    <div class="filter-body">
        <p class="filter-notes">
            <?php _e( 'Found', 'shawtheme' ); ?> <span class="results-count"><?php global $wp_query; echo $wp_query->found_posts; ?></span> <?php _e( 'results.', 'shawtheme' ); ?>
        </p>
        <form method="get" class="filter-form" action="/">
            <input type="hidden" name="s" value="<?php get_search_query(); ?>">
            <input type="hidden" name="cat" value="<?php global $cat; echo $cat; ?>">
            <p class="filter-group">
                <b class="filter-group-notes"><?php _e( 'Post Type', 'shawtheme' ); ?>: </b>
                <select name="post_type">
                    <option value=""><?php _e( 'All', 'default' ); ?></option>
                    <?php
                        $post_types = get_post_types( array( 'public' => true, 'exclude_from_search'=> false ), 'objects' );
                        foreach ( $post_types as $post_type ) {
                            if ( $post_type->name === 'page' || $post_type->name === 'attachment' ) { // 排除“页面”和“媒体”内容类型
                               continue;
                            } else {
                               $labels = get_post_type_labels( $post_type );
                                printf( '<option value="%1$s">%2$s</option>', esc_attr( $post_type->name ), esc_html( $labels->name ) );
                            }
                        }
                    ?>
                </select>
            </p>

            <?php if ( current_theme_supports( 'post-formats' ) ) : ?>
                <p class="filter-group">
                    <b class="filter-group-notes"><?php _e( 'Post Formats', 'shawtheme' ); ?>: </b>
                    <label><input type="radio" name="post_format" value="" checked><?php _e( 'All', 'default' ); ?></label>
                    <?php
                        $post_formats = get_theme_support( 'post-formats' );
                        $post_formats = $post_formats[0];
                        foreach ( $post_formats as $post_format ) {
                            $format_name = ucfirst( $post_format );
                            printf( '<label><input type="radio" name="post_format" value="%1$s">%2$s</label> ', $post_format, _x( $format_name, 'Post format', 'default' ) );
                        }
                    ?>
                </p>
            <?php endif; ?>

            <p class="filter-group">
                <b class="filter-group-notes"><?php _e( 'Order', 'default' ); ?>: </b>
                <select name="orderby">
                    <option value=""><?php _e( 'Default', 'default' ); ?></option>
                    <?php if ( is_multi_author() ) { ?>
                    <option value="author"><?php _e( 'By Author', 'shawtheme' ); ?></option>
                    <?php } ?>
                    <option value="title"><?php _e( 'By Title', 'shawtheme' ); ?></option>
                    <option value="date"><?php _e( 'By Date', 'shawtheme' ); ?></option>
                    <option value="modified"><?php _e( 'By Modify', 'shawtheme' ); ?></option>
                    <option value="comment_count"><?php _e( 'By Comment', 'shawtheme' ); ?></option>
                    <option value="rand"><?php _e( 'By Rand', 'shawtheme' ); ?></option>
                </select>
            </p>

            <p class="filter-apply">
                <button type="submit" class="filter-submit"><?php _e( 'Apply Filter', 'shawtheme' ); ?></button>
            </p>
        </form>
    </div><!-- .filter-body -->
</section><!-- .search-filter -->