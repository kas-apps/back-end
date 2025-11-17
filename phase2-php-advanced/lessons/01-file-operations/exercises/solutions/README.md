# Lesson 01: ファイル操作 - 解答例と解説 ✅

演習問題の解答例と、それぞれの解説を載せています。

**重要**：解答例は一つの正解ではありません。別の実装方法もあるので、AIと一緒に探してみよう！

---

## 演習 01-01: テキストファイルの読み込み

### 解答例

ファイル：`01-01-read-file.php`

```php
<?php
// ファイルパス
$filePath = "../resources/sample.txt";

// ファイルの存在確認
if (file_exists($filePath)) {
    // ファイルを読み込む
    $content = file_get_contents($filePath);

    if ($content !== false) {
        // 成功：内容を表示（XSS対策＋改行保持）
        echo "<h2>ファイルの内容：</h2>";
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
    } else {
        // 読み込み失敗
        echo "エラー：ファイルの読み込みに失敗しました";
    }
} else {
    // ファイルが存在しない
    echo "エラー：ファイルが見つかりません";
}
?>
```

### 解説

**ポイント1：ファイルの存在確認**

```php
if (file_exists($filePath)) {
    // 存在する場合の処理
}
```

- `file_get_contents()` の前に `file_exists()` でチェック
- より明確なエラーメッセージを出せる

**ポイント2：XSS対策**

```php
echo htmlspecialchars($content);
```

- ファイルの内容をそのまま表示すると、HTMLタグやJavaScriptが実行される危険がある
- `htmlspecialchars()` で必ずエスケープ

**ポイント3：改行の保持**

```php
// 方法1：<pre>タグを使う
echo "<pre>" . htmlspecialchars($content) . "</pre>";

// 方法2：nl2br()を使う
echo nl2br(htmlspecialchars($content));
```

### 実行結果

```text
ファイルの内容：
これはサンプルテキストファイルです。
2行目の内容です。
3行目の内容です。
```

---

## 演習 01-02: テキストファイルへの書き込み

### 解答例

ファイル1：`01-02-memo-form.html`

```html
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メモ保存</title>
</head>
<body>
    <h1>メモを保存</h1>

    <form method="POST" action="01-02-memo-save.php">
        <label for="memo">メモの内容：</label><br>
        <textarea name="memo" id="memo" rows="10" cols="50" required></textarea>
        <br><br>
        <button type="submit">保存</button>
    </form>
</body>
</html>
```

ファイル2：`01-02-memo-save.php`

```php
<?php
// POSTデータの確認
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['memo'])) {

    // 入力内容を取得
    $memo = $_POST['memo'];

    // 保存先ディレクトリ
    $dir = "../../data/";

    // ディレクトリが存在しない場合、作成
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // ファイルパス
    $filePath = $dir . "memo.txt";

    // ファイルに書き込む
    $result = file_put_contents($filePath, $memo);

    if ($result !== false) {
        echo "<h2>✅ 保存しました</h2>";
        echo "<p>" . $result . " バイト書き込みました</p>";
        echo "<a href='01-02-memo-form.html'>戻る</a>";
    } else {
        echo "<h2>❌ エラー：保存に失敗しました</h2>";
        echo "<a href='01-02-memo-form.html'>戻る</a>";
    }

} else {
    echo "エラー：不正なリクエストです";
}
?>
```

### 解説

**ポイント1：ディレクトリの存在確認と作成**

```php
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}
```

- `is_dir()` でディレクトリの存在をチェック
- 存在しない場合、`mkdir()` で作成
- 第2引数 `0755` はパーミッション
- 第3引数 `true` で親ディレクトリも自動作成

**ポイント2：書き込み結果の確認**

```php
$result = file_put_contents($filePath, $memo);

if ($result !== false) {
    // 成功（書き込んだバイト数が返る）
}
```

**注意点：XSS対策は不要？**

今回は、ファイルに書き込むだけで画面に表示していないので、`htmlspecialchars()` は不要です。
ただし、後でファイルの内容を読み込んで表示する場合は、その時点で `htmlspecialchars()` が必要！

---

## 演習 01-03: ディレクトリ内のファイル一覧表示

### 解答例

ファイル：`01-03-list-files.php`

