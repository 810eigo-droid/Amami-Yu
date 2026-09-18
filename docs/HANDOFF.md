# 引き継ぎメモ（あまみ悠様 LP「家族の役割 紐解きコーチング」）

最終更新: 2026-09-17　ブランチ: `claude/vigilant-dijkstra-345dd4`（main 未マージ）

## 1. 何を作っているか
- クライアント: あまみ悠様（家族連鎖クリア＆ライフコーチ）。サイト https://amamiyuh.com/（WordPress + THE THOR 2.5.3）
- 成果物: 固定ページ1枚のLP。URL https://amamiyuh.com/family-role-coaching/（スラッグ `family-role-coaching`）
- 原稿: `LPコピー（ライフコーチジャパン）.pdf`（17ページ）。文言は原稿どおり、装飾（太字・マーカー）のみ追加。
- 申込フォーム: https://1lejend.com/stepmail/kd.php?no=fqHSUws　特商法: https://amamiyuh.com/law/　プライバシーポリシー: https://amamiyuh.com/privacy-policy/
- クライアント指示: 色はFV画像（ローズ・ベージュ）に合わせる／CTAボタンはオレンジ／お客様の声4件に写真／各セクションに画像／スマホ最優先。ストライプ帯・アニメーション帯は不採用。

## 2. 設計の要点
- THE THOR を有効にしたまま、**子テーマ the-thor-child** に LP 専用テンプレート `page-lp.php`（ヘッダー・フッター・サイドバーなし）を追加する方式。
- 本文は **1セクション=1つのHTML塊**（`wordpress/blocks/01〜17`）。サイトは **旧エディタ（クラシックエディタ）** なので、結合版 `wordpress/blocks/ALL.html` を「コード」タブに一括貼り付けする運用。
- CSS/JS は子テーマ `assets/lp/lp.css` `lp.js`。すべて `#amami-lp` 配下にスコープ。
- 画像は `{{LP_IMG}}/name.webp` の書き方で参照し、PHP が実URLへ置換。**メディアライブラリに同名（-1, -2 付きも可）があれば一番新しいものを使い、無ければ子テーマ内 `assets/lp/img` を使う**。子テーマ内画像には `?v=更新日時` が付く。
- CTA URL は `functions-lp.php` の定数 `AMAMI_LP_CTA_URL` で一括管理（`{{CTA_URL}}` を置換）。
- SEO: Yoast SEO が有効なので title/description/OGP は Yoast に任せる（テンプレートのフォールバックは SEO プラグイン検出時にオフ）。構造化データ（Service / FAQPage）は `17-jsonld.html`。h1 は FV に1つだけ。

## 3. THE THOR で判明した相性問題と対策（すべて実装済み）
| 問題 | 対策（ファイル） |
|---|---|
| 子テーマに `style-user.css` が無いと Warning | 空の `style-user.css` を同梱 |
| 子テーマ有効化でカスタマイザー設定（FV画像・色）が引き継がれない | `functions.php` の `after_switch_theme` で親の theme_mods と追加CSSを1回コピー（フラグ `the_thor_child_mods_copied`） |
| THE THOR が CSS/JS を wp_head/wp_footer に直接出力 | `amami_lp_head()/amami_lp_footer()` で出力をバッファし、親テーマ由来の `<link>/<script>` を除去 |
| 画像遅延読み込み（layzr）が `<img src>` を濃いグレーのダミーに差し替え、JSが無いので戻らない（真っ黒画面の原因） | `the_content` 最終段で `data-layzr` を `src` に戻す |
| 旧エディタの wpautop / ビジュアルタブが HTML を壊す | LP表示時は wpautop 等を外す。LPページ編集時はビジュアルタブを無効化（`user_can_richedit`） |

## 4. リポジトリ構成
```
wordpress/the-thor-child/   子テーマ一式（style.css, style-user.css, functions.php, functions-lp.php, page-lp.php, assets/lp/）
wordpress/blocks/           セクションHTML 01〜17 と結合版 ALL.html
dist/the-thor-child-lp.zip  子テーマzip（「外観 > テーマ > テーマのアップロード」→「置き換え」で更新）
dist/lp-images.zip          画像一式（メディアへ一括アップロード用）
images/                     クライアント支給・生成の元画像（png/jpg/webp）
docs/LP編集マニュアル.pdf   クライアント向け編集手順（HTML元: docs/lp-manual-source.html）
docs/image-prompts.md       セクション画像の生成プロンプト
preview/build.sh            ブロック結合 → preview/index.html と blocks/ALL.html を生成
preview/index.html          WordPress なしのプレビュー。GitHub Pages: https://810eigo-droid.github.io/Amami-Yu/
```

