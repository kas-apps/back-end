# Lesson 01: ファイル操作 📁

**学習目標**：PHPでファイルの読み書き、CSVファイルの扱い、ファイルアップロードをマスターして、セキュアなファイル操作ができるようになる！

---

## 📖 このレッスンで学ぶこと

- ファイルの読み込み（file_get_contents、fopen、fread）
- ファイルへの書き込み（file_put_contents、fwrite）
- CSVファイルの読み込みと書き込み（fgetcsv、fputcsv）
- ディレクトリ操作（mkdir、scandir、is_dir、file_exists）
- ファイルのアップロード（$_FILES）
- **パストラバーサル対策**（basename、realpath） 🔒
- **ファイルタイプチェック**（mime_content_type、拡張子チェック） 🔒
- **ファイルサイズ制限** 🔒

---

## 🎯 なぜファイル操作を学ぶの？（Why）

### 実際のWebアプリケーションで超よく使う！

ファイル操作は、実践的なWebアプリケーションに欠かせない機能だよ！

**実際の使用例**：

- 📊 **CSVファイルのインポート・エクスポート**：商品データ、顧客データをExcelで管理
- 🖼️ **画像アップロード**：プロフィール画像、商品画像
- 📄 **ログファイル**：エラーログ、アクセスログの記録
- 💾 **データのバックアップ**：データベースの内容をファイルに保存
- 📝 **テキストファイル**：設定ファイル、キャッシュファイル

### バックエンド開発における重要性

ファイル操作は、**データベースと並んでバックエンドの中核機能**！

でも、**セキュリティリスクが超高い**分野でもある！

- 🚨 **パストラバーサル攻撃**：悪意のあるユーザーが、本来アクセスできないファイルを読み取る
- 🚨 **不正なファイルアップロード**：ウイルスや悪意のあるスクリプトがアップロードされる
- 🚨 **ファイル名の脆弱性**：特殊文字を使った攻撃

だからこそ、**最初から安全な書き方を学ぶ**ことが超重要！

---

## 🏗️ ファイル操作の基礎知識（What）

### ファイルシステムとは？

**ファイルシステム**は、コンピューターの「本棚」みたいなもの！

**アナロジー：図書館で考えてみよう**

- **ディレクトリ（フォルダ）**：本棚の棚
- **ファイル**：本
- **ファイルパス**：本の住所（「3階、歴史コーナー、3番目の棚」みたいな）

PHPでは、この「本棚」からファイルを読んだり、新しいファイルを書いたりできる！

### ファイルパスの種類

#### 絶対パス

「住所の完全版」

```php
<?php
// 絶対パス（MacのMAMP環境の例）
$absolutePath = "/Applications/MAMP/htdocs/myproject/data/sample.txt";
?>
```

**メリット**：どこからでも確実にファイルを指定できる
**デメリット**：環境が変わると動かない（本番サーバーでは違うパス）

#### 相対パス

「今いる場所から見た住所」

```php
<?php
// 相対パス（推奨！）
$relativePath = "data/sample.txt";  // 現在のディレクトリから見た位置
$parentPath = "../config/settings.txt";  // 一つ上のディレクトリ
?>
```

**メリット**：環境に依存しない、移植性が高い
**デメリット**：現在の位置を把握する必要がある

**推奨**：基本的に**相対パスを使おう**！

### セキュリティリスク：パストラバーサル攻撃

**超危険な例**：

```php
<?php
// 🚨 危険：パストラバーサル攻撃に脆弱
$filename = $_GET['file'];  // ユーザーが指定
$content = file_get_contents("uploads/" . $filename);
?>
```

**攻撃例**：

```text
悪意のあるユーザーが：
?file=../../config/database.php

を送ると、本来見えてはいけないファイルが読まれてしまう！
```

**対策方法（後で詳しく学ぶ）**：

- `basename()` を使ってディレクトリ部分を除去
- `realpath()` で実際のパスを確認
- ホワイトリスト（許可リスト）でファイル名をチェック

---

