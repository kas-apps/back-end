# 総合プロジェクト2: CSVマネージャー 📊

Phase 2で学んだファイル操作とエラーハンドリングを活用した、CSVファイル管理システムを構築します！

---

## 🎯 プロジェクト概要

### 機能

- ✅ CSVファイルのアップロード（セキュアな検証付き）
- ✅ CSVデータの表示（HTMLテーブル）
- ✅ CSVデータの編集
- ✅ CSVファイルのダウンロード
- ✅ エラーハンドリング

### セキュリティ対策

- 🔒 **ファイルタイプチェック**：CSVファイルのみ許可
- 🔒 **ファイルサイズ制限**：最大2MB
- 🔒 **パストラバーサル対策**：`basename()` でファイル名をサニタイズ
- 🔒 **XSS対策**：`htmlspecialchars()` でデータをエスケープ
- 🔒 **エラーハンドリング**：try-catchで適切に処理

---

## 📁 ファイル構成

```text
csv-manager/
├── README.md           # このファイル
├── index.php          # トップページ（一覧）
├── upload.php         # アップロード処理
├── view.php           # CSVデータ表示
├── download.php       # ダウンロード処理
└── data/              # CSVファイル保存先
    └── .gitkeep
```

---

## 🚀 セットアップ

### 1. ファイルの配置

MAMP環境の `htdocs` フォルダ内に配置：

```bash
/Applications/MAMP/htdocs/csv-manager/
```

### 2. ブラウザでアクセス

```text
http://localhost:8888/csv-manager/
```

---

## 💡 実装のポイント

### セキュアなCSVアップロード（upload.php）

```php
<?php
if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {

    $tmpPath = $_FILES['csv_file']['tmp_name'];
    $originalName = $_FILES['csv_file']['name'];
    $fileSize = $_FILES['csv_file']['size'];

    // ファイルサイズ制限（2MB）
    if ($fileSize > 2 * 1024 * 1024) {
        die("エラー：ファイルサイズが大きすぎます（最大2MB）");
    }

    // 拡張子チェック
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if ($extension !== 'csv') {
        die("エラー：CSVファイルのみアップロード可能です");
    }

    // MIMEタイプチェック（finfoはmime_content_typeより推奨される方法です）
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);

    $allowedMimeTypes = ['text/csv', 'text/plain', 'application/csv'];
    if (!in_array($mimeType, $allowedMimeTypes)) {
        die("エラー：無効なファイルタイプです");
    }

    // ファイル名のサニタイズ
    $safeFileName = basename($originalName);
    $uniqueFileName = date('YmdHis') . '_' . $safeFileName;

    // アップロード先
    $uploadDir = 'data/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $destination = $uploadDir . $uniqueFileName;

    // ファイル移動
    if (move_uploaded_file($tmpPath, $destination)) {
        echo "アップロード成功！";
    }
}
?>
```

### CSVデータの表示（view.php）

```php
<?php
$filename = basename($_GET['file'] ?? '');

if (empty($filename)) {
    die("エラー：ファイル名が指定されていません");
}

$filePath = 'data/' . $filename;

if (!file_exists($filePath)) {
    die("エラー：ファイルが存在しません");
}

// CSVを読み込んで表示
$handle = fopen($filePath, 'r');

echo "<table border='1'>";

$rowNumber = 0;
while (($data = fgetcsv($handle)) !== false) {
    $rowNumber++;

    echo "<tr>";
    foreach ($data as $cell) {
        $tag = ($rowNumber === 1) ? 'th' : 'td';
        echo "<{$tag}>" . htmlspecialchars($cell) . "</{$tag}>";
    }
    echo "</tr>";
}

echo "</table>";

fclose($handle);
?>
```

### エラーハンドリング

```php
<?php
try {
    if (!file_exists($filePath)) {
        throw new Exception("ファイルが存在しません");
    }

    if (!is_readable($filePath)) {
        throw new Exception("ファイルが読み込めません");
    }

    $content = file_get_contents($filePath);

} catch (Exception $e) {
    echo "エラー：" . htmlspecialchars($e->getMessage());
    error_log($e->getMessage());
}
?>
```

---

## 🔒 セキュリティチェックリスト

- [ ] ファイルタイプチェック（拡張子とMIMEタイプ）
- [ ] ファイルサイズ制限
- [ ] `basename()` でパストラバーサル対策
- [ ] `htmlspecialchars()` でXSS対策
- [ ] エラーハンドリング（try-catch）
- [ ] ユニークなファイル名の生成

---

## 🎨 拡張アイデア

- CSVデータの編集機能
- CSVデータの検索・フィルタリング
- CSVデータのソート（列ごと）
- CSVファイルの削除機能
- ページネーション（大量データ対応）
- データベースへのインポート機能

---

**Let's vibe and code! 🎉**

このプロジェクトを通じて、セキュアなファイル操作を学ぼう！
