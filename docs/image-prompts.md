# セクション画像の生成プロンプト

FV画像（fv1-pc.webp）と世界観を揃えるための共通スタイルと、セクションごとの被写体です。
生成サイズは **横長 3:2（例 1536×1024）** で統一してください。LP側は枠に合わせて自動トリミングするので、
横長で作っておけば「横長の枠」にも「正方形の枠」にも同じ画像が使えます。

## 共通スタイル（毎回プロンプトの先頭に付ける）

```
Soft, airy lifestyle photograph with a gentle watercolor-like glow. Japanese woman in her 40s, natural gentle expression.
Bright clean room with soft daylight, pale pink and beige tones with a hint of pale blue, fresh flowers and greenery,
shallow depth of field, pastel color grading, calm and hopeful mood, high quality, 3:2 landscape. No text, no logos, no watermark.
```

日本語メモ: FV画像と同じ「淡いピンク・ベージュ＋少しの水色、明るい自然光、やわらかい光のにじみ」。人物は40代前後の日本人女性、表情は穏やか。文字は入れない。

## セクション別（共通スタイルの後ろに続ける）

| ファイル名 | セクション | 被写体 |
|---|---|---|
| sec-03.webp | ミニ講座を見て感じたこと | `A woman at home watching an online lecture on a laptop, chin resting on her hand, quietly reflecting, cup of tea beside her.` |
| sec-04.webp | 学んでも難しい理由（正方形にトリミングされます・被写体は中央に） | `A woman sitting at a desk with a few open books and handwritten notes on psychology, a slightly troubled but gentle expression, looking away in thought. Subject centered.` |
| sec-05.webp | 役割と今の悩みのつながり | `A woman gently looking at an old family photo album, soft nostalgic light, a child's drawing visible on the table.` |
| sec-06.webp | セッションの流れ | `A woman in an online video call on a laptop, listening and nodding with a warm smile, notebook open, cozy home office.` |
| sec-07.webp | 90分後に手に入るもの | `A woman writing a short list in a notebook with a relieved, calm smile, morning light, a clear and organized desk.` |
| sec-08.webp | 人を支援する力 | `Two women talking warmly in a bright counseling room, one listening attentively with a gentle expression, tissue box and flowers on the table.` |
| sec-11.webp | この時間ではしないこと（正方形にトリミング・被写体は中央に） | `A woman relaxing on a sofa holding a warm cup of tea with both hands, safe and unhurried atmosphere, soft blanket. Subject centered.` |
| sec-12.webp | 安心して参加いただくために | `Close-up of two women's hands gently holding a cup together across a table, warm and reassuring, soft pink flowers blurred in the background.` |
| sec-14.webp | よくあるご質問 | `A woman looking at her smartphone with a curious, slightly questioning expression, sitting by a window with sheer curtains.` |

## 差し替えの手順
1. 生成した画像を上のファイル名で保存する（webp でなくても可。png/jpg なら変換します）。
2. GitHub の `images/sections/` フォルダにアップロードする（Add file > Upload files）。
3. こちらで子テーマに取り込んで zip を更新する。ブロックのHTMLは変更不要。

自分で差し替えたい場合は、WordPress の「メディア」に画像をアップロードしてURLをコピーし、
該当セクションの `<img src="{{LP_IMG}}/sec-XX.webp"` の部分をそのURLに書き換えるだけでも動きます。
