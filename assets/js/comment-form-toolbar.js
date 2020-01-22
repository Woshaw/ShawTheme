/* global screenReaderText */
/*
<= Comment form toolbar =>
Description: Toolbar script for comment textarea.
Version: 1.0.0
*/

(function(b, a, c) {
    b(function() {
        (function() {
            // 定义Xtag函数。
            function Xtag(q, m, p) {
                if (document.selection) {
                    q.focus();
                    sel = document.selection.createRange();
                    p ? sel.text = m + sel.text + p : sel.text = m;
                    q.focus();
                } else {
                    if (q.selectionStart || q.selectionStart == '0') {
                        var o = q.selectionStart;
                        var n = q.selectionEnd;
                        var r = n;
                        p ? q.value = q.value.substring(0, o) + m + q.value.substring(o, n) + p + q.value.substring(n, q.value.length)  : q.value = q.value.substring(0, o) + m + q.value.substring(n, q.value.length);
                        p ? r += m.length + p.length : r += m.length - n + o;
                        if (o == n && p) {
                            r -= p.length;
                        }
                        q.focus();
                        q.selectionStart = r;
                        q.selectionEnd = r;
                    } else {
                        q.value += m + p;
                        q.focus();
                    }
                }
            }

            // 当前时间。
            // _datetime = (function() {
            //     var now = new Date(), zeroise;

            //     zeroise = function(number) {
            //         var str = number.toString();

            //         if ( str.length < 2 ) {
            //             str = '0' + str;
            //         }

            //         return str;
            //     };

            //     return now.getUTCFullYear() + '-' +
            //       zeroise( now.getUTCMonth() + 1 ) + '-' +
            //       zeroise( now.getUTCDate() ) + 'T' +
            //       zeroise( now.getUTCHours() ) + ':' +
            //       zeroise( now.getUTCMinutes() ) + ':' +
            //       zeroise( now.getUTCSeconds() ) +
            //       '+00:00';
            // })();

            // 定义Xtag参数。
            var textarea = document.getElementById('comment') || 0;
            var tags = {
                bold: function() {
                    Xtag( textarea, '<b>', '</b>' );
                },
                italic: function() {
                    Xtag( textarea, '<i>', '</i>' );
                },
                strike: function() {
                    Xtag( textarea, '<s>', '</s>' );
                },
                quote: function() {
                    Xtag( textarea, '<q>', '</q>' );
                },
                code: function() {
                    Xtag( textarea, '<code>', '</code>' );
                },
                link: function() {
                    var url = prompt( localizeText.enterURL, 'https://' );
                    if ( url ) {
                        Xtag( textarea, '<a target="_blank" href="' + url + '">', '</a>' );
                    }
                },
                image: function() {
                    var src = prompt( localizeText.enterImageURL, 'https://' ), alt;
                    if ( src ) {
                        alt = prompt( localizeText.enterImageDescription, '');
                        Xtag( textarea, '<img src="' + src + '" alt="' + alt + '">', '' );
                    }
                }
            };

            a.HTML = {};
            a.HTML['QuickTag'] = tags;

        }) ();
    });
}) (jQuery, window);