## 💻 ファイルの読み込み（How）

### 方法1：file_get_contents()（シンプル＆おすすめ）

**一度に全部読み込む**方法。小さいファイル向け！

```php
<?php
// ファイルの内容を全部読み込んで文字列として取得
$content = file_get_contents("data/sample.txt");

if ($content !== false) {
    echo "ファイルの内容：<br>";
    echo nl2br(htmlspecialchars($content));  // XSS対策
} else {
    echo "ファイルが読めませんでした";
}
?>
```

**ポイント**：

- 超シンプル！1行で読み込める
- 小さいファイル（数MB以下）に最適
- 失敗すると `false` を返す

### 方法2：file()（行ごとに配列で取得）

**1行ずつ配列に入れる**方法。CSVや設定ファイル向け！

```php
<?php
// ファイルを1行ずつ配列に格納
$lines = file("data/sample.txt");

if ($lines !== false) {
    foreach ($lines as $lineNumber => $line) {
        // 各行を処理（XSS対策も忘れずに）
        echo "行" . ($lineNumber + 1) . ": " . htmlspecialchars($line) . "<br>";
    }
} else {
    echo "ファイルが読めませんでした";
}
?>
```

**ポイント**：

- 1行ずつ処理できる
- メモリ効率は file_get_contents より少し良い

### 方法3：fopen() + fread()（大きいファイル向け）

**ファイルを「開いて」、「読んで」、「閉じる」**という手順。大きいファイル向け！

```php
<?php
// ファイルを開く（'r' = 読み込みモード）
$handle = fopen("data/sample.txt", "r");

if ($handle) {
    // ファイルサイズを取得
    $fileSize = filesize("data/sample.txt");

    // ファイルの終わりまで4KBずつ読み込む
    $content = '';
    while (!feof($handle)) {
        $content .= fread($handle, 4096);
    }

    // ファイルを閉じる（重要！）
    fclose($handle);

    echo "ファイルの内容：<br>";
    echo nl2br(htmlspecialchars($content));
} else {
    echo "ファイルが開けませんでした";
}
?>
```

**ポイント**：

- `fopen()` でファイルを開く
- `fread()` で読み込む
- `fclose()` で必ず閉じる（メモリリーク防止）

**fopenのモード**：

| モード | 意味                     | 用途           |
| ------ | ------------------------ | -------------- |
| 'r'    | 読み込み専用             | ファイルを読む |
| 'w'    | 書き込み専用（上書き）   | ファイルを書く |
| 'a'    | 追記モード               | ログファイル   |
| 'r+'   | 読み書き両方             | 更新           |

---

## ✍️ ファイルへの書き込み

### 方法1：file_put_contents()（シンプル＆おすすめ）

**一度に全部書き込む**方法！

```php
<?php
// 書き込む内容
$content = "こんにちは！\nこれはテストファイルです。\n";

// ファイルに書き込む
$result = file_put_contents("data/output.txt", $content);

if ($result !== false) {
    echo "ファイルに書き込みました（" . $result . "バイト）";
} else {
    echo "書き込みに失敗しました";
}
?>
```

**ポイント**：

- 超シンプル！1行で書き込める
- ファイルが存在しない場合、自動的に作成される
- 既存ファイルは**上書き**される

**追記モード**（既存の内容を残す）：

```php
<?php
$content = "追加の内容\n";

// FILE_APPEND フラグで追記モード
$result = file_put_contents("data/log.txt", $content, FILE_APPEND);
?>
```

### 方法2：fopen() + fwrite()

**細かい制御が必要な場合**に使う！

```php
<?php
// ファイルを開く（'w' = 書き込みモード、上書き）
$handle = fopen("data/output.txt", "w");

if ($handle) {
    // データを書き込む
    fwrite($handle, "1行目のデータ\n");
    fwrite($handle, "2行目のデータ\n");

    // ファイルを閉じる
    fclose($handle);

    echo "ファイルに書き込みました";
} else {
    echo "ファイルが開けませんでした";
}
?>
```

---