```php
<?php
// ディレクトリパス
$dirPath = "../../data/";

echo "<h2>data ディレクトリの内容：</h2>";

// ディレクトリの存在確認
if (is_dir($dirPath)) {

    // ファイルとフォルダを取得
    $items = scandir($dirPath);

    echo "<ul>";

    foreach ($items as $item) {
        // . と .. はスキップ
        if ($item === "." || $item === "..") {
            continue;
        }

        // フルパス
        $fullPath = $dirPath . $item;

        // ディレクトリかファイルか判定
        if (is_dir($fullPath)) {
            echo "<li>[DIR] " . htmlspecialchars($item) . "</li>";
        } else {
            echo "<li>[FILE] " . htmlspecialchars($item) . "</li>";
        }
    }

    echo "</ul>";

} else {
    echo "エラー：ディレクトリが存在しません";
}
?>
```

### 解説

**ポイント1：. と .. をスキップ**

```php
if ($item === "." || $item === "..") {
    continue;
}
```

- `scandir()` は `.`（カレントディレクトリ）と `..`（親ディレクトリ）も返す
- これらは表示する必要がないのでスキップ

**ポイント2：ディレクトリとファイルの判定**

```php
if (is_dir($fullPath)) {
    // ディレクトリ
} else {
    // ファイル
}
```

**ポイント3：XSS対策**

```php
echo htmlspecialchars($item);
```

- ファイル名に特殊文字が含まれている可能性があるので、必ずエスケープ

---

## 演習 01-04: CSVファイルの読み込みとHTMLテーブル表示

### 解答例

ファイル：`01-04-csv-table.php`

```php
<?php
// CSVファイルパス
$csvPath = "../resources/products.csv";

// ファイルの存在確認
if (!file_exists($csvPath)) {
    die("エラー：CSVファイルが見つかりません");
}

// CSVファイルを開く
$handle = fopen($csvPath, "r");

if ($handle) {

    echo "<h2>商品一覧</h2>";
    echo "<table border='1' cellpadding='10'>";

    $rowNumber = 0;

    // 1行ずつ読み込む
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

    // ファイルを閉じる
    fclose($handle);

} else {
    echo "エラー：CSVファイルが開けませんでした";
}
?>
```

### 解説

**ポイント1：while + fgetcsv のパターン**

```php
while (($data = fgetcsv($handle)) !== false) {
    // $data は1行分の配列
}
```

- `fgetcsv()` は1行を配列として返す
- ファイルの最後まで読むと `false` を返す
- `while` ループで全行を処理

**ポイント2：ヘッダー行とデータ行の区別**

```php
if ($rowNumber === 1) {
    // ヘッダー行は <th> で表示
} else {
    // データ行は <td> で表示
}
```

**ポイント3：XSS対策**

```php
echo htmlspecialchars($cell);
```

- CSVの内容に悪意のあるHTMLやJavaScriptが含まれている可能性
- 必ず `htmlspecialchars()` でエスケープ

---

## 演習 01-05: CSVファイルのエクスポート

### 解答例

ファイル：`01-05-csv-export.php`

```php
<?php
// ユーザーデータ
$users = [
    ["name" => "山田太郎", "age" => 25, "email" => "taro@example.com"],
    ["name" => "佐藤花子", "age" => 30, "email" => "hanako@example.com"],
    ["name" => "鈴木一郎", "age" => 28, "email" => "ichiro@example.com"]
];

// 保存先ディレクトリ
$dir = "../../data/";

// ディレクトリが存在しない場合、作成
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// CSVファイルパス
$csvPath = $dir . "users.csv";

// CSVファイルを開く（書き込みモード）
$handle = fopen($csvPath, "w");

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

    // ファイルを閉じる
    fclose($handle);

    echo "<h2>✅ CSVファイルを作成しました</h2>";
    echo "<p>ファイルパス：" . htmlspecialchars($csvPath) . "</p>";
    echo "<p>ユーザー数：" . count($users) . " 人</p>";

} else {
    echo "エラー：CSVファイルが開けませんでした";
}
?>
```

### 解説

**ポイント1：fputcsv の便利さ**

```php
fputcsv($handle, ["名前", "年齢", "メール"]);
```

- 配列を渡すだけで、自動的にカンマ区切りのCSV形式に変換
- クォートやエスケープの処理も自動

**ポイント2：連想配列からCSVへ**

```php
foreach ($users as $user) {
    fputcsv($handle, [
        $user["name"],
        $user["age"],
        $user["email"]
    ]);
}
```

