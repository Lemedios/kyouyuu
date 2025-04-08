<?php
// アップロードファイルが送信されていない場合はindex.htmlへ戻す
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    header('Location: index.html');
    exit;
}

// 保存先フォルダ
$uploadDir = __DIR__ . '/uploads';

// 一時ファイルパス
$tmpFile = $_FILES['file']['tmp_name'];
// 元のファイル名
$originalName = $_FILES['file']['name'];

// 拡張子を取得
$extension = pathinfo($originalName, PATHINFO_EXTENSION);

// 衝突を避けるためにID（短い文字列）を生成
// 実運用では、もっと高度なランダム生成を推奨
$id = substr(md5(uniqid(mt_rand(), true)), 0, 8); 

// 保存先のファイルパス。ID＋拡張子で保存
$newFileName = $id . '.' . $extension;
$destination = $uploadDir . '/' . $newFileName;

// アップロードされたファイルを保存先へ移動
if (move_uploaded_file($tmpFile, $destination)) {
    // ファイルの保存が成功したら、共有用のURLを生成
    // view.php?id=xxxx の形式
    $sharedUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://')
               . $_SERVER['HTTP_HOST']
               . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\')
               . '/view.php?id=' . $id;

    // 結果を表示
    echo '<!DOCTYPE html>';
    echo '<html lang="ja"><head><meta charset="UTF-8"><title>アップロード完了</title></head><body>';
    echo '<h1>アップロード完了</h1>';
    echo '<p>以下のURLを共有すると、ファイルを表示・ダウンロードできます:</p>';
    echo '<p><a href="' . htmlspecialchars($sharedUrl, ENT_QUOTES, 'UTF-8') . '">' 
         . htmlspecialchars($sharedUrl, ENT_QUOTES, 'UTF-8') . '</a></p>';
    echo '</body></html>';
} else {
    // 失敗した場合
    echo '<!DOCTYPE html>';
    echo '<html lang="ja"><head><meta charset="UTF-8"><title>エラー</title></head><body>';
    echo '<h1>ファイルのアップロードに失敗しました。</h1>';
    echo '</body></html>';
}
