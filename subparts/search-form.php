<?php
/**
 * <= The template part for advanced search form =>
 * @package ShawTheme
 * @since ShawTheme 1.0.0
 */
?>

<form role="search" method="get" class="advanced-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <?php wp_dropdown_categories( array(
        'show_option_all'    => __( 'All categories', 'shawtheme' ),
        'depth'              => 1,
        'selected'           => $cat,
        'hierarchical'       => 1,
        'hide_if_empty'      => true,
    ) ); ?>
    <label>
        <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Enter the search terms here &hellip;', 'placeholder', 'shawtheme' ); ?>" value="<?php echo get_search_query(); ?>" name="s" autocomplete="off">
    </label>
    <button type="submit" class="search-submit"><span class="screen-reader-text"><?php echo _x( 'Search', 'submit button', 'default' ); ?></span></button>
</form>