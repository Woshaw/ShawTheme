<?php
/**
 * <= Custom comment walker for Shawtheme =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

class Shawtheme_Comment_Walker extends Walker_Comment {

    protected function html5_comment( $comment, $depth, $args ) {
        
        $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
?>
        <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $this->has_children ? 'parent' : '', $comment ); ?>>
            <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
                <footer class="comment-meta">
                    <span class="comment-author vcard">
                        <?php
                            $the_url    = get_comment_author_url( $comment );
                            $the_avatar = get_avatar( $comment, $args['avatar_size'] );
                            $the_author = get_comment_author( $comment );
                            $the_link   = get_comment_author_link( $comment );
                            if ( 0 != $args['avatar_size'] ) {
                                if ( empty( $the_url ) ) {
                                    printf(
                                        '%1$s<b class="fn">%2$s</b>',
                                        $the_avatar,
                                        $the_author
                                    );
                                } else {
                                    printf(
                                        '<a class="url" target="_blank" href="%1$s" rel="external nofollow">%2$s<b class="fn">%3$s</b></a>',
                                        $the_url,
                                        $the_avatar,
                                        $the_author
                                    );
                                }
                            } else {
                                $the_author = empty( $the_url ) ? $the_author : $the_link;
                                printf( '<b class="fn">%s</b>', $the_author );
                            }
                        ?>
                    </span>

                    <?php
                        printf( '<span class="screen-reader-text">%1$s</span> <span class="comment-time"><a href="%2$s"><time datetime="%3$s">%4$s</time></a></span> <span class="screen-reader-text">%5$s</span>',
                            _x( 'at', 'Used before comment date.', 'shawtheme' ),
                            esc_url( get_comment_link( $comment, $args ) ),
                            get_comment_time( 'c' ),
                            // sprintf( __( '%1$s at %2$s', 'default' ), get_comment_date( '', $comment ), get_comment_time() ),
                            shawtheme_time_format( get_gmt_from_date( get_comment_time( 'Y-m-d G:i:s' ) ) ),
                            __( 'says:', 'shawtheme' )
                        );
                    ?>
                </footer><!-- .comment-meta -->

                <div class="comment-content">
                    <?php
                        $commenter = wp_get_current_commenter();
                        if ( $commenter['comment_author_email'] ) {
                            $moderation_note = __( 'Your comment is awaiting moderation.', 'default' );
                        } else {
                            $moderation_note = __( 'Your comment is awaiting moderation. This is a preview, your comment will be visible after it has been approved.', 'default' );
                        }

                        if ( '0' == $comment->comment_approved ) {
                            printf( '<em class="comment-awaiting-moderation">%s</em>', $moderation_note );
                        }

                        comment_text();
                    ?>
                </div><!-- .comment-content -->

                <div class="comment-toolbar">
                    <?php comment_reply_link(
                        array_merge(
                            $args,
                            array(
                                'add_below' => 'div-comment',
                                'depth'     => $depth,
                                'max_depth' => $args['max_depth'],
                                'before'    => '<span class="reply-link">',
                                'after'     => '</span>',
                            )
                        )
                    ); ?>
                    <?php edit_comment_link( __( 'Edit', 'default' ), '<span class="edit-link">', '</span>' ); ?>
                </div><!-- .comment-toolbar -->
            </article><!-- .comment-body -->
<?php
    }   
}