- 連想配列の値を順番に配列に入れる
- この順番がCSVの列の順番になる

---

## 演習 01-06: パストラバーサル脆弱性の修正

### 元のコード（脆弱）

```php
<?php
// 🚨 危険：パストラバーサル攻撃に脆弱
$filename = $_GET['file'];
$content = file_get_contents("data/" . $filename);
echo $content;
?>
```

### 解答例（セキュア版）

ファイル：`01-06-secure-file-read.php`

```php
<?php
// ホワイトリスト（許可するファイル）
$allowedFiles = ['memo.txt', 'sample.txt', 'log.txt'];

// ユーザー入力を取得
if (isset($_GET['file'])) {

    // basename() でディレクトリ部分を除去（パストラバーサル対策）
    $filename = basename($_GET['file']);

    // ホワイトリストでチェック
    if (in_array($filename, $allowedFiles)) {

        // ファイルパス
        $filePath = "../../data/" . $filename;

        // ファイルの存在確認
        if (file_exists($filePath)) {

            // ファイルを読み込む
            $content = file_get_contents($filePath);

            if ($content !== false) {
                echo "<h2>ファイルの内容：</h2>";
                echo "<pre>" . htmlspecialchars($content) . "</pre>";
            } else {
                echo "エラー：ファイルの読み込みに失敗しました";
            }

        } else {
            echo "エラー：ファイルが存在しません";
        }

    } else {
        echo "エラー：許可されていないファイルです";
    }

} else {
    echo "エラー：ファイル名が指定されていません";
}
?>
```

### 解説

**脆弱性の説明**

元のコードの問題点：

```php
$filename = $_GET['file'];
$content = file_get_contents("data/" . $filename);
```

**攻撃例**：

```text
?file=../../config/database.php
→ "data/../../config/database.php" が読まれる
→ 実際のパス: "config/database.php"（親ディレクトリに遡っている）
```

**対策1：basename() の使用**

```php
$filename = basename($_GET['file']);
```

- `basename()` はパスからファイル名部分だけを取り出す
- `basename("../../config/database.php")` → `"database.php"`
- ディレクトリ部分が除去されるので、親ディレクトリに遡れない

**対策2：ホワイトリスト**

```php
$allowedFiles = ['memo.txt', 'sample.txt', 'log.txt'];

if (in_array($filename, $allowedFiles)) {
    // OK
} else {
    // NG
}
```

- 許可されたファイルのみアクセスを許可
- **最も強力なセキュリティ対策**

**対策3：XSS対策**

```php
echo htmlspecialchars($content);
```

- ファイルの内容を表示する際も、必ずエスケープ

### テスト方法

**正常なアクセス**：

```text
?file=memo.txt → OK（許可されているファイル）
```

**攻撃を試みる**：

```text
?file=../../config/database.php
→ basename() により "database.php" になる
→ ホワイトリストにないので拒否される
```

---

## 演習 01-07: セキュアなファイルアップロード

### 解答例

ファイル1：`01-07-upload-form.html`

```html
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>画像アップロード</title>
</head>
<body>
    <h1>画像アップロード</h1>

    <form method="POST" action="01-07-upload.php" enctype="multipart/form-data">
        <label>画像ファイルを選択（JPEG、PNG）：</label><br>
        <input type="file" name="image" accept="image/jpeg,image/png" required>
        <br><br>
        <p>※ 最大2MBまで</p>
        <button type="submit">アップロード</button>
    </form>
</body>
</html>
```

ファイル2：`01-07-upload.php`

