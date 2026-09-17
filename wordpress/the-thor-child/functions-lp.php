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
 * SEOプラグイン（Yoast SEO / SEO SIMPLE PACK / All in One SEO / Rank Math）が有効なら、
 * title・description・OGP はそちらに任せ、テンプレート側のフォールバックは出力しない。
 * プラグインを使わない構成でタグが二重になる場合は return true; に固定してください。
 */
function amami_lp_theme_prints_seo() {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'SSP_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' );
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

/**
 * THE THOR は自身のCSS/JSを wp_head / wp_footer の中で直接出力するため、
 * wp_enqueue の解除だけでは残る。LPでは出力をいったん受け取り、
 * 親テーマ（/themes/the-thor/）由来の <link> <script> と、
 * THE THOR が出力する <style> を取り除いてから出力する。
 * Yoast などプラグインのタグ、WP本体の title / canonical はそのまま残る。
 */
function amami_lp_strip_parent_assets( $html ) {
	$parent = preg_quote( get_template_directory_uri() . '/', '#' );
	// 相対パス（/wp-content/themes/the-thor/）で出力される場合にも対応
	$parent_rel = preg_quote( wp_make_link_relative( get_template_directory_uri() ) . '/', '#' );
	$patterns = array(
		'#<link\b[^>]*(?:' . $parent . '|' . $parent_rel . ')[^>]*>\s*#i',
		'#<script\b[^>]*src=["\'][^"\']*(?:' . $parent . '|' . $parent_rel . ')[^"\']*["\'][^>]*>\s*</script>\s*#i',
		'#<style\b[^>]*id=["\']the-?thor[^"\']*["\'][^>]*>.*?</style>\s*#is',
	);
	return preg_replace( $patterns, '', $html );
}

/** LPテンプレート内で wp_head() の代わりに呼ぶ */
function amami_lp_head() {
	ob_start();
	wp_head();
	echo amami_lp_strip_parent_assets( ob_get_clean() );
}

/** LPテンプレート内で wp_footer() の代わりに呼ぶ */
function amami_lp_footer() {
	ob_start();
	wp_footer();
	echo amami_lp_strip_parent_assets( ob_get_clean() );
}

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
