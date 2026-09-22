<?php
/**
 * トップページのメインビジュアル（THE THOR「静止画」モード）の補助。
 *
 * 1. 画質: THE THOR は 1280×720 に縮小した画像を出力し、PCではそれを高さ793pxに
 *    引き伸ばすためぼやける。トップページでは縮小版ではなく元の画像を使う。
 * 2. スマホ用画像: カスタマイザーに「スマホ用メインビジュアル画像」を追加し、
 *    スマホ表示のときだけその画像に差し替える。空欄ならPC用画像をそのまま使う。
 *
 * どちらも THE THOR 本体には手を入れない。この子テーマを外せば元の動きに戻る。
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * カスタマイザーに「スマホ用メインビジュアル画像」を追加。
 * THE THOR の「TOPページ[THE] > メインビジュアル設定」（fit_home_mainimg_section）の中、
 * 「静止画時の設定」の直後に置く。THE THOR のセクションが無い環境では独自セクションに置く。
 */
function amami_home_customize_register( $wp_customize ) {
	$section  = 'fit_home_mainimg_section';
	$priority = 10;
	if ( ! $wp_customize->get_section( $section ) ) {
		$section = 'amami_home_mainimg';
		$wp_customize->add_section( $section, array(
			'title'       => 'メインビジュアル（スマホ用画像）',
			'priority'    => 1,
			'description' => 'トップページのメインビジュアルを、スマホで見たときだけ別の画像にします。',
		) );
	} else {
		$still = $wp_customize->get_control( 'fit_homeMainimg_stillImg' );
		if ( $still ) {
			$priority = (int) $still->priority + 1;
		}
	}
	$wp_customize->add_setting( 'amami_home_mainimg_sp', array(
		'type'              => 'theme_mod',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'amami_home_mainimg_sp', array(
		'label'       => '静止画時の設定（スマホ用画像）',
		'section'     => $section,
		'priority'    => $priority,
		'description' => '■スマホで見たときだけ使う画像を登録（空欄ならPC用画像をそのまま使用）。縦長（例 1080×1350）のJPGがおすすめ。高さは下の「高さ(スマホ)」で調整。',
	) ) );
}
// THE THOR がセクションを登録し終えたあとに動かす（優先度 999）
add_action( 'customize_register', 'amami_home_customize_register', 999 );

/** メインビジュアルの画像ID（PC用・スマホ用）を取得。1リクエスト内でキャッシュ */
function amami_home_mainimg_ids() {
	static $ids = null;
	if ( null !== $ids ) {
		return $ids;
	}
	$ids = array( 'pc' => 0, 'sp' => 0, 'sp_url' => '' );
	$pc = get_theme_mod( 'fit_homeMainimg_stillImg' );
	if ( is_numeric( $pc ) ) {
		$ids['pc'] = (int) $pc;
	} elseif ( is_string( $pc ) && '' !== $pc ) {
		$ids['pc'] = (int) attachment_url_to_postid( $pc );
	}
	$sp = get_theme_mod( 'amami_home_mainimg_sp' );
	if ( is_string( $sp ) && '' !== $sp ) {
		$ids['sp']     = (int) attachment_url_to_postid( $sp );
		$ids['sp_url'] = $sp;
	}
	return $ids;
}

/**
 * トップページのメインビジュアル画像だけ、縮小版（1280×720）ではなく元画像を返す。
 * スマホ（wp_is_mobile）でスマホ用画像が設定されていればそちらの元画像を返す。
 * サムネイル用途（幅1000px未満の要求）は触らない。
 */
function amami_home_mainimg_src( $image, $attachment_id, $size, $icon ) {
	static $busy = false;
	if ( $busy || is_admin() || ! $image || ! ( is_front_page() || is_home() ) ) {
		return $image;
	}
	$ids = amami_home_mainimg_ids();
	if ( ! $ids['pc'] || (int) $attachment_id !== $ids['pc'] || (int) $image[1] < 1000 ) {
		return $image;
	}
	$busy = true;
	$out  = $image;
	if ( wp_is_mobile() && $ids['sp'] ) {
		$sp = wp_get_attachment_image_src( $ids['sp'], 'full' );
		if ( $sp ) {
			$out = $sp;
		}
	} else {
		$full = wp_get_attachment_image_src( $attachment_id, 'full' );
		if ( $full ) {
			$out = $full;
		}
	}
	$busy = false;
	return $out;
}
add_filter( 'wp_get_attachment_image_src', 'amami_home_mainimg_src', 10, 4 );

/**
 * 保険: ページキャッシュでPC用HTMLがスマホに配られた場合でも、
 * CSSでスマホ幅のときにスマホ用画像へ差し替える（Chrome / Safari で有効）。
 */
function amami_home_mainimg_sp_css() {
	if ( ! ( is_front_page() || is_home() ) ) {
		return;
	}
	$ids = amami_home_mainimg_ids();
	if ( ! $ids['sp_url'] ) {
		return;
	}
	echo '<style id="amami-home-mainimg-sp">@media (max-width:767px){.still .still__img{content:url("' . esc_url( $ids['sp_url'] ) . '");object-fit:cover;}}</style>' . "\n";
}
add_action( 'wp_head', 'amami_home_mainimg_sp_css', 99 );
