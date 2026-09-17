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
**子テーマが未導入の場合（かんたん）**
1. `dist/the-thor-child-lp.zip` をダウンロードする。
2. 「外観 > テーマ > 新規追加 > テーマのアップロード」で zip を選んで「今すぐインストール」→「有効化」。
   （style.css / style-user.css / functions.php / LP用ファイル一式が入った子テーマ）
3. 有効化時に、親テーマ THE THOR のカスタマイザー設定（FV画像・色など）と追加CSSを子テーマへ自動コピーする
   （WordPress 本体はメニュー位置とウィジェットしか引き継がないため）。コピーは初回の1回だけ。
   うまく引き継がれない場合は「Customizer Export/Import」プラグインで親→子にエクスポート/インポートする。

**すでに子テーマ（the-thor-child）が有効な場合**
1. `wordpress/the-thor-child/` のうち `page-lp.php`、`functions-lp.php`、`assets/` を
   サーバーの `wp-content/themes/the-thor-child/` にアップロードする（FTP、サーバーのファイルマネージャー、または WP File Manager プラグイン）。
   既存の style.css / functions.php は上書きしない。
2. 子テーマの `functions.php` の末尾に次の1行を追加する。
   ```php
   require_once get_stylesheet_directory() . '/functions-lp.php';
   ```
3. `zip` を作り直すときは `dist/` を参照。
4. 申込フォームのURLは `functions-lp.php` の `AMAMI_LP_CTA_URL` に設定済み（https://1lejend.com/stepmail/kd.php?no=fqHSUws）。
   変更するときはここを書き換えると全CTAボタンのリンク先が一括で変わる。

### 2. 固定ページを作る
1. 「固定ページ > 新規追加」。タイトルは
   `今の悩みの根っこがわかる「家族の役割 紐解きコーチング」｜あまみ悠`（検索結果に出るタイトル）。
2. 右側「ページ属性 > テンプレート」で **「LP（ヘッダー・フッターなし）」** を選ぶ。
3. **ブロックエディタの場合**: `blocks/01-fv.html` から `17-jsonld.html` まで、順番に **「カスタムHTML」ブロック** を1つ追加して中身を貼る。
   1セクション=1ブロックなので、後から差し替えたいセクションだけ開いて書き換えられる。
   **旧エディタ（クラシックエディタ）の場合**: 「コード」タブに 01〜17 を順に続けて貼る。各セクションの先頭に `<!-- 01 … -->` のコメントがあるので、後から差し替えるときはその範囲を置き換える。
   LPテンプレートを選んだページでは「ビジュアル」タブは自動的に無効になり、自動整形（wpautop）も効かない。
4. `{{LP_IMG}}` と `{{CTA_URL}}` はそのままでよい（表示時に実URLへ置換される）。
5. スラッグ（URL）は短く意味のあるものにする（例: `family-role-coaching`）。

### 3. SEO 設定（公開前チェック）
- **抜粋**欄に説明文を入れる → `meta description` と OGP に使われる。
  例: 「今の悩み」と「家族の中で身につけた役割」のつながりを90分で整理する、あまみ悠のオンライン個別コーチング。役割から読み解くミニ講座の視聴者限定 5,500円（税込）、毎月5名様。
- **アイキャッチ画像**に `fv1-pc.webp` を設定 → OGP画像（SNSシェア時）になる。
- このサイトは Yoast SEO が有効なので、タイトル・ディスクリプション・OGP は固定ページ編集画面下部の **Yoast SEO 欄** で設定する（テンプレート側のフォールバックは自動でオフになる）。
- 公開後、ページの「ソースを表示」で `description` や `og:title` が **二重に出ていないか** 確認する。
- LPのスラッグは `family-role-coaching`（`17-jsonld.html` の `serviceUrl` と一致させる）。
- 特商法（https://amamiyuh.com/law/）・プライバシーポリシー（https://amamiyuh.com/privacy-policy/）は `16-footer.html` に設定済み。
- 見出しは h1（FVに1つ）→ h2（各セクション）→ h3 の階層になっている。画像には alt を設定済み。
- FV画像は `fetchpriority="high"` と preload、それ以外は遅延読み込み。

## 既知の THE THOR との相性と対策（実装済み）
- THE THOR の「画像遅延読み込み」は本文の `<img>` の src をダミー画像（濃いグレー300×300）に差し替え、`data-layzr` に本物を退避する。LPでは THE THOR のJSを読まないため、`functions-lp.php` で本文出力の最後に元へ戻している。
- THE THOR は CSS/JS を wp_head / wp_footer に直接出力するため、LPでは出力をバッファして親テーマ由来の `<link>` `<script>` を取り除いている。
- 子テーマには `style-user.css` が必須（無いと THE THOR が Warning を出す）。
- 子テーマ有効化時、カスタマイザー設定は WordPress 本体では引き継がれないので `functions.php` で親からコピーしている。

## ローカルでのプレビュー
```bash
./preview/build.sh   # blocks/*.html を結合して preview/index.html を生成
```
生成された `preview/index.html` をブラウザで開く。

## クライアント様指示の反映状況
- FV: 支給画像をそのまま使用（PC: `fv1-pc.webp` / スマホ: `fv1-sp.webp`）。スマホ用は縦長画像を支給いただいているので切れない。
- 色合い: FV画像のローズ・ベージュに合わせた。CTAボタン3か所（＋クロージング・スマホ追従）は指定どおりオレンジ。
- お客様の声4件のイメージ画像: 支給の文字なし画像 `fv2-pc.webp` から4人を切り出して `voice-1〜4.webp` を作成（「写真はイメージです」の注記あり）。
- クロージング直前に文字なし画像 `fv2-pc.webp` を配置。
- 文字色・配置・デザインはお任せとのことで、ローズ系見出し＋こげ茶本文に統一。
