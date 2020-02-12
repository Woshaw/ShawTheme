<?php
/**
 * <= The template part for displaying single-post content =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php
    // post metadata.
    shawtheme_entry_meta();
    // $the_terms = get_the_terms( $post->ID, 'subject' );
    // $the_term  = $the_terms[0];
    // $the_id    = $the_term->term_id;

    // echo '<pre>';
    // print_r($the_term);
    // echo '</pre>';
    ?>

    <div class="entry-content">
        <?php
            the_content();
            wp_link_pages( array(
                'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'shawtheme' ) . '</span>',
                'after'       => '</div>',
                'link_before' => '<span>',
                'link_after'  => '</span>',
                'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'shawtheme' ) . ' </span>%',
                'separator'   => '<span class="screen-reader-text">, </span>',
            ));
            // echo '<pre>';
            // $cat      = get_the_category();
            // // $cat_slug = $cat->slug;
            // $cat      = get_the_category( $post->ID );
            // $parent_id = $cat[0]->category_parent;
            // // $parents = get_the_category( $parent_id );
            // $obj    = get_term( $parent_id, 'category' );
            // print_r( $obj );
            // echo '</pre>';
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer">
        
        <?php
        if ( post_password_required() ) :

            printf(
                '<p class="post-protected-notes"><span class="hint">%1$s: </span>%2$s</p>',
                __( 'Little Hint', 'shawtheme' ),
                sprintf( __( 'This content is password protected, try send a email to <a target="_blank" href="mailto:%1$s" title="Author&#39;s Email">%1$s</a> to get the password.', 'shawtheme' ), get_the_author_meta( 'user_email' ) )
            );

        elseif ( is_singular( 'post' ) ) : ?>

            <div class="post-toolbar">

                <a id="post-likes" class="likes-link <?php echo get_likes_class();?>" data-id="<?php the_id();?>" data-action="liking" href="javascript:;" ><?php _e( 'Thumbs-up', 'shawtheme' );?>(<span class="count"><?php echo post_likes_count();?></span>)</a>

                <a id="post-share" class="share-link" href="javascript:;"><?php _e( 'Share', 'shawtheme' ); ?>(<span class="BSHARE_COUNT">0</span>)</a>

                <div class="bshare-custom icon-medium-plus" style="display: none;">
                    <div class="bsPromo bsPromo1"></div>
                    <a title="分享到QQ好友" class="bshare-qqim"></a>
                    <a title="分享到微信" class="bshare-weixin"></a>
                    <a title="分享到新浪微博" class="bshare-sinaminiblog"></a>
                    <a title="分享到网易微博" class="bshare-neteasemb"></a>
                    <a title="分享到电子邮件" class="bshare-email"></a>
                    <a title="更多平台" class="bshare-more bshare-more-icon more-style-addthis"></a>
                    <!-- <span class="BSHARE_COUNT bshare-share-count">0</span> -->
                </div>
                <script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/button.js#style=-1&amp;uuid=&amp;pophcol=2&amp;lang=zh"></script>
                <a class="bshareDiv" onclick="javascript:return false;"></a>
                <script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/bshareC0.js"></script>

            </div><!-- .post-toolbar -->

        <?php
            shawtheme_post_rights();
        endif;
        ?>

    </footer><!-- .entry-footer -->

</article><!-- .entry -->