## 📊 CSVファイルの扱い

### CSVファイルとは？

**CSV（Comma-Separated Values）**：カンマで区切られたデータ

**例**：

```csv
名前,年齢,メール
山田太郎,25,taro@example.com
佐藤花子,30,hanako@example.com
鈴木一郎,28,ichiro@example.com
```

**Excelとの関係**：

- ExcelはCSVファイルを開ける
- Excelで「名前を付けて保存」→「CSV形式」で保存できる

### CSVファイルの読み込み

```php
<?php
// CSVファイルを開く
$handle = fopen("resources/sample.csv", "r");

if ($handle) {
    $rowNumber = 0;

    // 1行ずつ読み込む
    while (($data = fgetcsv($handle)) !== false) {
        $rowNumber++;

        if ($rowNumber === 1) {
            // 1行目はヘッダー
            echo "<h3>ヘッダー：" . implode(", ", $data) . "</h3>";
        } else {
            // データ行（XSS対策）
            echo "名前：" . htmlspecialchars($data[0]) . "<br>";
            echo "年齢：" . htmlspecialchars($data[1]) . "<br>";
            echo "メール：" . htmlspecialchars($data[2]) . "<br><br>";
        }
    }

    // ファイルを閉じる
    fclose($handle);
} else {
    echo "CSVファイルが開けませんでした";
}
?>
```

**ポイント**：

- `fgetcsv()` が1行を配列として返す
- ファイルの最後まで読むと `false` を返す
- `while` ループで1行ずつ処理

### CSVファイルの書き込み

```php
<?php
// CSVファイルを開く（書き込みモード）
$handle = fopen("data/export.csv", "w");

if ($handle) {
    // ヘッダー行
    fputcsv($handle, ["名前", "年齢", "メール"]);

    // データ行
    fputcsv($handle, ["山田太郎", 25, "taro@example.com"]);
    fputcsv($handle, ["佐藤花子", 30, "hanako@example.com"]);
    fputcsv($handle, ["鈴木一郎", 28, "ichiro@example.com"]);

    // ファイルを閉じる
    fclose($handle);

    echo "CSVファイルを作成しました";
} else {
    echo "CSVファイルが開けませんでした";
}
?>
```

**ポイント**：

- `fputcsv()` が配列をCSV形式で書き込む
- カンマやクォートの処理を自動でやってくれる

### 実用例：配列からCSVを作成

```php
<?php
// ユーザーデータ（連想配列の配列）
$users = [
    ["name" => "山田太郎", "age" => 25, "email" => "taro@example.com"],
    ["name" => "佐藤花子", "age" => 30, "email" => "hanako@example.com"],
    ["name" => "鈴木一郎", "age" => 28, "email" => "ichiro@example.com"]
];

// CSVファイルを作成
$handle = fopen("data/users.csv", "w");

if ($handle) {
    // ヘッダー行
    fputcsv($handle, ["名前", "年齢", "メール"]);

    // データ行
    foreach ($users as $user) {
        fputcsv($handle, [
            $user["name"],
            $user["age"],
            $user["email"]
        ]);
    }

    fclose($handle);
    echo "ユーザーデータをCSVにエクスポートしました";
}
?>
```

---

## 📂 ディレクトリ操作

### ディレクトリの作成

```php
<?php
// ディレクトリを作成
$dirName = "uploads";

if (!file_exists($dirName)) {
    // ディレクトリが存在しない場合、作成
    mkdir($dirName, 0755);
    echo "ディレクトリ '{$dirName}' を作成しました";
} else {
    echo "ディレクトリは既に存在します";
}
?>
```

**パーミッション（0755）**：

- `0755` = オーナーは読み書き実行、他は読み実行
- セキュリティのため、適切なパーミッションを設定

### ディレクトリの存在確認

```php
<?php
$dirName = "uploads";

if (is_dir($dirName)) {
    echo "'{$dirName}' はディレクトリです";
} else {
    echo "'{$dirName}' はディレクトリではありません";
}

if (file_exists($dirName)) {
    echo "'{$dirName}' は存在します";
} else {
    echo "'{$dirName}' は存在しません";
}
?>
```