```php
<?php
// ファイルがアップロードされたかチェック
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

    // アップロードされたファイル情報
    $uploadedFile = $_FILES['image'];
    $tmpPath = $uploadedFile['tmp_name'];
    $originalName = $uploadedFile['name'];
    $fileSize = $uploadedFile['size'];

    // セキュリティチェック1：ファイルサイズ制限（2MB以下）
    $maxSize = 2 * 1024 * 1024;  // 2MB

    if ($fileSize > $maxSize) {
        die("❌ エラー：ファイルサイズが大きすぎます（最大2MB）");
    }

    // セキュリティチェック2：MIMEタイプ確認
    $allowedTypes = ['image/jpeg', 'image/png'];
    $fileType = mime_content_type($tmpPath);

    if (!in_array($fileType, $allowedTypes)) {
        die("❌ エラー：画像ファイル（JPEG、PNG）のみアップロード可能です");
    }

    // セキュリティチェック3：拡張子確認
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions)) {
        die("❌ エラー：許可されていない拡張子です");
    }

    // セキュリティチェック4：ファイル名のサニタイズ
    $safeFileName = basename($originalName);

    // セキュリティチェック5：ユニークなファイル名を生成
    $uniqueFileName = uniqid() . '_' . $safeFileName;

    // アップロード先ディレクトリ
    $uploadDir = "../../uploads/";

    // ディレクトリが存在しない場合、作成
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // 最終的な保存パス
    $destination = $uploadDir . $uniqueFileName;

    // ファイルを移動
    if (move_uploaded_file($tmpPath, $destination)) {
        echo "<h2>✅ アップロード成功！</h2>";
        echo "<p>ファイル名：" . htmlspecialchars($uniqueFileName) . "</p>";
        echo "<p>サイズ：" . number_format($fileSize) . " バイト</p>";
        echo "<p>タイプ：" . htmlspecialchars($fileType) . "</p>";
        echo "<br>";
        echo "<img src='" . htmlspecialchars($destination) . "' style='max-width: 300px;'>";
        echo "<br><br>";
        echo "<a href='01-07-upload-form.html'>戻る</a>";
    } else {
        echo "❌ エラー：ファイルの移動に失敗しました";
    }

} else {
    // エラーハンドリング
    if (isset($_FILES['image'])) {
        $error = $_FILES['image']['error'];

        switch ($error) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                echo "❌ エラー：ファイルサイズが大きすぎます";
                break;
            case UPLOAD_ERR_PARTIAL:
                echo "❌ エラー：ファイルが部分的にしかアップロードされませんでした";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "❌ エラー：ファイルが選択されていません";
                break;
            default:
                echo "❌ エラー：アップロードに失敗しました（エラーコード：" . $error . "）";
                break;
        }
    } else {
        echo "❌ エラー：不正なリクエストです";
    }
}
?>
```

### 解説

**セキュリティチェック1：ファイルサイズ制限**

```php
$maxSize = 2 * 1024 * 1024;  // 2MB

if ($fileSize > $maxSize) {
    die("エラー：ファイルサイズが大きすぎます");
}
```

- 大きすぎるファイルによるサーバーリソースの枯渇を防ぐ

**セキュリティチェック2：MIMEタイプ確認**

```php
$fileType = mime_content_type($tmpPath);

if (!in_array($fileType, $allowedTypes)) {
    die("エラー：許可されていないファイルタイプです");
}
```

- ファイルの実際の内容（MIMEタイプ）を確認
- 拡張子を偽装した悪意のあるファイルを防ぐ

**セキュリティチェック3：拡張子確認**

```php
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

if (!in_array($extension, $allowedExtensions)) {
    die("エラー：許可されていない拡張子です");
}
```

- MIMEタイプと拡張子の両方をチェック（二重チェック）

**セキュリティチェック4：ファイル名のサニタイズ**

```php
$safeFileName = basename($originalName);
```

- パストラバーサル対策
- ディレクトリ部分を除去

**セキュリティチェック5：ユニークなファイル名**

```php
$uniqueFileName = uniqid() . '_' . $safeFileName;
```

- 既存ファイルの上書きを防止
- `uniqid()` でユニークなIDを生成

**move_uploaded_file() の重要性**

```php
move_uploaded_file($tmpPath, $destination);
```

- アップロードされたファイルであることを確認してから移動
- セキュリティ上、`rename()` や `copy()` ではなく、`move_uploaded_file()` を使う

---

## 演習 01-08: 簡易ブログシステム（ファイルベース）

### 解答例

この総合チャレンジは大規模なので、ファイルが複数になります。

**ファイル構成**：

```text
01-08-blog-index.php    ← 記事一覧
01-08-blog-view.php     ← 記事詳細
01-08-blog-create.php   ← 新規作成フォーム
01-08-blog-save.php     ← 保存処理
01-08-blog-delete.php   ← 削除処理
```

ファイル1：`01-08-blog-index.php`

