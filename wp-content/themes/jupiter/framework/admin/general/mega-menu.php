<?php


function mk_menus_hook() {

	wp_enqueue_script( 'mk-menus-scripts', THEME_ADMIN_ASSETS_URI . '/js/mk-menus-scripts.js', array( 'jquery' ), false, true );
	wp_enqueue_style( 'mk-menus-styles', THEME_ADMIN_ASSETS_URI . '/stylesheet/css/mk-menus-styles.css' );
    wp_enqueue_media();
}


if ( mk_theme_is_menus() ) {
	add_action( 'admin_init', 'mk_menus_hook' );
}