### ディレクトリ内のファイル一覧

```php
<?php
$dirName = "uploads";

if (is_dir($dirName)) {
    // ディレクトリ内のファイルとフォルダを取得
    $files = scandir($dirName);

    echo "<h3>{$dirName} 内のファイル：</h3>";
    echo "<ul>";
    foreach ($files as $file) {
        // . と .. はスキップ
        if ($file !== "." && $file !== "..") {
            echo "<li>" . htmlspecialchars($file) . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "ディレクトリが存在しません";
}
?>
```

---

## 📤 ファイルのアップロード

### HTMLフォーム

```html
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ファイルアップロード</title>
</head>
<body>
    <h1>ファイルアップロード</h1>

    <!-- enctype="multipart/form-data" が必須！ -->
    <form method="POST" action="upload.php" enctype="multipart/form-data">
        <label>ファイルを選択：</label>
        <input type="file" name="uploaded_file" required>
        <br><br>
        <button type="submit">アップロード</button>
    </form>
</body>
</html>
```

**ポイント**：

- `enctype="multipart/form-data"` が必須
- `<input type="file">` でファイル選択

### PHPでの受け取り（セキュア版）

```php
<?php
// ファイルがアップロードされたかチェック
if (isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] === UPLOAD_ERR_OK) {

    // アップロードされたファイル情報
    $uploadedFile = $_FILES['uploaded_file'];
    $tmpPath = $uploadedFile['tmp_name'];  // 一時ファイルのパス
    $originalName = $uploadedFile['name'];  // 元のファイル名
    $fileSize = $uploadedFile['size'];  // ファイルサイズ（バイト）

    // セキュリティチェック1：ファイルサイズ制限（5MB以下）
    $maxSize = 5 * 1024 * 1024;  // 5MB
    if ($fileSize > $maxSize) {
        die("エラー：ファイルサイズが大きすぎます（最大5MB）");
    }

    // セキュリティチェック2：ファイルタイプ確認（画像のみ許可）
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($tmpPath);

    if (!in_array($fileType, $allowedTypes)) {
        die("エラー：画像ファイル（JPEG、PNG、GIF）のみアップロード可能です");
    }

    // セキュリティチェック3：拡張子確認
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions)) {
        die("エラー：許可されていない拡張子です");
    }

    // セキュリティチェック4：ファイル名のサニタイズ（パストラバーサル対策）
    $safeFileName = basename($originalName);  // ディレクトリ部分を除去

    // さらに安全のため、ユニークなファイル名を生成
    $uniqueFileName = uniqid() . '_' . $safeFileName;

    // アップロード先ディレクトリ
    $uploadDir = "uploads/";

    // ディレクトリが存在しない場合、作成
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // 最終的な保存パス
    $destination = $uploadDir . $uniqueFileName;

    // ファイルを移動
    if (move_uploaded_file($tmpPath, $destination)) {
        echo "アップロード成功！<br>";
        echo "ファイル名：" . htmlspecialchars($uniqueFileName) . "<br>";
        echo "サイズ：" . number_format($fileSize) . " バイト<br>";
        echo "タイプ：" . htmlspecialchars($fileType) . "<br>";
    } else {
        echo "エラー：ファイルの移動に失敗しました";
    }

} else {
    // エラーハンドリング
    if (isset($_FILES['uploaded_file'])) {
        $error = $_FILES['uploaded_file']['error'];
        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                echo "エラー：ファイルサイズが大きすぎます";
                break;
            case UPLOAD_ERR_PARTIAL:
                echo "エラー：ファイルが部分的にしかアップロードされませんでした";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "エラー：ファイルが選択されていません";
                break;
            default:
                echo "エラー：アップロードに失敗しました";
                break;
        }
    }
}
?>
```

**セキュリティポイント**：

