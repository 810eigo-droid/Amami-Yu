#!/usr/bin/env bash
# ブロックHTMLを結合してローカルプレビュー(preview/index.html)を生成します。
# WordPress を使わずに見た目を確認するためのものです。
set -euo pipefail
cd "$(dirname "$0")/.."
OUT=preview/index.html
{
cat <<'HEAD'
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>今の悩みの根っこがわかる「家族の役割 紐解きコーチング」｜あまみ悠</title>
<meta name="description" content="「今の悩み」と「家族の中で身につけた役割」のつながりを90分で整理する、あまみ悠のオンライン個別コーチング。役割から読み解くミニ講座の視聴者限定 5,500円（税込）、毎月5名様。">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Noto+Serif+JP:wght@500;600;700&display=swap">
<link rel="stylesheet" href="../wordpress/the-thor-child/assets/lp/lp.css">
<style>body{margin:0}</style>
</head>
<body>
<main id="amami-lp">
HEAD
for f in wordpress/blocks/*.html; do
  echo "<!-- ===== $(basename "$f") ===== -->"
  sed -e 's|{{LP_IMG}}|../wordpress/the-thor-child/assets/lp/img|g' -e 's|{{CTA_URL}}|https://1lejend.com/stepmail/kd.php?no=fqHSUws|g' "$f"
  echo
done
cat <<'TAIL'
</main>
<script src="../wordpress/the-thor-child/assets/lp/lp.js"></script>
</body>
</html>
TAIL
} > "$OUT"
echo "wrote $OUT"