```php
<?php
// 記事一覧表示
$postsDir = "../../data/posts/";

// ディレクトリが存在しない場合、作成
if (!is_dir($postsDir)) {
    mkdir($postsDir, 0755, true);
}

echo "<h1>簡易ブログ</h1>";
echo "<p><a href='01-08-blog-create.php'>新規記事を作成</a></p>";

// ディレクトリ内のファイルを取得
$files = scandir($postsDir);

// ファイルを新しい順に並べ替え（ファイル名がタイムスタンプなので降順）
$files = array_reverse($files);

$postCount = 0;

echo "<ul>";

foreach ($files as $file) {
    // . と .. と 隠しファイルはスキップ
    if ($file === "." || $file === ".." || $file[0] === ".") {
        continue;
    }

    $filePath = $postsDir . $file;

    // ファイルのみ対象（ディレクトリは除外）
    if (is_file($filePath)) {
        $postCount++;

        // ファイルの1行目（タイトル）を取得
        $handle = fopen($filePath, "r");
        $title = fgets($handle);
        fclose($handle);

        // リンクを表示
        echo "<li>";
        echo "<a href='01-08-blog-view.php?file=" . urlencode($file) . "'>";
        echo htmlspecialchars(trim($title));
        echo "</a>";
        echo " (" . htmlspecialchars($file) . ")";
        echo "</li>";
    }
}

echo "</ul>";

if ($postCount === 0) {
    echo "<p>まだ記事がありません</p>";
}
?>
```

ファイル2：`01-08-blog-view.php`

```php
<?php
// 記事詳細表示
$postsDir = "../../data/posts/";

if (isset($_GET['file'])) {

    // パストラバーサル対策：basename() でサニタイズ
    $filename = basename($_GET['file']);

    // ファイル名のバリデーション（タイムスタンプ形式のみ許可）
    if (!preg_match('/^\d{8}_\d{6}\.txt$/', $filename)) {
        die("エラー：不正なファイル名です");
    }

    $filePath = $postsDir . $filename;

    // ファイルの存在確認
    if (file_exists($filePath) && is_file($filePath)) {

        // ファイルを読み込む
        $lines = file($filePath);

        // 1行目がタイトル、2行目以降が本文
        $title = trim($lines[0]);
        $body = implode("", array_slice($lines, 1));

        echo "<h1>" . htmlspecialchars($title) . "</h1>";
        echo "<p>ファイル名：" . htmlspecialchars($filename) . "</p>";
        echo "<hr>";
        echo "<div>" . nl2br(htmlspecialchars($body)) . "</div>";
        echo "<hr>";
        echo "<p>";
        echo "<a href='01-08-blog-index.php'>一覧に戻る</a> | ";
        echo "<a href='01-08-blog-delete.php?file=" . urlencode($filename) . "' onclick='return confirm(\"本当に削除しますか？\")'>削除</a>";
        echo "</p>";

    } else {
        echo "エラー：記事が見つかりません";
        echo "<br><a href='01-08-blog-index.php'>一覧に戻る</a>";
    }

} else {
    echo "エラー：ファイル名が指定されていません";
    echo "<br><a href='01-08-blog-index.php'>一覧に戻る</a>";
}
?>
```

ファイル3：`01-08-blog-create.php`

```php
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規記事作成</title>
</head>
<body>
    <h1>新規記事作成</h1>

    <form method="POST" action="01-08-blog-save.php">
        <label for="title">タイトル：</label><br>
        <input type="text" name="title" id="title" size="50" required>
        <br><br>

        <label for="body">本文：</label><br>
        <textarea name="body" id="body" rows="15" cols="60" required></textarea>
        <br><br>

        <button type="submit">保存</button>
        <a href="01-08-blog-index.php">キャンセル</a>
    </form>
</body>
</html>
```

ファイル4：`01-08-blog-save.php`