1. ✅ **ファイルサイズ制限**：大きすぎるファイルを拒否
2. ✅ **ファイルタイプチェック**：MIMEタイプで確認
3. ✅ **拡張子チェック**：許可された拡張子のみ
4. ✅ **ファイル名のサニタイズ**：`basename()` でパストラバーサル対策
5. ✅ **ユニークなファイル名**：上書きを防止

---

## 🔒 セキュリティ対策（超重要！）

### 1. パストラバーサル対策

**危険なコード**：

```php
<?php
// 🚨 超危険：パストラバーサル攻撃に脆弱
$filename = $_GET['file'];
$content = file_get_contents("uploads/" . $filename);
echo $content;
?>
```

**攻撃例**：

```text
?file=../../config/database.php
→ 本来見えてはいけないファイルが読まれる！
```

**安全なコード**：

```php
<?php
// ✅ 安全：basename() でディレクトリ部分を除去
$filename = basename($_GET['file']);  // "../../config/database.php" → "database.php"

// さらに安全のため、ホワイトリストでチェック
$allowedFiles = ['file1.txt', 'file2.txt', 'file3.txt'];

if (in_array($filename, $allowedFiles)) {
    $content = file_get_contents("uploads/" . $filename);
    echo nl2br(htmlspecialchars($content));  // XSS対策も忘れずに
} else {
    echo "エラー：許可されていないファイルです";
}
?>
```

**安全対策**：

- `basename()` を使う
- ホワイトリストで許可されたファイルのみ
- `realpath()` で実際のパスを確認

### 2. ファイルタイプチェック

**拡張子だけではダメ！**

```php
<?php
// 🚨 危険：拡張子だけのチェックは不十分
$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if ($extension === 'jpg') {
    // 悪意のあるユーザーが .php ファイルを .jpg にリネームできる
}
?>
```

**安全なコード**：

```php
<?php
// ✅ 安全：MIMEタイプもチェック
$tmpPath = $_FILES['file']['tmp_name'];
$fileType = mime_content_type($tmpPath);
$extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

if (in_array($fileType, $allowedTypes) && in_array($extension, $allowedExtensions)) {
    // OK
} else {
    die("エラー：許可されていないファイルタイプです");
}
?>
```

### 3. ファイルサイズ制限

```php
<?php
// ファイルサイズ制限（5MB）
$maxSize = 5 * 1024 * 1024;

if ($_FILES['file']['size'] > $maxSize) {
    die("エラー：ファイルサイズが大きすぎます（最大5MB）");
}
?>
```

### 4. アップロード先ディレクトリの保護

**重要**：アップロード先ディレクトリを**ドキュメントルート外**に配置するのが理想

もしドキュメントルート内に置く場合、`.htaccess` でPHPの実行を禁止：

```apache
# uploads/.htaccess
<FilesMatch "\.php$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

---

## 🤖 バイブコーディング実践（最重要セクション！）

### AIへの指示例

#### 良い指示の例1：CSVファイルの読み込み

```text
「PHPで、resources/products.csvファイルを読み込んで、HTMLテーブルで表示するコードを書いてください。
CSVの1行目はヘッダー（商品名、価格、在庫）で、2行目以降がデータです。
XSS対策としてhtmlspecialchars()を使ってください。
ファイルが存在しない場合のエラーハンドリングも含めてください。」
```

**生成されるコードの例**：

```php
<?php
$csvFile = "resources/products.csv";

// ファイルの存在確認
if (!file_exists($csvFile)) {
    die("エラー：CSVファイルが見つかりません");
}

// CSVファイルを開く
$handle = fopen($csvFile, "r");

