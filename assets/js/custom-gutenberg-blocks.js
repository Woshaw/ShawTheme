/*
<= Shawtheme gutenberg blocks javascript file =>
Description: Cutom bolcks for gutenberg block editor.
Version: 1.0.0
*/

// WordPress dependencies.
const { __ } = wp.i18n;
const { createElement, RawHTML } = wp.element;

wp.blocks.registerBlockType( 'shawtheme/embed', {
    title: __( 'Shawtheme Embed', 'shawtheme' ),
    description: __( 'Embed media from external sources with responsive styles.', 'shawtheme' ),
    category: 'embed', // Available options: common | formatting | layout | widgets | embed
    icon: 'format-video',
    keywords: [ __( 'embed' ), __( 'media' ), __( 'iframe' ) ],
    attributes: {
        content: {
            type: 'string'
        }
    },
    edit: function( props ) {
        return createElement( 'textarea', {
            style: { width: '100%' },
            placeholder: __( 'Enter iframe code here ...' ),
            value: props.attributes.content,
            onChange: event => props.setAttributes( { content: event.target.value } )
        } );
    },
    save: function( props ) {
        return createElement( 'div', {
            className: 'media-16by9'
        }, createElement( RawHTML, null, props.attributes.content ) );
    }
} );