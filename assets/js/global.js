/*
<= Shawtheme main javascript file =>
Description: Global javascript functions for ShawTheme.
Version: 1.0.0
*/

( function( $ ) {
    var body           = $( document.body );
    var masthead       = $( '#masthead' );
    var siteNavBar     = masthead.find( '#site-navbar' );

    function initNavigationBar( container ) {
        // Main menu toggle.
        var menuButton = $( '<button />', {
            'id': 'menu-toggle',
            'class': 'menu-toggle'
        } ).append( $( '<span />', {
            'class': 'screen-reader-text',
            text: screenReaderText.menuOn
        } ) );
        container.find( '#site-navigation' ).before( menuButton ); // Add menu toggle that displays main menu.
        // container.find( '.menu-toggle' ).add( container.find( '.site-menubar' ) ).attr( 'aria-expanded', 'false' ); // Add an initial values for the attribute.
        container.find( '.menu-toggle' ).click( function( e ) {
            var _this            = $( this ),
                screenReaderSpan = _this.find( '.screen-reader-text' );
            e.preventDefault();
            _this.add( container.find( '#site-navigation' ) ).toggleClass( 'toggled-on' );
            _this.add( container.find( '#site-navigation' ) ).attr( 'aria-expanded', _this.attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
            screenReaderSpan.text( screenReaderSpan.text() === screenReaderText.menuOn ? screenReaderText.menuOff : screenReaderText.menuOn );
        } ); // Menu toggle click event.

        // Child menu toggle.
        var dropdownButton = $( '<button />', {
            'class': 'dropdown-toggle',
            'aria-expanded': false
        } ).append( $( '<span />', {
            'class': 'screen-reader-text',
            text: screenReaderText.expand
        } ) );
        container.find( '.menu-item-has-children > a' ).after( dropdownButton ); // Add dropdown toggle that displays child menu items.
        container.find( '.current-menu-ancestor > button' ).add( container.find( '.current-menu-ancestor > .sub-menu' ) ).addClass( 'toggled-on' ); // Toggle buttons and submenu items with active children menu items.
        container.find( '.menu-item-has-children' ).attr( 'aria-haspopup', 'true' ); // Add menu items with submenus to aria-haspopup="true".
        container.find( '.dropdown-toggle' ).click( function( e ) {
            var _this            = $( this ),
                screenReaderSpan = _this.find( '.screen-reader-text' );
            e.preventDefault();
            _this.toggleClass( 'toggled-on' );
            _this.next( '.children, .sub-menu' ).toggleClass( 'toggled-on' );
            _this.attr( 'aria-expanded', _this.attr( 'aria-expanded' ) === 'false' ? 'true' : 'false' );
            screenReaderSpan.text( screenReaderSpan.text() === screenReaderText.expand ? screenReaderText.collapse : screenReaderText.expand );
        } ); // Dropdown toggle click event.
    }
    initNavigationBar( siteNavBar );

    var mainNavigation = masthead.find( '#site-navigation' );
    ( function() {
        if ( !mainNavigation.length || !mainNavigation.children().length ) {
            return;
        }

        // Toggle `focus` class to allow submenu access on tablets.
        function toggleFocusClassTouchScreen() {
            if ( window.innerWidth >= 992 ) {
                $( document.body ).on( 'touchstart.shawtheme', function( e ) {
                    if ( ! $( e.target ).closest( '.main-navigation li' ).length ) {
                        $( '.main-navigation li' ).removeClass( 'focus' );
                    }
                } );
                mainNavigation.find( '.menu-item-has-children > a' ).on( 'touchstart.shawtheme', function( e ) {
                    var el = $( this ).parent( 'li' );

                    if ( ! el.hasClass( 'focus' ) ) {
                        e.preventDefault();
                        el.toggleClass( 'focus' );
                        el.siblings( '.focus' ).removeClass( 'focus' );
                    }
                } );
            } else {
                mainNavigation.find( '.menu-item-has-children > a' ).unbind( 'touchstart.shawtheme' );
            }
        }

        if ( 'ontouchstart' in window ) {
            $( window ).on( 'resize.shawtheme', toggleFocusClassTouchScreen );
            toggleFocusClassTouchScreen();
        }

        mainNavigation.find( 'a' ).on( 'focus.shawtheme blur.shawtheme', function() {
            $( this ).parents( '.menu-item' ).toggleClass( 'focus' );
        } );
    } )();

    // Add the default ARIA attributes for the menu toggle and the navigations.
    var menuToggle     = masthead.find( '#menu-toggle' );
    function onResizeARIA() {
        if ( window.innerWidth < 992 ) {
            if ( menuToggle.hasClass( 'toggled-on' ) ) {
                menuToggle.attr( 'aria-expanded', 'true' );
            } else {
                menuToggle.attr( 'aria-expanded', 'false' );
            }

            if ( mainNavigation.hasClass( 'toggled-on' ) ) {
                mainNavigation.attr( 'aria-expanded', 'true' );
            } else {
                mainNavigation.attr( 'aria-expanded', 'false' );
            }

            menuToggle.attr( 'aria-controls', 'main-navigation' );
        } else {
            menuToggle.removeAttr( 'aria-expanded' );
            mainNavigation.removeAttr( 'aria-expanded' );
            menuToggle.removeAttr( 'aria-controls' );
        }
    }

    // 固定顶部导航栏
    var navTop = siteNavBar.offset().top, navH = siteNavBar.outerHeight(), winTop_1 = 0, winWidth = $( window ).width(), holder = jQuery( '<div>' );
    $( window ).on( 'scroll', function() {
        var winTop_2 = $( window ).scrollTop();
        holder.css( 'height', navH );
        // 开始浮动，但不显示.
        if ( winTop_2 > navTop && winWidth > 992 ) {
            holder.show().insertBefore( siteNavBar );
            siteNavBar.addClass( 'site-navbar-fixed' );
        }else {
            holder.hide();
            siteNavBar.removeClass( 'site-navbar-fixed' );
        }
        // 判断鼠标向上滚动，显示出来.
        if ( winTop_2 > winTop_1 && winWidth > 992 ) {
            siteNavBar.removeClass( 'toggled-on' );
        } else if( winTop_2 < winTop_1 ) {
            siteNavBar.addClass( 'toggled-on' );
        }
        winTop_1 = $( window ).scrollTop();
    });

    // 头部搜索框切换.
    var searchToggle = $( '#search-toggle' );
    var searchWrap   = $( '#site-search' );
    searchToggle.click( function ( event ) {
        // 防止默认事件.
        event.preventDefault();
        // 取消事件冒泡.
        event.stopPropagation();
        searchToggle.add( searchWrap ).toggleClass( 'toggled-on' );
    });

    // 定义overlay移除.
    function overlayRemove() {
        body.removeClass( 'site-overlay-appear' );
        $( '#overlay' ).fadeOut().remove();
    }

    // 定义动态overlay模块.
    function dynamicOverlay( hasModal, theModal, theID, theMain, theNotes ) {

        if ( hasModal == true ) {

            var modalWrap;

            if ( theModal == 'default' ) {

                var mainWrap;

                if ( theMain == 'default' ) {

                    mainWrap = $( '<p />', { 'class': 'modal-notes', text: theNotes } );

                } else {

                    mainWrap = theMain;

                }

                // 定义生成模块.
                modalWrap   = $( '<div />', { 
                    'class': 'site-overlay-wrap'
                } ).append( $( '<div />', {
                    'id': theID, 'class': 'site-modal ' + theID
                } ).append( $( '<header />', { 
                    'class': 'modal-header'
                } ).append( $( '<h3 />', {
                    'class': 'modal-title', text: screenReaderText.notes
                } ).append( $( '<button />', {
                    'class': 'modal-toggle'
                } ).append( $( '<span />', {
                    'class': 'screen-reader-text', text: '×'
                } ) ) ) ), $( '<div />', {
                    'class': 'modal-body'
                } ).append( mainWrap ), $( '<header />', {
                    'class': 'modal-footer'
                } ).append( $( '<button />', {
                    'class': 'modal-toggle', text: screenReaderText.ok
                } ) ) ) );

            } else {

                modalWrap = $( '<div />', {
                    // 'id': theID,
                    'class': 'site-overlay-wrap'
                } ).append( theModal );

            }

            // overlay切换. 
            if ( $( '#overlay' ).length > 0 ) {

                overlayRemove();

            } else {

                body.addClass( 'site-overlay-appear' ).append( $( '<div />', { 'id': 'overlay', 'class': 'site-overlay' } ).append( modalWrap ) );
                $( '#overlay' ).fadeIn();

                // 监听模块按钮点击事件.
                $( '#' + theID + ' .modal-toggle' ).click( function() {
                    if ( $( '#' + theID ).hasClass( 'toggled-on' ) ) {
                        $( '#' + theID ).removeClass( 'toggled-on' );
                    }
                    overlayRemove();
                    return false;
                });

            }

        } else {

            // overlay切换.
            if ( $( '#overlay' ).length > 0 ) {

                overlayRemove();

            } else {

                body.addClass( 'site-overlay-appear' ).append( '<div id="overlay" class="site-overlay"></div>' );
                $( '#overlay' ).fadeIn();

            }

        }
    }

    // 点赞功能.
    $.fn.postLikes = function() {
        if ( $( this ).hasClass( 'liked' ) ) {
            // alert( '之前已经赞过该文章啦~' );
            dynamicOverlay( true, 'default', 'liked-modal', 'default', screenReaderText.likedNotes );
            return false;
        } else {
            $( this ).addClass( 'liked' );
            var id = $( this ).data( 'id' ),
            action = $( this ).data( 'action' ),
            target = $( this ).children( '.count' ),
            count  = target.text(),
            number = Number( count ) + 1;
            var ajax_data = {
                action: 'post_likes',
                data_id: id,
                data_action: action
            };
            target.html( '<span class="loading"></span>' );
            $.post( screenReaderText.siteURL + '/wp-admin/admin-ajax.php', ajax_data, function() {
                target.html( number );
            });
            return false;
        }
    };
    $( '#post-likes' ).click( function() {
        $( this ).postLikes();
    });


    // 分享功能.
    $( '#post-share' ).click( function() {
        $( '.bshare-custom' ).fadeToggle( 'slow' );
    });

    // 复制功能.
    $( '#the-copyist' ).click( function( event ) {
        event.preventDefault();
        $( '#the-url' ).select(); // 选择对象
        document.execCommand( 'Copy' ); // 执行浏览器复制命令
        // alert( '链接已复制到剪贴板中。' );
        dynamicOverlay( true, 'default', 'copied-modal', 'default', screenReaderText.copiedNotes );
    });

    // 评论帮助功能.
    var commentHelp = $( '.comment-allowed-tags' );
    $( '.tool-help' ).click( function( event ) {
        event.preventDefault();
        dynamicOverlay( true, 'default', 'helps-modal', commentHelp );
    });

    // Global sidebar toggle.
    var sidebarToggle = $( '.sidebar-toggle' );
    var sidebar       = $( '#supply' );
    sidebarToggle.click( function( event ) {
        event.preventDefault();
        event.stopPropagation();
        sidebarToggle.add( sidebar ).toggleClass( 'toggled-on' );
        dynamicOverlay( false );
    });

    // User modal toggle.
    var modalToggle = $( '#user-modal-toggle, .widget_meta a[href*="wp-admin"], a[href*="wp-login.php?redirect"]' );
    var userModal   = $( '#user-modal' );
    modalToggle.click( function( event ) {
        event.preventDefault();
        event.stopPropagation();
        userModal.addClass( 'toggled-on' );
        dynamicOverlay( true, userModal, 'user-modal' );
    });

    // 回到顶部
    var toTop = $( '.skip-link.to-top' );
    toTop.click( function ( event ) {
        event.preventDefault();
        $( 'html, body' ).animate({
            scrollTop: 0
        }, 800 );
    });
    $( window ).on( 'scroll', function () {// 当滚动条的垂直位置大于浏览器所能看到的页面的那部分的高度时，回到顶部按钮就显示.
        if ( $( window ).scrollTop() > $( window ).height() ) {
            toTop.addClass( 'toggled-on' ).fadeIn();
        }
        else {
            toTop.removeClass( 'toggled-on' ).fadeOut();
        }
    });
    $( window ).trigger( 'scroll' ); // 触发滚动事件，避免刷新的时候显示回到顶部按钮.

    $( document ).ready( function() {
        $( window ).on( 'load.shawtheme', onResizeARIA ).on( 'resize.shawtheme', function() { onResizeARIA(); } );

        // 改变滚动条样式
        body.niceScroll( { cursorcolor: '#333', cursorborder: '1px solid #808080', zindex: '99998' } );
        $( '#sidebar-body' ).niceScroll( { cursorcolor: '#444', cursorborder: '1px solid #666', autohidemode: false, background: '#3f3f3f' } );

        // 如果是单页，则加载使用FancyBox灯箱
        if ( body.hasClass( 'singular' ) ) {
            // 文章缩略图支持FancyBox灯箱。
            // $( 'a.post-thumbnail[href$=".jpg"], a.post-thumbnail[href$=".jpeg"], a.post-thumbnail[href$=".png"], a.post-thumbnail[href$=".gif"]' ).fancybox( { caption: function( instance, item ) { return $(this).parent( '.entry-preview' ).prev( '.entry-header' ).children( '.entry-title' ).html(); } } );

            $( '.wp-block-image a[href$=".jpg"], .wp-block-image a[href$=".jpeg"], .wp-block-image a[href$=".png"], .wp-block-image a[href$=".gif"], a:not(.wp-block-file__button)[href$=".mp4"]' ).add( $( '.wp-caption a[href$=".jpg"], .wp-caption a[href$=".jpeg"], .wp-caption a[href$=".png"], .wp-caption a[href$=".gif"], .image-size a' ) ).fancybox( { caption: function( instance, item ) { return $(this).next( 'figcaption' ).html(); } } );

            // 评论内容支持FancyBox灯箱。
            $( '.comment-content a[href$=".jpg"], .comment-content a[href$=".jpeg"], .comment-content a[href$=".png"], .comment-content a[href$=".gif"]' ).fancybox();

            if ( body.find( '.wp-block-gallery' ) ) {
                $( '.wp-block-gallery a[href$=".jpg"], .wp-block-gallery a[href$=".jpeg"], .wp-block-gallery a[href$=".png"], .wp-block-gallery a[href$=".gif"]' ).attr( 'data-fancybox', 'gallery' ).fancybox( { loop: true, caption: function( instance, item ) { return $(this).next( 'figcaption' ).html(); } } );
            }

            if ( body.find( '.gallery' ) ) {
                $( '.gallery a[href$=".jpg"], .gallery a[href$=".jpeg"], .gallery a[href$=".png"], .gallery a[href$=".gif"]' ).attr( 'data-fancybox', 'album' ).fancybox( {
                    loop: true, caption: function( instance, item ) { return $(this).parent( '.gallery-icon' ).next( 'figcaption' ).html(); } } );
            }
        }

        // 点击空白处隐藏弹出层.
        $( document ).click( function( event ) {
            var _evt = event.srcElement ? event.srcElement : event.target;// IE支持 event.srcElement ，FF支持 event.target.

            if ( searchWrap.hasClass( 'toggled-on' ) && !searchWrap.is( _evt ) && searchWrap.has( _evt ).length === 0 ) {
                searchToggle.add( searchWrap ).removeClass( 'toggled-on' );
            } // 搜索框隐藏.
          

            if ( sidebar.hasClass( 'toggled-on' ) && !sidebar.is( _evt ) && sidebar.has( _evt ).length === 0 ) {
                sidebarToggle.add( sidebar ).removeClass( 'toggled-on' );
                overlayRemove();
            } // 侧边栏隐藏.

            if ( userModal.hasClass( 'toggled-on' ) && !userModal.is( _evt ) && userModal.has( _evt ).length === 0 ) {
                userModal.removeClass( 'toggled-on' );
                overlayRemove();
            } // 登录管理模块隐藏.

            return;
        });

    } );
} )( jQuery );