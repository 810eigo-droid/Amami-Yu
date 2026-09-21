# Claude Code 作業指示書
## あまみ悠様 LP｜子テーマ更新（CTA / 銀行払い / プロフィール写真）

対象リポジトリ：`810eigo-droid/Amami-Yu`  
作業ブランチ：`claude/vigilant-dijkstra-345dd4`

## 目的

WordPress（THE THOR + 子テーマ）で使用している「あまみ悠様 家族の役割 紐解きコーチングLP」の子テーマを、今回のクライアント修正内容に合わせて更新し、WordPressへそのままアップロードできる最新版ZIPを作成してください。

**重要：ユーザーはすでに WordPress 側へ最新版 `wordpress/blocks/ALL.html` をコピペ済みです。**  
したがって、今回の主作業は **子テーマ側の更新とZIP再生成** です。

---

## 今回の修正内容

### 1. プロフィール写真を修正版へ変更

クライアント要望：

- プロフィール写真右上の壁にある銀色の3つの点を削除
- 写真右下の影を削除

修正版画像はすでにリポジトリへアップロード済みです。

使用画像：

```
images/profile-bright.webp
```

LP側では古い `profile.webp` を参照せず、必ず次を使用してください。

```
profile-bright.webp
```

子テーマ内にも以下の場所へ入っていることを確認してください。

```
wordpress/the-thor-child/assets/lp/img/profile-bright.webp
```

プロフィールセクション：

```
wordpress/blocks/10-profile.html
```

では、次の参照になっていることを確認してください。

```html
{{LP_IMG}}/profile-bright.webp
```

---

### 2. CTAのリンク先をSquareへ変更

今後、オレンジ色のメインCTAはすべてSquare決済へ遷移させます。

新しいメインCTA URL：

```
https://square.link/u/2midxl9Q
```

`wordpress/the-thor-child/functions-lp.php` 内で、以下の定数を使用してください。

```php
if ( ! defined( 'AMAMI_LP_CTA_URL' ) ) {
    define( 'AMAMI_LP_CTA_URL', 'https://square.link/u/2midxl9Q' );
}
```

LP本文内の `{{CTA_URL}}` は、このSquare URLへ置換される状態にしてください。

対象：

- 本文内CTA
- 画面下の追従CTA
- ハンバーガーメニュー内CTA

---

### 3. 「銀行払いの方はこちら」を補助リンクとして追加

銀行払い希望者用に、メインCTAの下へ小さく目立たないテキストリンクを設置します。

表示文言：

```
銀行払いの方はこちら
```

リンク先：

```
https://1lejend.com/stepmail/kd.php?no=fqHSUws
```

`functions-lp.php` では以下の定数を使用してください。

```php
if ( ! defined( 'AMAMI_LP_BANK_URL' ) ) {
    define( 'AMAMI_LP_BANK_URL', 'https://1lejend.com/stepmail/kd.php?no=fqHSUws' );
}
```

さらに、HTML内の

```
{{BANK_URL}}
```

を `AMAMI_LP_BANK_URL` に置換できるようにしてください。

例：

```php
return str_replace(
    array( '{{CTA_URL}}', '{{BANK_URL}}' ),
    array( AMAMI_LP_CTA_URL, AMAMI_LP_BANK_URL ),
    $content
);
```

---

## 銀行払いリンクのデザイン

メインCTAより明確に弱い階層にしてください。

希望：

- 小さい文字
- 本文の薄い文字色
- 下線あり
- ボタン化しない
- オレンジ色にしない
- CTAから少しだけ間隔を空ける
- PC / スマホとも自然に見える
- 追従CTA内でも高さを取りすぎない

既存CSSクラス：

```
.lp-bank-link
.lp-bank-link--sticky
.lp-bank-link--menu
```

を使用して構いません。

例：

```css
#amami-lp .lp-bank-link {
  margin: 7px 0 0;
  font-size: .78rem;
  line-height: 1.5;
  text-align: center;
}

#amami-lp .lp-bank-link a,
#amami-lp a.lp-bank-link {
  color: var(--lp-ink-soft);
  font-weight: 400;
  text-decoration: underline;
  text-underline-offset: 2px;
}
```

必要であればスマホで詰まりすぎない程度に微調整してください。

---

## 4. 子テーマZIPを再生成

更新後、必ず以下を作り直してください。

```
dist/the-thor-child-lp.zip
```

ZIPのルートは必ず次の構造にしてください。

```
the-thor-child/
├── style.css
├── style-user.css
├── functions.php
├── functions-lp.php
├── page-lp.php
└── assets/
    └── lp/
        ├── lp.css
        ├── lp.js
        └── img/
            ├── profile-bright.webp
            └── ...
```

**注意：ZIP直下にファイルが裸で入らないようにしてください。**
WordPressの「テーマのアップロード → 置き換え」でそのまま使用できる形式にします。

---

## 5. 今回は変更しないもの

以下は原則変更しないでください。

- LP全体の文章
- FV
- セクション構成
- 配色
- CTA文言
- 既存画像（profile-bright.webp以外）
- THE THOR親テーマ
- `style.css` のテーマヘッダー
- 既存WordPress設定

また、ユーザーはすでに `ALL.html` をWordPressへ貼り付け済みのため、HTML側を不要に変更しないでください。

---

## 6. 既に反映済みの修正を維持

以下はすでに修正済みなので、戻さないでください。

### 文言修正

```
上記のことは「しません」。
```

↓

```
上記のことは「いたしません」。
```

### 強調解除

以下の文章は赤字・太字ではなく通常の黒文字です。

```
そして、90分ですべてを解決することもお約束しません。
```

---

## 7. 最終確認

作業完了前に以下を確認してください。

- [ ] `AMAMI_LP_CTA_URL` が Square URL
- [ ] `AMAMI_LP_BANK_URL` が 1lejend URL
- [ ] `{{CTA_URL}}` と `{{BANK_URL}}` が正しく置換される
- [ ] `profile-bright.webp` が子テーマ内に存在
- [ ] プロフィールセクションが `profile-bright.webp` を参照
- [ ] 銀行払いリンクがCTAより目立たない
- [ ] 追従CTAでも銀行払いリンクが崩れない
- [ ] PC / スマホでレイアウト崩れがない
- [ ] `dist/the-thor-child-lp.zip` を再生成
- [ ] ZIPを展開すると `the-thor-child/` から始まる
- [ ] 不要なOSファイル（.DS_Store等）がZIPに入っていない

---

## 8. 完了時に報告してほしい内容

Claude Code は作業完了時に、以下だけ簡潔に報告してください。

1. 変更したファイル一覧
2. CTA URL変更確認
3. 銀行払いリンク追加確認
4. profile-bright.webp反映確認
5. 再生成したZIPのパス
6. WordPress側でユーザーが行う操作

WordPress側でユーザーが行う操作は、基本的に次の1つです。

```
外観 → テーマ → 新規追加 → テーマのアップロード
→ dist/the-thor-child-lp.zip
→ 既存のTHE THOR CHILDを「置き換え」
```

既に有効化されている子テーマを再度有効化する必要はありません。