( function( $ ) {

    // 创建comment-form-toolbar区块。
    var tool = [
        { name: 'bold', action: 'javascript:HTML.QuickTag.bold();', text: localizeText.bold, title: localizeText.bold },
        { name: 'italic', action: 'javascript:HTML.QuickTag.italic();', text: localizeText.italic, title: localizeText.italic },
        { name: 'strike', action: 'javascript:HTML.QuickTag.strike();', text: localizeText.strike, title: localizeText.strikethrough },
        { name: 'quote', action: 'javascript:HTML.QuickTag.quote();', text: localizeText.quote, title: localizeText.quote },
        { name: 'code', action: 'javascript:HTML.QuickTag.code();', text: localizeText.code, title: localizeText.code },
        { name: 'link', action: 'javascript:HTML.QuickTag.link();', text: localizeText.link, title: localizeText.insertLink },
        { name: 'image', action: 'javascript:HTML.QuickTag.image();', text: localizeText.image, title: localizeText.insertImage },
        { name: 'emoji', action: 'javascript:;', text: localizeText.emoji, title: localizeText.insertEmoji },
        { name: 'help', action: 'javascript:;', text: localizeText.help, title: localizeText.help }
    ];
    var tools = '';
    for ( var i = 0; i < tool.length; i++ ) {
        tools += '<li class="comment-tool-' + tool[i].name + '"><a class="tool-' + tool[i].name + '" href="' + tool[i].action +'" title="' + tool[i].title + '">'+ tool[i].text + '</a></li>';
    }
    var toolbar = $( '<ul />', { 'class': 'comment-form-toolbar' } ).append( tools );
    $( '#comment' ).before( toolbar );


    // 表情区====================================================================================
    // 点击表情输入字符到texearea.
    emoji = function( sign ) {
        var myField;
        sign = ' ' + sign + ' ';
        if ( $( '#comment' ) && $( '#comment' ).is( 'textarea' ) ) {
            // myField = $( '#comment' ); 会使后面代码出错的原因：被$包装成为一个jQuery对象，而非原来的DOM对象，不能直接调用DOM方法
            myField = document.getElementById( 'comment' );
        } else {
            return false;
        }

        if ( document.selection ) {
            myField.focus();
            sel = document.selection.createRange();
            sel.text = sign;
            myField.focus();
            emojiHide();
        }
        else if ( myField.selectionStart || myField.selectionStart == '0' ) {
            var startPos = myField.selectionStart;
            var endPos = myField.selectionEnd;
            var cursorPos = endPos;
            myField.value = myField.value.substring( 0, startPos ) + sign + myField.value.substring( endPos, myField.value.length );
            cursorPos += sign.length;
            myField.focus();
            myField.selectionStart = cursorPos;
            myField.selectionEnd = cursorPos;
            emojiHide();
        }
        else {
            myField.value += sign;
            myField.focus();
        }
    }

    // 获取当前脚本的路径
    // var js = document.scripts;
    //     js = js[js.length-1].src.substring(0,js[js.length-1].src.lastIndexOf("/")+1);

    // 创建comment-smileys区块。
    var smiley = [
        { attr: localizeText.smile, action: "javascript:emoji(':smile:');", src: localizeText.themeURL + '/assets/img/smilies/weixiao.gif' },
        { attr: localizeText.sad, action: "javascript:emoji(':sad:');", src: localizeText.themeURL + '/assets/img/smilies/piezui.gif' },
        { attr: localizeText.razz, action: "javascript:emoji(':razz:');", src: localizeText.themeURL + '/assets/img/smilies/tiaopi.gif' },
        { attr: localizeText.roll, action: "javascript:emoji(':roll:');", src: localizeText.themeURL + '/assets/img/smilies/baiyan.gif' },
        { attr: localizeText.idea, action: "javascript:emoji(':idea:');", src: localizeText.themeURL + '/assets/img/smilies/jingxi.gif' },
        { attr: localizeText.grin, action: "javascript:emoji(':grin:');", src: localizeText.themeURL + '/assets/img/smilies/ciya.gif' },
        { attr: localizeText.cool, action: "javascript:emoji(':cool:');", src: localizeText.themeURL + '/assets/img/smilies/ku.gif' },
        { attr: localizeText.oops, action: "javascript:emoji(':oops:');", src: localizeText.themeURL + '/assets/img/smilies/qiudale.gif' },
        { attr: localizeText.cry, action: "javascript:emoji(':cry:');", src: localizeText.themeURL + '/assets/img/smilies/liulei.gif' },
        { attr: localizeText.shock, action: "javascript:emoji(':shock:');", src: localizeText.themeURL + '/assets/img/smilies/penxue.gif' },
        { attr: localizeText.neutral, action: "javascript:emoji(':neutral:');", src: localizeText.themeURL + '/assets/img/smilies/fadai.gif' },
        { attr: localizeText.twisted, action: "javascript:emoji(':twisted:');", src: localizeText.themeURL + '/assets/img/smilies/qiaoda.gif' },
        { attr: localizeText.lust, action: "javascript:emoji(':?:');", src: localizeText.themeURL + '/assets/img/smilies/se.gif' },
        { attr: localizeText.wink, action: "javascript:emoji(':wink:');", src: localizeText.themeURL + '/assets/img/smilies/haixiu.gif' },
        { attr: localizeText.eek, action: "javascript:emoji(':eek:');", src: localizeText.themeURL + '/assets/img/smilies/bizui.gif' },
        { attr: localizeText.lol, action: "javascript:emoji(':lol:');", src: localizeText.themeURL + '/assets/img/smilies/touxiao.gif' },
        { attr: localizeText.arrow, action: "javascript:emoji(':arrow:');", src: localizeText.themeURL + '/assets/img/smilies/wunai.gif' },
        { attr: localizeText.crazy, action: "javascript:emoji(':!:');", src: localizeText.themeURL + '/assets/img/smilies/zhuakuang.gif' },
        { attr: localizeText.mad, action: "javascript:emoji(':mad:');", src: localizeText.themeURL + '/assets/img/smilies/zhouma.gif' },
        { attr: localizeText.mrgreen, action: "javascript:emoji(':mrgreen:');", src: localizeText.themeURL + '/assets/img/smilies/yun.gif' },
        { attr: localizeText.question, action: "javascript:emoji(':???:');", src: localizeText.themeURL + '/assets/img/smilies/yiwen.gif' },
        { attr: localizeText.evil, action: "javascript:emoji(':evil:');", src: localizeText.themeURL + '/assets/img/smilies/yinxian.gif' },
        { attr: localizeText.sleep, action: "javascript:emoji(':|');", src: localizeText.themeURL + '/assets/img/smilies/shui.gif' },
        { attr: localizeText.hush, action: "javascript:emoji(':x');", src: localizeText.themeURL + '/assets/img/smilies/xu.gif' }
    ];
    var smileys = '';
    for ( var i = 0; i < smiley.length; i++ ) {
        smileys += '<a href="' + smiley[i].action +'" title="' + smiley[i].attr + '"><img src="' + smiley[i].src + '" alt="' + smiley[i].attr + '"></a>';
    }
    var emojiBox = $( '<div />', { 'class': 'comment-smileys' } ).append( smileys );
    $( '.comment-tool-emoji' ).append( emojiBox );

    // 表情区块 显示/隐藏 切换。
    $( '.tool-emoji' ).click( function ( event ) {
        // 取消事件冒泡.
        event.stopPropagation();
        $( '.comment-smileys' ).toggleClass( 'toggled-on' )
    });

    function emojiHide() {
        $( '.comment-smileys' ).removeClass( 'toggled-on' );
    }

    $( document ).click( function( event ) {
        var _evt = event.srcElement ? event.srcElement : event.target;// IE支持 event.srcElement ，FF支持 event.target.
        if ( $( '.comment-smileys' ).hasClass( 'toggled-on' ) && !$( '.comment-smileys' ).is( _evt ) && $( '.comment-smileys' ).has( _evt ).length === 0 ) {
            emojiHide();
        }
    });

} )( jQuery );