```php
<?php
// 記事保存処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title']) && isset($_POST['body'])) {

    $title = trim($_POST['title']);
    $body = trim($_POST['body']);

    // バリデーション
    if (empty($title) || empty($body)) {
        die("エラー：タイトルと本文を入力してください");
    }

    // ファイル名を生成（YYYYMMDD_HHMMSS.txt）
    $filename = date('Ymd_His') . '.txt';

    // 保存先ディレクトリ
    $postsDir = "../../data/posts/";

    // ディレクトリが存在しない場合、作成
    if (!is_dir($postsDir)) {
        mkdir($postsDir, 0755, true);
    }

    // ファイルパス
    $filePath = $postsDir . $filename;

    // ファイルの内容（1行目：タイトル、2行目以降：本文）
    $content = $title . "\n" . $body;

    // ファイルに書き込む
    $result = file_put_contents($filePath, $content);

    if ($result !== false) {
        echo "<h2>✅ 記事を保存しました</h2>";
        echo "<p>ファイル名：" . htmlspecialchars($filename) . "</p>";
        echo "<p><a href='01-08-blog-view.php?file=" . urlencode($filename) . "'>記事を見る</a></p>";
        echo "<p><a href='01-08-blog-index.php'>一覧に戻る</a></p>";
    } else {
        echo "❌ エラー：保存に失敗しました";
        echo "<br><a href='01-08-blog-create.php'>戻る</a>";
    }

} else {
    echo "エラー：不正なリクエストです";
    echo "<br><a href='01-08-blog-index.php'>一覧に戻る</a>";
}
?>
```

ファイル5：`01-08-blog-delete.php`

```php
<?php
// 記事削除処理
$postsDir = "../../data/posts/";

if (isset($_GET['file'])) {

    // パストラバーサル対策：basename() でサニタイズ
    $filename = basename($_GET['file']);

    // ファイル名のバリデーション（タイムスタンプ形式のみ許可）
    if (!preg_match('/^\d{8}_\d{6}\.txt$/', $filename)) {
        die("エラー：不正なファイル名です");
    }

    $filePath = $postsDir . $filename;

    // ファイルの存在確認
    if (file_exists($filePath) && is_file($filePath)) {

        // ファイルを削除
        if (unlink($filePath)) {
            echo "<h2>✅ 記事を削除しました</h2>";
            echo "<p>ファイル名：" . htmlspecialchars($filename) . "</p>";
            echo "<p><a href='01-08-blog-index.php'>一覧に戻る</a></p>";
        } else {
            echo "❌ エラー：削除に失敗しました";
            echo "<br><a href='01-08-blog-index.php'>一覧に戻る</a>";
        }

    } else {
        echo "エラー：記事が見つかりません";
        echo "<br><a href='01-08-blog-index.php'>一覧に戻る</a>";
    }

} else {
    echo "エラー：ファイル名が指定されていません";
    echo "<br><a href='01-08-blog-index.php'>一覧に戻る</a>";
}
?>
```

### 解説

**セキュリティポイント1：ファイル名のバリデーション**

```php
if (!preg_match('/^\d{8}_\d{6}\.txt$/', $filename)) {
    die("エラー：不正なファイル名です");
}
```

- 正規表現でファイル名の形式をチェック
- `YYYYMMDD_HHMMSS.txt` 形式のみ許可
- 予期しないファイルへのアクセスを防ぐ

**セキュリティポイント2：basename() でサニタイズ**

```php
$filename = basename($_GET['file']);
```

- パストラバーサル攻撃を防ぐ

**セキュリティポイント3：XSS対策**

```php
echo htmlspecialchars($title);
echo nl2br(htmlspecialchars($body));
```

- タイトルと本文を表示する際に必ずエスケープ

**機能ポイント：ファイル名のタイムスタンプ**

```php
$filename = date('Ymd_His') . '.txt';
```

- `date('Ymd_His')` で現在時刻をファイル名に
- 例：`20250117_143052.txt`
- 時系列順にソートしやすい

**拡張アイデア**：

- 記事の編集機能
- カテゴリ分類（サブディレクトリで管理）
- 検索機能
- ページネーション（記事が多い場合）

---

## 💡 学習のヒント

### コードを読む習慣をつけよう

解答例を見るだけでなく、以下を考えてみよう：

- なぜこう書いているのか？
- 別の書き方はあるか？
- セキュリティ対策はどこにあるか？

### AIと一緒に改善しよう

解答例を見た後、AIに質問してみよう：

```text
「この解答例のコードをもっと良くする方法はありますか？」
「このコードにセキュリティ上の問題はありますか？」
「エラーハンドリングをもっと充実させるには？」
```

### 自分なりのカスタマイズを加えよう

解答例をそのまま使うのではなく：

- 変数名をもっとわかりやすくする
- コメントを追加する
- エラーメッセージをもっと親切にする
- 見た目（HTML/CSS）を改善する

---

**Let's vibe and code! 🎉**

演習を通じて、セキュアなファイル操作ができるようになったね！✨
