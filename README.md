# あまみ悠様「家族の役割 紐解きコーチング」LP

原稿: `LPコピー（ライフコーチジャパン）.pdf`　画像: `images/`

## 構成（THE THOR を有効にしたまま、LP専用テンプレートで作る）

```
wordpress/
├─ the-thor-child/            ← 子テーマ（the-thor-child）に丸ごとコピーするファイル
│   ├─ page-lp.php            LP用固定ページテンプレート（ヘッダー・フッターなし）
│   ├─ functions-lp.php       LP時だけ THE THOR のCSS/JSを外し、LP用CSS/JSを読み込む
│   └─ assets/lp/
│       ├─ lp.css             LPのスタイル（すべて #amami-lp 配下にスコープ）
│       ├─ lp.js              スクロール表示・スマホ追従CTA
│       └─ img/               LPで使う画像（webp）
└─ blocks/                    ← 固定ページに「カスタムHTML」ブロックで1つずつ貼るセクション
    ├─ 01-fv.html … 16-footer.html
    └─ 17-jsonld.html         構造化データ（Service / FAQPage）
preview/
├─ build.sh                   ブロックを結合して preview/index.html を作る
└─ index.html                 WordPress なしで見た目を確認できるプレビュー
```

## 設置手順

### 1. 子テーマにファイルを置く
1. THE THOR 公式の子テーマ（the-thor-child）を「外観 > テーマ」で有効化する（未導入なら先に入れる）。
2. `wordpress/the-thor-child/` の中身を、サーバーの `wp-content/themes/the-thor-child/` にそのままアップロードする
   （FTP、またはサーバーのファイルマネージャー）。
3. 子テーマの `functions.php` の末尾に次の1行を追加する。
   ```php
   require_once get_stylesheet_directory() . '/functions-lp.php';
   ```
4. 申込フォームのURLが決まったら、`functions-lp.php` の `AMAMI_LP_CTA_URL` を書き換える。
   全CTAボタンのリンク先が一括で変わる。

### 2. 固定ページを作る
1. 「固定ページ > 新規追加」。タイトルは
   `今の悩みの根っこがわかる「家族の役割 紐解きコーチング」｜あまみ悠`（検索結果に出るタイトル）。
2. 右側「ページ属性 > テンプレート」で **「LP（ヘッダー・フッターなし）」** を選ぶ。
3. `blocks/01-fv.html` から `17-jsonld.html` まで、順番に **「カスタムHTML」ブロック** を1つ追加して中身を貼る。
   1セクション=1ブロックなので、後から差し替えたいセクションだけ開いて書き換えられる。
4. `{{LP_IMG}}` と `{{CTA_URL}}` はそのままでよい（表示時に実URLへ置換される）。
5. スラッグ（URL）は短く意味のあるものにする（例: `family-role-coaching`）。

### 3. SEO 設定（公開前チェック）
- **抜粋**欄に説明文を入れる → `meta description` と OGP に使われる。
  例: 「今の悩み」と「家族の中で身につけた役割」のつながりを90分で整理する、あまみ悠のオンライン個別コーチング。役割から読み解くミニ講座の視聴者限定 5,500円（税込）、毎月5名様。
- **アイキャッチ画像**に `fv1-pc.webp` を設定 → OGP画像（SNSシェア時）になる。
- 公開後、ページの「ソースを表示」で `description` や `og:title` が **二重に出ていないか** 確認する。
  二重なら THE THOR 側が出力しているので、`functions-lp.php` の `amami_lp_theme_prints_seo()` を `return true;` に変える。
- `17-jsonld.html` 内の `https://example.com/...` を公開後の実URLに書き換える。
- `16-footer.html` の特商法・プライバシーポリシーのリンク先（`/tokushoho/` `/privacy-policy/`）を実際のURLに合わせる。
- 見出しは h1（FVに1つ）→ h2（各セクション）→ h3 の階層になっている。画像には alt を設定済み。
- FV画像は `fetchpriority="high"` と preload、それ以外は遅延読み込み。

## ローカルでのプレビュー
```bash
./preview/build.sh   # blocks/*.html を結合して preview/index.html を生成
```
生成された `preview/index.html` をブラウザで開く。

## 原稿との差異・要確認事項
- FV画像の文言は「診断を受けた方限定」、原稿本文は「ミニ講座 視聴者限定」。どちらに揃えるか要確認。
- 原稿の「（画像）」指定があるお客様の声は、写真素材がないためテキストカードで作成。写真が来たら `.lp-review` に追加可能。
- 申込フォームURL・特商法ページ・プライバシーポリシーのURLは未定のためプレースホルダー。