## 5. 定型作業
- **CSS/PHP/画像を変えたとき**: `dist/the-thor-child-lp.zip` を作り直す → WordPress でテーマzipを「置き換え」（有効化不要）。
- **ブロックHTMLを変えたとき**: `./preview/build.sh` で `ALL.html` を再生成 → WordPress のLPコード欄に Ctrl+A で貼り直し。
- **画像を足す/替える**: 元画像を `images/` へ → Pillow で webp 化（横1536px、200〜300KB以下）→ `assets/lp/img/` へ → zip 再生成。メディアに同名で上げれば自動で最新が使われる。
- zip 生成（Python）:
  ```python
  import zipfile, os
  root='wordpress/the-thor-child'
  with zipfile.ZipFile('dist/the-thor-child-lp.zip','w',zipfile.ZIP_DEFLATED) as z:
      for dp,dn,fn in os.walk(root):
          for f in fn:
              p=os.path.join(dp,f); z.write(p, os.path.join('the-thor-child', os.path.relpath(p, root)))
  ```
- マニュアルPDF再生成: `docs/lp-manual-source.html` を編集 → Playwright(Chromium) の `page.pdf` で A4 出力（Noto Sans JP を Google Fonts から読み込み）。

## 6. 画像の名前と場所
| 名前 | 用途 |
|---|---|
| fv1-pc.webp / fv1-sp.webp | FV（PC/スマホ、文字入り・支給） |
| fv2-pc.webp | 文字なし集合写真（クロージング前）。声の写真の切り出し元にも使った |
| woman1.webp / overview.webp | 開催概要（PC/スマホ・支給） |
| profile.webp | プロフィール（支給、1200px に縮小） |
| sec-03,04,05,06,07,08,11,12,14.webp | セクション画像（生成、1536×1024）。04・11 は正方形にトリミング表示 |
| voice-1〜4.webp | お客様の声（クライアント作成の正方形 480×480）。E様/Y様/M様/I様 |
| bg-orange-tile / bg-pink-tile / bg-beige-tile.webp（03,06,09,11 / 05,08,13 / 10,14） | 漆喰テクスチャ背景。上下鏡面で継ぎ足したタイル（1600×約1790）を幅100%・縦リピートで敷き、拡大による粗を防ぐ。白のかぶせ 40%（ベージュ 60%）。元画像 bg-*.webp はメディアと images/ に保存 |

## 7. デザインの決定事項
- 色: ローズ `#b8646c` / 深ローズ `#9a4f57` / クリーム `#fdf9f5` / 水色アクセント `#d9ecf3` 系 / CTA オレンジ `#f07a1f`。
- H2: ピンク→水色グラデーションの角丸枠、左上に薄いオレンジのぼかし円＋細い輪（枠内に収める）、右下にローズの輪。飾りは文字の後ろ。
- H3: 左ローズ縦線＋下に水色の短いライン。
- 強調: `.lp-mk`（蛍光マーカー、表示時に左から引かれる）/ `<strong>`（ローズ太字）/ `.lp-big` / `.lp-or`（オレンジ）。
- CTA: 本文内は02の1つだけ。追従CTA（`.lp-sticky`）は02のCTAが画面外に消えたら表示し、PC・スマホとも最後まで表示（フッターに下余白120px）。
- アニメーション: セクションのフェードイン、STEP・声カードの時間差表示、CTAの光の流れ。`prefers-reduced-motion` で停止。
- スマホ: 見出しは文節折り返し（`word-break: auto-phrase`）、`<br class="sp-br">`（スマホのみ改行）/`<br class="pc-br">`（PCのみ改行）で調整済み。ボタンは横幅いっぱい。開催概要の表は縦積み。

## 8. WordPress 側の現状（2026-09-17 時点）
- 子テーマ THE THOR CHILD 有効。トップページ等は親テーマ時と同じ表示を確認済み。
- LP ページ作成済み（下書き）。テンプレート「LP（ヘッダー・フッターなし）」選択、スラッグ設定済み。
- メディアに画像一式（19枚＋bg 3枚）アップロード済み。同名の古い画像が残っている（-1 付きが新しい）。
- UpdraftPlus でバックアップ取得済み（2026-09-17 10:30）。プラグイン: Yoast SEO, Wordfence, UpdraftPlus, wpForo, Duplicate Post ほか。

## 8b. クライアント初回フィードバック（2026-09-18）
`docs/client-feedback.md` 参照。CTAは「本文内1つ＋画面下の追従CTA」構成に変更済み。Broken Link Checker の27件は `{{LP_IMG}}` プレースホルダー起因（除外リスト設定で解消）。

## 9. 未完了・次にやること
1. 最新 zip の置き換えと `ALL.html` の貼り直しが済んでいるか確認（テクスチャ背景 10/14 追加が最後の変更）。
2. 実ページ（PC・スマホ）のスクリーンショットをもらい、テクスチャの濃さ・改行を最終調整。
3. クライアント確認用に **公開状態を「パスワード保護」で公開**し、確認依頼文 `docs/client-message.md` を送る。
4. クライアントの修正依頼を反映。
5. 本公開: 公開状態を「公開」へ。Yoast の SEOタイトル/ディスクリプション、アイキャッチ（fv1-pc）を設定。ソースで meta 二重出力が無いか確認。
6. 公開後、`docs/LP編集マニュアル.pdf` をクライアントへ渡す。
7. 任意: メディアの重複画像の整理、GitHub Pages の停止（Settings > Pages > Source: None）、ブランチを main へマージ。
