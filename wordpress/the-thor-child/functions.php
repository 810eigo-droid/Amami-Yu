<?php
/**
 * THE THOR 子テーマ functions.php
 * 子テーマの style.css / style-user.css は THE THOR 本体が読み込むため、ここでは読み込みません。
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * 子テーマを有効化したとき、親テーマ（THE THOR）のカスタマイザー設定を子テーマにコピーする。
 *
 * WordPress 本体が引き継ぐのは「メニューの位置」と「ウィジェット」だけで、
 * THE THOR のデザイン設定（FV画像・色・レイアウトなど）は引き継がれないため。
 * 親テーマ側の設定は変更しない。コピーは一度だけ行い、以降は子テーマ側の設定を優先する。
 * もう一度コピーしたいときは、オプション the_thor_child_mods_copied を削除して再有効化する。
 */
function the_thor_child_copy_parent_customizer() {
	if ( get_option( 'the_thor_child_mods_copied' ) ) {
		return;
	}
	$child_slug  = get_stylesheet();  // the-thor-child
	$parent_slug = get_template();    // the-thor
	if ( $child_slug === $parent_slug ) {
		return;
	}

	$parent_mods = get_option( 'theme_mods_' . $parent_slug );
	if ( ! is_array( $parent_mods ) || empty( $parent_mods ) ) {
		return;
	}
	$child_mods = get_option( 'theme_mods_' . $child_slug );
	$child_mods = is_array( $child_mods ) ? $child_mods : array();

	$merged = $parent_mods;
	// WordPress 本体がすでに子テーマ用に用意した値は子テーマ側を優先
	foreach ( array( 'nav_menu_locations', 'sidebars_widgets' ) as $key ) {
		if ( isset( $child_mods[ $key ] ) ) {
			$merged[ $key ] = $child_mods[ $key ];
		} else {
			unset( $merged[ $key ] );
		}
	}
	// 追加CSSの参照IDは親テーマのものなので外し、下で子テーマ用に作り直す
	unset( $merged['custom_css_post_id'] );

	update_option( 'theme_mods_' . $child_slug, $merged );

	$parent_css = wp_get_custom_css( $parent_slug );
	if ( $parent_css ) {
		wp_update_custom_css_post( $parent_css, array( 'stylesheet' => $child_slug ) );
	}

	update_option( 'the_thor_child_mods_copied', current_time( 'mysql' ) );
}
add_action( 'after_switch_theme', 'the_thor_child_copy_parent_customizer' );

/** LP（家族の役割 紐解きコーチング）用の設定 */
require_once get_stylesheet_directory() . '/functions-lp.php';
