<?php
/**
 * 家族の役割 紐解きコーチング LP 用の関数。
 *
 * 使い方: 子テーマの functions.php の末尾に次の1行を追加してください。
 *   require_once get_stylesheet_directory() . '/functions-lp.php';
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** LPテンプレートを表示中かどうか */
function amami_lp_is_lp() {
	return is_page_template( 'page-lp.php' );
}

/**
 * テーマ側（THE THOR）が SEO メタを出力する設定になっているかの判定。
 * 現状は「必ず true」ではなく「false」を返し、テンプレート側のフォールバックを出力します。
 * 実サイトのHTMLソースで description / og:title が二重になっていたら、
 * ここを return true; に変えてください。
 */
function amami_lp_theme_prints_seo() {
	return false;
}

/**
 * LPテンプレートのときだけ:
 *  - 親テーマ（THE THOR）のCSS/JSを読み込み解除して干渉を防ぐ
 *  - LP専用のCSS/JSとGoogle Fontsを読み込む
 */
function amami_lp_enqueue_assets() {
	if ( ! amami_lp_is_lp() ) {
		return;
	}
	$parent_uri = get_template_directory_uri();

	$styles = wp_styles();
	foreach ( (array) $styles->queue as $handle ) {
		if ( isset( $styles->registered[ $handle ] ) && false !== strpos( (string) $styles->registered[ $handle ]->src, $parent_uri ) ) {
			wp_dequeue_style( $handle );
		}
	}
	$scripts = wp_scripts();
	foreach ( (array) $scripts->queue as $handle ) {
		if ( isset( $scripts->registered[ $handle ] ) && false !== strpos( (string) $scripts->registered[ $handle ]->src, $parent_uri ) ) {
			wp_dequeue_script( $handle );
		}
	}
	// 子テーマ本体の style.css（THE THOR の子テーマは親CSSを @import している場合がある）も外す
	wp_dequeue_style( 'the-thor-child-style' );
	wp_dequeue_style( 'child-style' );

	$dir = get_stylesheet_directory();
	$uri = get_stylesheet_directory_uri();

	wp_enqueue_style(
		'amami-lp-fonts',
		'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Noto+Serif+JP:wght@500;600;700&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'amami-lp', $uri . '/assets/lp/lp.css', array(), filemtime( $dir . '/assets/lp/lp.css' ) );
	wp_enqueue_script( 'amami-lp', $uri . '/assets/lp/lp.js', array(), filemtime( $dir . '/assets/lp/lp.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'amami_lp_enqueue_assets', 999 );

/** LPでは絵文字用スクリプトなど不要なものを外して軽くする */
function amami_lp_trim_head() {
	if ( ! amami_lp_is_lp() ) {
		return;
	}
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
}
add_action( 'wp', 'amami_lp_trim_head' );

/** 申込フォームのURL。ここを書き換えると全CTAボタンのリンク先が一括で変わります。 */
if ( ! defined( 'AMAMI_LP_CTA_URL' ) ) {
	define( 'AMAMI_LP_CTA_URL', 'https://1lejend.com/stepmail/kd.php?no=fqHSUws' );
}

/**
 * HTMLブロック内のトークンを実URLに置換する。
 *  {{LP_IMG}}  → 子テーマ内の画像フォルダURL
 *  {{CTA_URL}} → 申込フォームURL（AMAMI_LP_CTA_URL）
 */
function amami_lp_replace_placeholders( $content ) {
	if ( ! amami_lp_is_lp() ) {
		return $content;
	}
	return str_replace(
		array( '{{LP_IMG}}', '{{CTA_URL}}' ),
		array( get_stylesheet_directory_uri() . '/assets/lp/img', AMAMI_LP_CTA_URL ),
		$content
	);
}
add_filter( 'the_content', 'amami_lp_replace_placeholders', 5 );
