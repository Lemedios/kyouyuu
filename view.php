<?php
// idパラメータがない場合は終了
if (!isset($_GET['id'])) {
    echo 'ファイルが指定されていません。';
    exit;
}

$id = $_GET['id'];

// アップロード先フォルダ
$uploadDir = __DIR__ . '/uploads';

// まず、uploadsディレクトリのファイル一覧から、該当IDを含むファイルを探す
$files = glob($uploadDir . '/' . $id . '.*');
if (count($files) === 0) {
    echo 'ファイルが見つかりません。';
    exit;
}

// 1つ見つかったとして、拡張子を取得
$filePath = $files[0];
$extension = pathinfo($filePath, PATHINFO_EXTENSION);

// MIMEタイプを簡単に判定（本番運用ではより厳密なバリデーション推奨）
$mimeType = mime_content_type($filePath);

// 拡張子に応じて表示方法を変更
// ここではよくある拡張子の例だけ示しています
// 必要に応じてケースを追加してください
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ファイル表示</title>
</head>
<body>
<?php

switch (strtolower($extension)) {
    case 'jpg':
    case 'jpeg':
    case 'png':
    case 'gif':
        // 画像として表示
        echo '<h1>画像プレビュー</h1>';
        echo '<img src="uploads/' . basename($filePath) . '" alt="image">';
        break;

    case 'pdf':
        // PDFプレビュー（iframe等で埋め込み）
        // ブラウザがPDF表示に対応していれば埋め込み表示される
        echo '<h1>PDFプレビュー</h1>';
        echo '<iframe src="uploads/' . basename($filePath) . '" width="600" height="800"></iframe>';
        break;

    case 'txt':
    case 'csv':
    case 'html':
        // テキスト系は中身を表示
        // ただし、HTMLをそのまま表示するとスクリプトが実行される場合があるので注意
        // 安全のために htmlspecialchars() でエスケープして表示
        echo '<h1>テキスト表示</h1>';
        $content = file_get_contents($filePath);
        echo '<pre>' . htmlspecialchars($content, ENT_QUOTES, 'UTF-8') . '</pre>';
        break;

    default:
        // その他はダウンロードリンクだけ表示
        echo '<h1>ファイルのダウンロード</h1>';
        echo '<p>このファイルは直接プレビューできない形式です。</p>';
        echo '<p><a href="uploads/' . basename($filePath) . '" download>ダウンロード</a></p>';
        break;
}
?>
</body>
</html>
