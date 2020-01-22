<?php
/**
 * <= The guestbook comments template file =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

if ( post_password_required() ) {
    return;
} ?>

<div id="comments" class="comments-area area">

    <?php

    //# Comment form.
    $req           = get_option( 'require_name_email' );
    $required_text = sprintf( ' ' . __( 'Required fields are marked %s', 'default' ), '<span class="required">*</span>' );
    $commenter     = wp_get_current_commenter();
    $aria_req      = ( $req ? " aria-required='true'" : '' );
    $fields        = array(
        'author' => '<p class="comment-form-author"><label for="author">' . __( 'Name', 'shawtheme' ) . ( $req ? '<span class="required">*</span>' : '' ) . '</label>' . '<input id="author" name="author" type="text" placeholder="' . __( 'Enter Name', 'shawtheme' ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',

        'email'  => '<p class="comment-form-email"><label for="email">' . __( 'Email', 'shawtheme' ) . ( $req ? '<span class="required">*</span>' : '' ) . '</label>' . '<input id="email" name="email" type="email" placeholder="' . __( 'Enter Email', 'shawtheme' ) . '" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',

        'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website', 'shawtheme' ) . '</label>' . '<input id="url" name="url" type="url" placeholder="' . __( 'Enter Website', 'shawtheme' ) . '" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>'
    );
    if ( has_action( 'set_comment_cookies', 'wp_set_comment_cookies' ) && get_option( 'show_comments_cookies_opt_in' ) ) {
        $consent           = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';
        $fields['cookies'] = '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' />' . '<label for="wp-comment-cookies-consent">' . __( 'Remember my information.', 'shawtheme' ) . '</label></p>';
    }
    $textholder =  have_comments() ? __( 'Enter your message here ...', 'shawtheme' ) : __( 'No messages yet, be the first !', 'shawtheme' );
    comment_form(
        array(
            'fields'               => $fields,
            'comment_field'        => '<p class="comment-form-comment"><label class="screen-reader-text" for="comment">' . _x( 'Comment', 'Used before comment textarea.', 'shawtheme' ) . '</label><textarea id="comment" name="comment" rows="5" placeholder="' . $textholder . '" aria-required="true"></textarea></p>',
            'comment_notes_before' => '<p class="comment-notes"><span id="email-notes">' . __( 'Your email address will not be published.', 'default' ) . '</span>' . ( $req ? $required_text : '' ) . '</p>',
            'comment_notes_after'  => '<p class="comment-allowed-tags">' . sprintf( __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s', 'shawtheme' ), ' <code>' . allowed_tags() . '</code>' ) . '</p>',
            'title_reply'          => __( 'Leave a message', 'shawtheme' ),
            'label_submit'         => __( 'Post message', 'shawtheme' ),
        )
    );

    if ( have_comments() ) : ?>
        <h3 class="comments-title">
            <?php printf( __( '%s Messages', 'shawtheme' ), number_format_i18n( get_comments_number() ) ); ?>
        </h3>

        <ol class="comment-list">
            <?php
                wp_list_comments(
                    array(
                        'style'       => 'ol',
                        'short_ping'  => true,
                        'avatar_size' => 64,
                        'walker'      => new Shawtheme_Comment_Walker(),
                    )
                );
            ?>
        </ol><!-- .comment-list -->

        <?php
            // Comments pagination.
            the_comments_pagination(
                array(
                    'end_size'  => 3,
                    'mid_size'  => 3,
                    'prev_text' => '&lt; <span class="screen-reader-text">' . __( 'Previous', 'shawtheme' ) . '</span>',
                    'next_text' => '<span class="screen-reader-text">' . __( 'Next', 'shawtheme' ) . '</span> &gt;',
                    'before_page_number' => '<span class="screen-reader-text">' . _x( 'The', 'Used before pagination page number.', 'shawtheme' ) . '</span>',
                    'after_page_number' => '<span class="screen-reader-text">' . _x( 'page', 'Used after pagination page number.', 'shawtheme' ) . '</span>',
                )
            );

    endif; // Check for have_comments().

    // If comments are closed and there are comments, let's leave a little note, shall we?
    if ( !comments_open() && get_comments_number() ) {

        printf( '<p class="no-comments">%s</p>', __( 'Guestbook are closed.', 'shawtheme' ) );

    }
?>

</div><!-- .comments-area -->