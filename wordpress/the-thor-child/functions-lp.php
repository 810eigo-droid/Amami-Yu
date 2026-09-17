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

/**
 * 旧エディタ（クラシックエディタ）対策。
 *  - LP表示時は wpautop / wptexturize を外し、貼り付けたHTMLに <p> や <br> が勝手に入らないようにする
 *  - LPページの編集画面では「ビジュアル」タブを無効化し、TinyMCE が <picture> などを組み替えるのを防ぐ
 */
function amami_lp_disable_autop() {
	if ( ! amami_lp_is_lp() ) {
		return;
	}
	remove_filter( 'the_content', 'wpautop' );
	remove_filter( 'the_content', 'wptexturize' );
	remove_filter( 'the_content', 'convert_smilies', 20 );
}
add_action( 'wp', 'amami_lp_disable_autop' );

function amami_lp_is_lp_edit_screen() {
	if ( ! is_admin() ) {
		return false;
	}
	$post_id = 0;
	if ( isset( $_GET['post'] ) ) {
		$post_id = (int) $_GET['post'];
	} elseif ( isset( $_POST['post_ID'] ) ) {
		$post_id = (int) $_POST['post_ID'];
	}
	return $post_id && 'page-lp.php' === get_page_template_slug( $post_id );
}
function amami_lp_disable_richedit( $can ) {
	return amami_lp_is_lp_edit_screen() ? false : $can;
}
add_filter( 'user_can_richedit', 'amami_lp_disable_richedit' );

/**
 * THE THOR の画像遅延読み込み（layzr）対策。
 * THE THOR は本文中の <img> の src をダミー画像に置き換え、本物のURLを data-layzr に退避し、
 * 自前のJSで戻す。LPではそのJSを読み込まないため、ここで本文出力の最後に元へ戻す。
 * （LPの画像はブラウザ標準の loading="lazy" で遅延読み込みしている）
 */
function amami_lp_undo_theme_lazyload( $content ) {
	if ( ! amami_lp_is_lp() ) {
		return $content;
	}
	// src="...dummy.gif" ... data-layzr="実URL"  →  src="実URL"
	$content = preg_replace_callback(
		'#<img\b[^>]*\bdata-layzr=["\']([^"\']+)["\'][^>]*>#i',
		function ( $m ) {
			$tag = $m[0];
			$real = $m[1];
			$tag = preg_replace( '#\bsrc=["\'][^"\']*["\']#i', 'src="' . esc_url( $real ) . '"', $tag, 1 );
			$tag = preg_replace( '#\s+data-layzr(?:-[a-z]+)?=["\'][^"\']*["\']#i', '', $tag );
			return $tag;
		},
		$content
	);
	// srcset 版（data-layzr-srcset）にも対応
	$content = preg_replace( '#\bdata-layzr-srcset=#i', 'srcset=', $content );
	return $content;
}
add_filter( 'the_content', 'amami_lp_undo_theme_lazyload', PHP_INT_MAX );

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
 * LP画像のURLを返す。
 * メディアライブラリに同じ名前（例 voice-1.webp）または「-1」「-2」付き（voice-1-2.webp）の画像があれば、
 * その中で一番新しいものを使う。無ければ子テーマ内 assets/lp/img の画像を使う。
 * 結果は12時間キャッシュし、メディアの追加・削除でキャッシュを捨てる。
 */
function amami_lp_img_url( $name ) {
	$map = get_transient( 'amami_lp_img_map' );
	if ( ! is_array( $map ) ) {
		$map = array();
	}
	if ( isset( $map[ $name ] ) ) {
		return $map[ $name ];
	}
	$url = '';
	$dot = strrpos( $name, '.' );
	if ( false !== $dot ) {
		$stem = substr( $name, 0, $dot );
		$ext  = substr( $name, $dot + 1 );
		$found = get_posts( array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'fields'         => 'ids',
			'meta_query'     => array( array(
				'key'     => '_wp_attached_file',
				'value'   => '(^|/)' . preg_quote( $stem ) . '(-[0-9]+)?\\.' . preg_quote( $ext ) . '$',
				'compare' => 'REGEXP',
			) ),
		) );
		if ( $found ) {
			$url = (string) wp_get_attachment_url( $found[0] );
		}
	}
	if ( '' === $url ) {
		$file = get_stylesheet_directory() . '/assets/lp/img/' . $name;
		$url  = get_stylesheet_directory_uri() . '/assets/lp/img/' . $name;
		if ( file_exists( $file ) ) {
			$url .= '?v=' . filemtime( $file );
		}
	}
	$map[ $name ] = $url;
	set_transient( 'amami_lp_img_map', $map, 12 * HOUR_IN_SECONDS );
	return $url;
}
/** メディアが追加・削除されたらキャッシュを捨てる */
function amami_lp_clear_img_cache() {
	delete_transient( 'amami_lp_img_map' );
	delete_transient( 'amami_lp_img_base' );
}
add_action( 'add_attachment', 'amami_lp_clear_img_cache' );
add_action( 'delete_attachment', 'amami_lp_clear_img_cache' );

/**
 * HTMLブロック内のトークンを実URLに置換する。
 *  {{LP_IMG}}/name.webp → 画像URL（メディアの最新版を優先、なければ子テーマ内）
 *  {{CTA_URL}}          → 申込フォームURL（AMAMI_LP_CTA_URL）
 */
function amami_lp_replace_placeholders( $content ) {
	if ( ! amami_lp_is_lp() ) {
		return $content;
	}
	$content = preg_replace_callback(
		'#\{\{LP_IMG\}\}/([A-Za-z0-9_.-]+)#',
		function ( $m ) { return amami_lp_img_url( $m[1] ); },
		$content
	);
	return str_replace( '{{CTA_URL}}', AMAMI_LP_CTA_URL, $content );
}
add_filter( 'the_content', 'amami_lp_replace_placeholders', 5 );
