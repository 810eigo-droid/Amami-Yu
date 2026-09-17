<?php
/**
 * Template Name: LP（ヘッダー・フッターなし）
 * Template Post Type: page
 *
 * 家族の役割 紐解きコーチング LP 用の固定ページテンプレート。
 * THE THOR のヘッダー・フッター・サイドバーを読み込まず、
 * 本文（ブロックエディタで組んだセクション）だけを出力します。
 * SEO用のタグは wp_head() 経由と、このファイル内のフォールバックで出力します。
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$amami_lp_title = get_the_title();
$amami_lp_desc  = has_excerpt() ? wp_strip_all_tags( get_the_excerpt() ) : '';
$amami_lp_url   = get_permalink();
$amami_lp_ogp   = has_post_thumbnail()
	? get_the_post_thumbnail_url( get_the_ID(), 'full' )
	: get_stylesheet_directory_uri() . '/assets/lp/img/fv1-pc.webp';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if ( $amami_lp_desc && ! amami_lp_theme_prints_seo() ) : ?>
<meta name="description" content="<?php echo esc_attr( $amami_lp_desc ); ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?php echo esc_attr( $amami_lp_title ); ?>">
<meta property="og:description" content="<?php echo esc_attr( $amami_lp_desc ); ?>">
<meta property="og:url" content="<?php echo esc_url( $amami_lp_url ); ?>">
<meta property="og:image" content="<?php echo esc_url( $amami_lp_ogp ); ?>">
<meta property="og:site_name" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
<meta name="twitter:card" content="summary_large_image">
<?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="image" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/lp/img/fv1-sp.webp' ); ?>" media="(max-width: 767px)">
<link rel="preload" as="image" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/lp/img/fv1-pc.webp' ); ?>" media="(min-width: 768px)">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'amami-lp-body' ); ?>>
<?php wp_body_open(); ?>
<main id="amami-lp">
<?php
while ( have_posts() ) :
	the_post();
	the_content();
endwhile;
?>
</main>
<?php wp_footer(); ?>
</body>
</html>