if ($handle) {
    echo "<table border='1'>";

    $rowNumber = 0;

    while (($data = fgetcsv($handle)) !== false) {
        $rowNumber++;

        if ($rowNumber === 1) {
            // ヘッダー行
            echo "<thead><tr>";
            foreach ($data as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr></thead><tbody>";
        } else {
            // データ行
            echo "<tr>";
            foreach ($data as $cell) {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
            echo "</tr>";
        }
    }

    echo "</tbody></table>";
    fclose($handle);
} else {
    echo "エラー：CSVファイルが開けませんでした";
}
?>
```

**なぜ良い？**

- ✅ ファイル名を明示
- ✅ XSS対策を指示
- ✅ エラーハンドリングを指示
- ✅ 出力形式（HTMLテーブル）を明示

#### 良い指示の例2：セキュアなファイルアップロード

```text
「PHPで、画像ファイル（JPEG、PNG、GIF）のみをアップロードできるコードを書いてください。
セキュリティ対策として以下を含めてください：
- ファイルサイズ制限（最大5MB）
- MIMEタイプチェック
- 拡張子チェック
- ファイル名のサニタイズ（basename使用）
- ユニークなファイル名の生成（uniqid使用）
アップロード先は uploads/ ディレクトリで、存在しない場合は作成してください。」
```

**なぜ良い？**

- ✅ 許可するファイルタイプを明示
- ✅ セキュリティ要件を具体的に指示
- ✅ 使う関数を指定（basename、uniqid）

#### 曖昧な指示の例（避けるべき）

```text
「ファイルをアップロードするコードを書いて」
```

**なぜダメ？**

- ❌ どんなファイルを許可するか不明
- ❌ セキュリティ要件が含まれていない
- ❌ エラーハンドリングの指示がない

#### 危険な指示の例（絶対NG）

```text
「どんなファイルでもアップロードできるようにして」
```

**なぜ危険？**

- 🚨 セキュリティ意識がゼロ
- 🚨 悪意のあるファイルがアップロードされる可能性
- 🚨 サーバーが乗っ取られるリスク

### 生成されたコードのチェックポイント

#### セキュリティチェック（最優先！）

- [ ] **パストラバーサル対策**：`basename()` が使われているか
- [ ] **ファイルタイプチェック**：`mime_content_type()` または拡張子チェックがあるか
- [ ] **ファイルサイズ制限**：大きすぎるファイルを拒否しているか
- [ ] **ホワイトリスト**：許可されたファイルのみ処理しているか
- [ ] **XSS対策**：ファイル名やファイル内容を出力する際に `htmlspecialchars()` を使っているか
- [ ] **エラーハンドリング**：ファイルが存在しない場合の処理があるか

#### 機能チェック

- [ ] **ファイルの存在確認**：`file_exists()` や `is_file()` でチェックしているか
- [ ] **ディレクトリの存在確認**：アップロード先ディレクトリが存在するかチェックしているか
- [ ] **ファイルを閉じる**：`fopen()` を使った場合、`fclose()` で閉じているか
- [ ] **一時ファイルの移動**：`move_uploaded_file()` を使っているか

#### コード品質チェック

- [ ] **変数名**：わかりやすい名前か（`$f` より `$filename`）
- [ ] **コメント**：セキュリティ対策部分に説明があるか
- [ ] **エラーメッセージ**：ユーザーに分かりやすいメッセージか

### よくある問題と修正方法

#### 問題1：パストラバーサル脆弱性

**AIが生成しがちな危険なコード**：

```php
<?php
$filename = $_GET['file'];
$content = file_get_contents("uploads/" . $filename);
echo $content;
?>
```

**原因**：ユーザー入力をそのまま使っている

**修正**：

```php
<?php
// basename() でディレクトリ部分を除去
$filename = basename($_GET['file']);

// ホワイトリストでチェック
$allowedFiles = ['file1.txt', 'file2.txt', 'file3.txt'];

if (in_array($filename, $allowedFiles)) {
    $content = file_get_contents("uploads/" . $filename);
    echo nl2br(htmlspecialchars($content));
} else {
    echo "エラー：許可されていないファイルです";
}
?>
```

**AIへの修正指示**：

```text
「パストラバーサル対策として、basename()でファイル名を安全にしてください。
さらに、ホワイトリストで許可されたファイルのみアクセスできるようにしてください。」
```

#### 問題2：ファイルタイプチェック不足

**AIが生成しがちなコード**：

```php
<?php
// 拡張子だけのチェック（不十分）
$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
if ($extension === 'jpg') {
    move_uploaded_file($_FILES['file']['tmp_name'], "uploads/" . $_FILES['file']['name']);
}
?>
```

**原因**：MIMEタイプをチェックしていない

**修正**：

```php
<?php
$tmpPath = $_FILES['file']['tmp_name'];
$fileType = mime_content_type($tmpPath);
$extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

$allowedTypes = ['image/jpeg', 'image/png'];
$allowedExtensions = ['jpg', 'jpeg', 'png'];

if (in_array($fileType, $allowedTypes) && in_array($extension, $allowedExtensions)) {
    $safeFileName = basename($_FILES['file']['name']);
    move_uploaded_file($tmpPath, "uploads/" . $safeFileName);
} else {
    echo "エラー：許可されていないファイルタイプです";
}
?>
```

**AIへの修正指示**：

```text
「ファイルタイプチェックを強化してください。
拡張子だけでなく、mime_content_type()でMIMEタイプもチェックしてください。」
```

#### 問題3：ファイルを閉じ忘れ

**AIが生成しがちなコード**：

```php
<?php
$handle = fopen("data/sample.txt", "r");
$content = fread($handle, filesize("data/sample.txt"));
// fclose() を忘れている！
echo $content;
?>
```

**原因**：`fclose()` を忘れている

**修正**：

```php
<?php
$handle = fopen("data/sample.txt", "r");
if ($handle) {
    $content = fread($handle, filesize("data/sample.txt"));
    fclose($handle);  // 必ず閉じる
    echo htmlspecialchars($content);
}
?>
```

**AIへの修正指示**：

```text
「fopen()でファイルを開いたら、必ずfclose()で閉じるようにしてください。」
```

---

## 💪 演習

実際に手を動かして、ファイル操作をマスターしよう！

演習問題はこちら：
👉 **[演習問題を見る](exercises/README.md)**

---

## ✅ まとめ

このレッスンで学んだことを振り返ろう！

### ファイルの読み込み

- ✅ `file_get_contents()`：シンプルに全部読む
- ✅ `file()`：行ごとに配列で読む
- ✅ `fopen()` + `fread()`：大きいファイル向け

### ファイルの書き込み

- ✅ `file_put_contents()`：シンプルに全部書く
- ✅ `FILE_APPEND` フラグで追記
- ✅ `fopen()` + `fwrite()`：細かい制御

### CSVファイル

- ✅ `fgetcsv()`：CSVを1行ずつ読む
- ✅ `fputcsv()`：配列をCSV形式で書く

### ディレクトリ操作

- ✅ `mkdir()`：ディレクトリ作成
- ✅ `is_dir()`、`file_exists()`：存在確認
- ✅ `scandir()`：ファイル一覧取得

### ファイルアップロード

- ✅ `$_FILES` でアップロードファイルを受け取る
- ✅ `move_uploaded_file()` でファイルを移動

### セキュリティ対策（超重要！）

- ✅ **パストラバーサル対策**：`basename()` でサニタイズ
- ✅ **ファイルタイプチェック**：`mime_content_type()` と拡張子チェック
- ✅ **ファイルサイズ制限**：大きすぎるファイルを拒否
- ✅ **ホワイトリスト**：許可されたファイルのみ処理
- ✅ **XSS対策**：`htmlspecialchars()` で出力

---

## 🚀 次のステップ

ファイル操作をマスターしたね！すごい！✨

次は**Lesson 02: セッション管理**で：

- セッションの仕組み
- ログイン機能の実装
- セッションハイジャック対策（`session_regenerate_id()`）
- セキュアなセッション管理

を学んでいくよ！

Lesson 01で学んだファイル操作の知識を使いながら、ユーザーの状態を管理する方法を学ぶ！

👉 **[Lesson 02: セッション管理へ進む](../02-sessions/README.md)**

---

**Let's vibe and code! 🎉**

ファイル操作の基礎が身についた！セキュリティ対策も忘れずにね！🔒
