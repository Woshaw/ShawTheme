<?php
/**
 * Template Name: Homepage
 * Template Post Type: page
 * Description: Homepage page-template for shawtheme.
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */

get_header(); 
$more_text = __( 'More >', 'shawtheme' ); ?>

    <main id="main" class="site-main" role="main">

            <section id="slider" class="istop sticky-posts slider area">
                <h2 class="istop-title screen-reader-text"><?php _e( 'Featured Posts', 'shawtheme' ); ?></h2>
                <?php
                // Loop 5 sticky posts for page slider.
                $sticky_posts_query = new WP_Query( array(
                    'post_type'      => 'post',
                    'post__in'       => get_option( 'sticky_posts' ),
                    'posts_per_page' => 5
                ) );
                if ( $sticky_posts_query->have_posts() ) : ?>

                    <ul class="sticky-posts-list">
                        <?php
                        // Start the loop.
                        while ( $sticky_posts_query->have_posts() ) : $sticky_posts_query->the_post(); ?>

                            <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                <?php
                                    the_title(
                                        sprintf(
                                            '<h1 class="entry-title"><a href="%1$s" title="%2$s" rel="bookmark">',
                                            esc_url( get_permalink() ),
                                            the_title_attribute( 'echo=0' )
                                        ),
                                        '</a></h1>'
                                    );

                                    shawtheme_thumbnail();

                                    edit_post_link(
                                        sprintf(
                                            wp_kses(
                                                /* translators: %s: Name of current post. Only visible to screen readers */
                                                __( 'Edit <span class="screen-reader-text">%s</span>', 'shawtheme' ),
                                                array(
                                                    'span' => array(
                                                        'class' => array(),
                                                    ),
                                                )
                                            ),
                                            get_the_title()
                                        ),
                                        sprintf( '<span class="post-editor"><span class="screen-reader-text">%s: </span>',  __( 'Edit link', 'shawtheme' ) ),
                                        '</span>'
                                    );
                                ?>
                            </li>

                        <?php
                        // End the loop.
                        endwhile;
                        ?>
                    </ul>
                    <div class="toggle-links"></div>
                    <nav class="nav-links" aria-label="<?php esc_attr_e( 'Slider Navigation', 'shawtheme' ); ?>">
                        <a class="prev" href="javascript:;"> &lt; </a>
                        <a class="next" href="javascript:;"> &gt; </a>
                    </nav>
                        
                    <script type="text/javascript">
                        window.onload = function() {
                            function $( element ) {
                                if( arguments[1] == true ) {
                                    return document.querySelectorAll( element );
                                } else {
                                    return document.querySelector( element );
                                }
                            }
                            // const $slider = $( '#slider' );
                            const $postsList = $( '.sticky-posts-list li', true );
                            const $toggleLnks = $( '.toggle-links' );
                            const $navLinks = $( '.nav-links' );
                            const $length = $postsList.length;

                            let toggles = '';
                            for( let i=0; i<$length; i++ ) {
                                if( i == 0 ) {
                                    toggles += '<a href="javascript:;" class="toggled-on">' + ( i+1 ) + '</a>';
                                } else {
                                    toggles += '<a href="javascript:;">' + (i+1) + '</a>';
                                }
                            }
                            $toggleLnks.innerHTML = toggles;

                            let current = 0;

                            function backward() {
                                for( let i=0; i<$length; i++ ) {
                                    $postsList[i].style.display = 'none';
                                    $toggleLnks.children[i].className = '';
                                }
                                if( current == 0 ) {
                                    current = $length;
                                }
                                $postsList[current-1].style.display = 'block';
                                $toggleLnks.children[current-1].className = 'toggled-on';
                                current--;
                            }
                            function forward() {
                                for( let i=0; i<$length; i++ ) {
                                    $postsList[i].style.display = 'none';
                                    $toggleLnks.children[i].className = '';
                                }
                                if( $length == current ) {
                                    current = 0;
                                }
                                $postsList[current].style.display = 'block';
                                $toggleLnks.children[current].className = 'toggled-on';
                                current++;
                            }

                            let timer;
                            timer = setInterval( forward, 1500 );

                            for( let i=0; i<$length; i++ ) {
                                $postsList[i].onmouseover = function() {
                                    clearInterval( timer );
                                }
                                $postsList[i].onmouseout = function() {
                                    timer = setInterval( forward, 1500 );
                                }
                            }
                            for( let i=0; i<$navLinks.children.length; i++ ) {
                                $navLinks.children[i].onmouseover = function() {
                                    clearInterval( timer );
                                };
                                $navLinks.children[i].onmouseout = function() {
                                    timer = setInterval( forward, 1500 );
                                }
                            }

                            for( let i=0; i<$length; i++ ) {
                                $toggleLnks.children[i].index = i;
                                $toggleLnks.children[i].onmouseover = function() {
                                    clearInterval( timer );
                                    for( let i=0; i<$length; i++ ) {
                                        $postsList[i].style.display = 'none';
                                        $toggleLnks.children[i].className = '';
                                    }
                                    this.className = 'toggled-on';
                                    $postsList[this.index].style.display = 'block';
                                    current = this.index +1;
                                }
                                $toggleLnks.children[i].onmouseout = function() {
                                    timer = setInterval( forward, 1500 );
                                }
                            }
                            
                            $navLinks.children[0].onclick = function() {
                                backward();
                                return false;
                            }
                            $navLinks.children[1].onclick = function() {
                                forward();
                                return false;
                            }
                        }
                    </script>

                <?php
                    wp_reset_postdata();
                else :

                    printf( '<p class="not-found">%s<p>', __( 'No sticky post yet.', 'shawtheme' ) );

                endif; ?>
            </section><!-- .sticky-posts -->

            <section class="istop recent-posts">
                <h2 class="istop-title screen-reader-text">
                    <?php _e( 'Recent Posts', 'shawtheme' ); ?>
                    <small><a class="more-link" href="#"><?php echo $more_text; ?></a></small>
                </h2>
                <?php
                // Loop 6 recent posts.
                $recent_posts_query = new WP_Query( array(
                    'post_type'      => 'post',
                    'posts_per_page' => 6
                ) );
                if ( $recent_posts_query->have_posts() ) : ?>

                    <ul class="istop-list recent-posts-list">
                        <?php
                        // Start the loop.
                        while ( $recent_posts_query->have_posts() ) : $recent_posts_query->the_post(); ?>

                            <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                <?php
                                    shawtheme_thumbnail();

                                    the_title(
                                        sprintf(
                                            '<h1 class="entry-title"><a href="%1$s" title="%2$s" rel="bookmark">',
                                            esc_url( get_permalink() ),
                                            the_title_attribute( 'echo=0' )
                                        ),
                                        '</a></h1>'
                                    );

                                    edit_post_link(
                                        sprintf(
                                            wp_kses(
                                                /* translators: %s: Name of current post. Only visible to screen readers */
                                                __( 'Edit <span class="screen-reader-text">%s</span>', 'shawtheme' ),
                                                array(
                                                    'span' => array(
                                                        'class' => array(),
                                                    ),
                                                )
                                            ),
                                            get_the_title()
                                        ),
                                        sprintf( '<span class="post-editor"><span class="screen-reader-text">%s: </span>',  __( 'Edit link', 'shawtheme' ) ),
                                        '</span>'
                                    );
                                ?>
                            </li>

                        <?php
                        // End the loop.
                        endwhile;
                        ?>
                    </ul>

                <?php
                    wp_reset_postdata();
                else :

                    printf( '<p class="not-found">%s<p>', __( 'No post yet.', 'shawtheme' ) );

                endif; ?>
            </section><!-- .recent-posts -->

            <section class="istop recent-tutorials">
                <h2 class="istop-title">
                    <?php _e( 'Recent Tutorials', 'shawtheme' ); ?>
                    <small><a class="more-link" href="#"><?php echo $more_text; ?></a></small>
                </h2>
                <?php
                // Loop 5 recent tutorials.
                $recent_tutorials_query = new WP_Query( array(
                    'post_type'      => 'tutorial',
                    'posts_per_page' => 5
                ) );
                if ( $recent_tutorials_query->have_posts() ) : ?>

                    <ul class="istop-list recent-tutorials-list">
                        <?php
                        // Start the loop.
                        while ( $recent_tutorials_query->have_posts() ) : $recent_tutorials_query->the_post(); ?>

                            <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                <?php
                                    shawtheme_thumbnail();

                                    the_title(
                                        sprintf(
                                            '<h1 class="entry-title"><a href="%1$s" title="%2$s" rel="bookmark">',
                                            esc_url( get_permalink() ),
                                            the_title_attribute( 'echo=0' )
                                        ),
                                        '</a></h1>'
                                    );

                                    edit_post_link(
                                        sprintf(
                                            wp_kses(
                                                /* translators: %s: Name of current post. Only visible to screen readers */
                                                __( 'Edit <span class="screen-reader-text">%s</span>', 'shawtheme' ),
                                                array(
                                                    'span' => array(
                                                        'class' => array(),
                                                    ),
                                                )
                                            ),
                                            get_the_title()
                                        ),
                                        sprintf( '<span class="post-editor"><span class="screen-reader-text">%s: </span>',  __( 'Edit link', 'shawtheme' ) ),
                                        '</span>'
                                    );
                                ?>
                            </li>

                        <?php
                        // End the loop.
                        endwhile;
                        ?>
                    </ul>

                <?php
                    wp_reset_postdata();
                else :

                    printf( '<p class="not-found">%s<p>', __( 'No tutorial yet.', 'shawtheme' ) );

                endif; ?>
            </section><!-- .recent-tutorials -->

            <section class="istop recent-resources">
                <h2 class="istop-title">
                    <?php _e( 'Recent Resources', 'shawtheme' ); ?>
                    <small><a class="more-link" href="#"><?php echo $more_text; ?></a></small>
                </h2>
                <?php
                // Loop 5 recent resources.
                $recent_resources_query = new WP_Query( array(
                    'post_type'      => 'resource',
                    'posts_per_page' => 5
                ) );
                if ( $recent_resources_query->have_posts() ) : ?>

                    <ul class="istop-list recent-resources-list">
                        <?php
                        // Start the loop.
                        while ( $recent_resources_query->have_posts() ) : $recent_resources_query->the_post(); ?>

                            <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                <?php
                                    shawtheme_thumbnail();

                                    the_title(
                                        sprintf(
                                            '<h1 class="entry-title"><a href="%1$s" title="%2$s" rel="bookmark">',
                                            esc_url( get_permalink() ),
                                            the_title_attribute( 'echo=0' )
                                        ),
                                        '</a></h1>'
                                    );

                                    edit_post_link(
                                        sprintf(
                                            wp_kses(
                                                /* translators: %s: Name of current post. Only visible to screen readers */
                                                __( 'Edit <span class="screen-reader-text">%s</span>', 'shawtheme' ),
                                                array(
                                                    'span' => array(
                                                        'class' => array(),
                                                    ),
                                                )
                                            ),
                                            get_the_title()
                                        ),
                                        sprintf( '<span class="post-editor"><span class="screen-reader-text">%s: </span>',  __( 'Edit link', 'shawtheme' ) ),
                                        '</span>'
                                    );
                                ?>
                            </li>

                        <?php
                        // End the loop.
                        endwhile;
                        ?>
                    </ul>

                <?php
                    wp_reset_postdata();
                else :

                    printf( '<p class="not-found">%s<p>', __( 'No resource yet.', 'shawtheme' ) );

                endif; ?>
            </section><!-- .recent-resources -->

    </main><!-- .site-main -->

    <?php get_sidebar();

get_footer(); ?>