<?php
/**
 * THE THOR 子テーマ functions.php
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** 親テーマ（THE THOR）のスタイルを読み込む */
function the_thor_child_enqueue_styles() {
	wp_enqueue_style( 'the-thor-parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'the-thor-child-style', get_stylesheet_directory_uri() . '/style.css', array( 'the-thor-parent-style' ) );
}
add_action( 'wp_enqueue_scripts', 'the_thor_child_enqueue_styles' );

/** LP（家族の役割 紐解きコーチング）用の設定 */
require_once get_stylesheet_directory() . '/functions-lp.php';
