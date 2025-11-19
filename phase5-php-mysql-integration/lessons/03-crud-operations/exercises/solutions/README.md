# Lesson 03: CRUD操作の実装 - 解答例 📝

各問題の解答例と解説を示します。実際のアプリケーション開発に使えるコードです！

---

## 🌱 基礎編

### 問題3-1：Create - 商品登録フォーム - 解答例

**解答**：

```php
<?php
// config.phpを読み込み（データベース接続）
require_once 'config.php';

// エラーメッセージを格納する配列
$errors = [];
$success = false;

// POSTリクエストの場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームからデータを取得
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';

    // バリデーション
    if (empty($name)) {
        $errors[] = "商品名は必須です。";
    }

    if (empty($price)) {
        $errors[] = "価格は必須です。";
    } elseif (!is_numeric($price) || $price < 0) {
        $errors[] = "価格は0以上の数値で入力してください。";
    }

    if (!empty($stock) && (!is_numeric($stock) || $stock < 0)) {
        $errors[] = "在庫は0以上の整数で入力してください。";
    }

    // エラーがなければデータベースに保存
    if (empty($errors)) {
        try {
            // プリペアドステートメントを準備
            $stmt = $pdo->prepare("
                INSERT INTO products (name, description, price, stock)
                VALUES (:name, :description, :price, :stock)
            ");

            // パラメータをバインド
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);

            // 実行
            $stmt->execute();

            // 挿入したIDを取得
            $lastId = $pdo->lastInsertId();

            $success = true;
            $successMessage = "商品を登録しました！（ID: {$lastId}）";

            // フォームをクリア
            $name = $description = $price = $stock = '';

        } catch (PDOException $e) {
            $errors[] = "データベースエラーが発生しました。";
            // 本番環境ではログに記録
            // error_log($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品登録</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>商品登録</h1>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success">
            <p><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>商品名 <span style="color: red;">*</span></label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>説明</label>
        <textarea name="description" rows="5"><?php echo htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>

        <label>価格（円） <span style="color: red;">*</span></label>
        <input type="number" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($price ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>在庫数</label>
        <input type="number" name="stock" min="0" value="<?php echo htmlspecialchars($stock ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        <button type="submit">登録</button>
    </form>

    <p><a href="list.php">商品一覧に戻る</a></p>
</body>
</html>
```

**解説**：

✅ **セキュリティポイント**

- プリペアドステートメントでINSERT
- すべての出力に`htmlspecialchars()`使用
- バリデーション（空チェック、数値チェック）
- try-catchでエラーハンドリング

💡 **コードのポイント**

- `lastInsertId()`で挿入したIDを取得
- フォームとPHP処理を1ファイルにまとめた
- 成功時にフォームをクリア
- HTMLのrequired属性でクライアントサイドバリデーション

---

### 問題3-2：Read - 商品一覧表示 - 解答例

**解答**：

```php
<?php
// config.phpを読み込み（データベース接続）
require_once 'config.php';

try {
    // すべての商品を取得（新しい順）
    $stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("データベースエラーが発生しました。");
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品一覧</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
        }
        .btn {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn:hover {
            background-color: #218838;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>商品一覧</h1>

    <p><a href="create.php" class="btn">新規登録</a></p>

    <?php if (count($products) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>商品名</th>
                <th>価格</th>
                <th>在庫</th>
                <th>登録日</th>
                <th>操作</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo number_format($product['price']); ?>円</td>
                    <td><?php echo htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($product['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td class="actions">
                        <a href="detail.php?id=<?php echo urlencode($product['id']); ?>">詳細</a>
                        <a href="edit.php?id=<?php echo urlencode($product['id']); ?>">編集</a>
                        <a href="delete.php?id=<?php echo urlencode($product['id']); ?>" style="color: red;">削除</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <p>全<?php echo count($products); ?>件</p>
    <?php else: ?>
        <div class="no-data">
            <p>商品が登録されていません。</p>
            <p><a href="create.php">最初の商品を登録する</a></p>
        </div>
    <?php endif; ?>
</body>
</html>
```

**解説**：

✅ **セキュリティポイント**

- すべての出力に`htmlspecialchars()`使用
- URLパラメータに`urlencode()`使用

💡 **コードのポイント**

- `number_format()`で価格を3桁区切り表示
- `ORDER BY created_at DESC`で新しい順に表示
- `count($products)`で件数を表示
- 商品が0件の場合の表示
- CSSで見やすいテーブルデザイン

---

### 問題3-3：Read - 商品詳細ページ - 解答例

**解答**：

```php
<?php
// config.phpを読み込み（データベース接続）
require_once 'config.php';

// GETパラメータからIDを取得
$id = $_GET['id'] ?? 0;
$id = (int)$id; // 整数型にキャスト

if ($id <= 0) {
    die("不正なIDです。");
}

try {
    // 商品を取得
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("商品が見つかりませんでした。");
    }

} catch (PDOException $e) {
    die("データベースエラーが発生しました。");
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品詳細</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .detail-card {
            border: 1px solid #ddd;
            padding: 30px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .detail-row {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 18px;
        }
        .button-group {
            margin-top: 30px;
        }
        .btn {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <h1>商品詳細</h1>

    <div class="detail-card">
        <div class="detail-row">
            <div class="detail-label">商品ID</div>
            <div class="detail-value"><?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">商品名</div>
            <div class="detail-value"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">説明</div>
            <div class="detail-value">
                <?php
                $description = $product['description'] ?? '';
                echo nl2br(htmlspecialchars($description, ENT_QUOTES, 'UTF-8'));
                ?>
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">価格</div>
            <div class="detail-value"><?php echo number_format($product['price']); ?>円</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">在庫数</div>
            <div class="detail-value"><?php echo htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8'); ?>個</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">登録日時</div>
            <div class="detail-value"><?php echo htmlspecialchars($product['created_at'], ENT_QUOTES, 'UTF-8'); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">更新日時</div>
            <div class="detail-value"><?php echo htmlspecialchars($product['updated_at'], ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
    </div>

    <div class="button-group">
        <a href="edit.php?id=<?php echo urlencode($product['id']); ?>" class="btn btn-primary">編集</a>
        <a href="list.php" class="btn btn-secondary">一覧に戻る</a>
    </div>
</body>
</html>
```

**解説**：

✅ **セキュリティポイント**

- IDの整数型キャスト
- プリペアドステートメント使用
- すべての出力に`htmlspecialchars()`使用

💡 **コードのポイント**

- `nl2br()`で説明の改行を表示
- 商品が見つからない場合のエラーメッセージ
- 見やすいカードデザイン
- 編集ボタンと戻るボタン

---

## 🚀 応用編

### 問題3-4：Update - 商品編集フォーム - 解答例

**解答**：

```php
<?php
// config.phpを読み込み（データベース接続）
require_once 'config.php';

// GETパラメータからIDを取得
$id = $_GET['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    die("不正なIDです。");
}

$errors = [];
$success = false;

// POSTリクエストの場合（更新処理）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';

    // バリデーション
    if (empty($name)) {
        $errors[] = "商品名は必須です。";
    }

    if (empty($price)) {
        $errors[] = "価格は必須です。";
    } elseif (!is_numeric($price) || $price < 0) {
        $errors[] = "価格は0以上の数値で入力してください。";
    }

    if (!empty($stock) && (!is_numeric($stock) || $stock < 0)) {
        $errors[] = "在庫は0以上の整数で入力してください。";
    }

    // エラーがなければ更新
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                UPDATE products
                SET name = :name, description = :description, price = :price, stock = :stock
                WHERE id = :id
            ");

            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            $rowCount = $stmt->rowCount();

            if ($rowCount > 0) {
                // 更新成功 - 一覧ページへリダイレクト
                header("Location: list.php");
                exit;
            } else {
                $errors[] = "商品情報に変更がありませんでした。";
            }

        } catch (PDOException $e) {
            $errors[] = "データベースエラーが発生しました。";
        }
    }
} else {
    // GETリクエストの場合（初回表示）- 現在の商品情報を取得
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            die("商品が見つかりませんでした。");
        }

        // フォームの初期値として設定
        $name = $product['name'];
        $description = $product['description'];
        $price = $product['price'];
        $stock = $product['stock'];

    } catch (PDOException $e) {
        die("データベースエラーが発生しました。");
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品編集</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>商品編集</h1>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <label>商品名 <span style="color: red;">*</span></label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>説明</label>
        <textarea name="description" rows="5"><?php echo htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>

        <label>価格（円） <span style="color: red;">*</span></label>
        <input type="number" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($price ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>在庫数</label>
        <input type="number" name="stock" min="0" value="<?php echo htmlspecialchars($stock ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        <button type="submit">更新</button>
    </form>

    <p><a href="list.php">一覧に戻る</a></p>
</body>
</html>
```

**解説**：

✅ **セキュリティポイント**

- プリペアドステートメントでUPDATE
- WHERE句でIDを指定（必須！）
- すべての出力に`htmlspecialchars()`使用

💡 **コードのポイント**

- GETリクエストで現在の情報を取得し、フォームに初期値として表示
- POSTリクエストで更新処理
- `rowCount()`で更新件数を確認
- 更新成功時にリダイレクト（PRGパターン）

---

### 問題3-5：Delete - 商品削除機能 - 解答例

**解答**：

```php
<?php
// config.phpを読み込み（データベース接続）
require_once 'config.php';

// セッション開始（CSRFトークン用）
session_start();

// CSRFトークン生成
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// GETパラメータからIDを取得
$id = $_GET['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    die("不正なIDです。");
}

$error = '';
$deleted = false;

// POSTリクエストの場合（削除処理）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRFトークンをチェック
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        die("不正なリクエストです。");
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $rowCount = $stmt->rowCount();

        if ($rowCount > 0) {
            // 削除成功 - 一覧ページへリダイレクト
            header("Location: list.php");
            exit;
        } else {
            $error = "商品が見つかりませんでした。";
        }

    } catch (PDOException $e) {
        $error = "データベースエラーが発生しました。";
    }
}

// 商品情報を取得（確認画面用）
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("商品が見つかりませんでした。");
    }

} catch (PDOException $e) {
    die("データベースエラーが発生しました。");
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品削除確認</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .product-info {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .button-group {
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <h1>商品削除確認</h1>

    <?php if ($error): ?>
        <div class="error">
            <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    <?php endif; ?>

    <div class="warning">
        <strong>⚠️ 警告</strong>
        <p>以下の商品を削除します。この操作は取り消せません。</p>
    </div>

    <div class="product-info">
        <p><strong>商品名:</strong> <?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>価格:</strong> <?php echo number_format($product['price']); ?>円</p>
        <p><strong>在庫:</strong> <?php echo htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8'); ?>個</p>
    </div>

    <div class="button-group">
        <form method="POST" style="display: inline;" onsubmit="return confirm('本当に削除しますか？');">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn btn-danger">削除する</button>
        </form>
        <a href="list.php" class="btn btn-secondary" style="margin-left: 10px;">キャンセル</a>
    </div>
</body>
</html>
```

**解説**：

✅ **セキュリティポイント**

- POSTメソッドで削除（GETは禁止！）
- CSRFトークンで保護
- `hash_equals()`で安全なトークン比較
- WHERE句でIDを指定

💡 **コードのポイント**

- 確認画面を表示してから削除
- JavaScriptの`confirm()`で二重確認
- 削除成功時にリダイレクト

---

### 問題3-6：Update - 在庫数の増減 - 解答例

**解答**：

```php
<?php
// config.phpを読み込み（データベース接続）
require_once 'config.php';

// パラメータを取得
$id = $_POST['id'] ?? 0;
$change = $_POST['change'] ?? 0;

$id = (int)$id;
$change = (int)$change;

if ($id <= 0) {
    die("不正なIDです。");
}

try {
    // トランザクション開始
    $pdo->beginTransaction();

    // 現在の在庫を取得（行ロック）
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id FOR UPDATE");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $currentStock = $stmt->fetchColumn();

    if ($currentStock === false) {
        throw new Exception("商品が見つかりませんでした。");
    }

    // 新しい在庫数を計算
    $newStock = $currentStock + $change;

    // マイナスチェック
    if ($newStock < 0) {
        throw new Exception("在庫が不足しています。（現在: {$currentStock}個、変更: {$change}個）");
    }

    // 在庫を更新
    $stmt = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
    $stmt->bindParam(':stock', $newStock, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // コミット
    $pdo->commit();

    echo "在庫を更新しました。（{$currentStock}個 → {$newStock}個）";

} catch (Exception $e) {
    // ロールバック
    $pdo->rollBack();
    echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**HTMLフォーム例**：

```php
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>在庫管理</title>
</head>
<body>
    <h1>在庫管理</h1>

    <h2>商品ID 1 の在庫を増やす</h2>
    <form method="POST" action="stock_update.php">
        <input type="hidden" name="id" value="1">
        <input type="number" name="change" value="10" placeholder="増減数">
        <button type="submit">在庫を増やす</button>
    </form>

    <h2>商品ID 1 の在庫を減らす</h2>
    <form method="POST" action="stock_update.php">
        <input type="hidden" name="id" value="1">
        <input type="number" name="change" value="-5" placeholder="増減数">
        <button type="submit">在庫を減らす</button>
    </form>
</body>
</html>
```

**解説**：

✅ **セキュリティポイント**

- トランザクション処理で整合性を保証
- `FOR UPDATE`で行ロック（同時アクセス対策）
- マイナスチェック

💡 **コードのポイント**

- `beginTransaction()`、`commit()`、`rollBack()`
- エラー時は自動的にロールバック
- 同時に複数人が在庫を変更しても安全

---

## 🔗 JOIN演習

### 問題3-7：JOINを使った商品とカテゴリーの表示 - 解答例

まず、カテゴリーテーブルと必要なデータを準備します：

```sql
-- カテゴリーテーブルを作成
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 商品テーブルにcategory_idカラムを追加（既存のテーブルの場合）
ALTER TABLE products
ADD COLUMN category_id INT AFTER description,
ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

-- サンプルカテゴリーを挿入
INSERT INTO categories (name, description) VALUES
('電化製品', '家電製品やガジェット'),
('食品', '食べ物・飲み物'),
('書籍', '本・雑誌');

-- 既存の商品にカテゴリーIDを設定
UPDATE products SET category_id = 1 WHERE id = 1;  -- ノートパソコン → 電化製品
UPDATE products SET category_id = 2 WHERE id = 2;  -- 有機コーヒー → 食品
UPDATE products SET category_id = 3 WHERE id = 3;  -- プログラミング入門書 → 書籍
```

**product_list_with_categories.php**：

```php
<?php
// config.phpを読み込み（データベース接続）
require_once 'config.php';

// 商品一覧をカテゴリー名とともに取得（INNER JOIN）
try {
    $stmt = $pdo->query("
        SELECT
            products.id,
            products.name AS product_name,
            products.description,
            products.price,
            products.stock,
            categories.name AS category_name,
            products.created_at
        FROM products
        INNER JOIN categories ON products.category_id = categories.id
        ORDER BY products.created_at DESC
    ");

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品一覧 with カテゴリー</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>📦 商品一覧（カテゴリー表示）</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>商品名</th>
                <th>カテゴリー</th>
                <th>説明</th>
                <th>価格</th>
                <th>在庫</th>
                <th>登録日時</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="7">商品が登録されていません。</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </td>
                    <td><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>¥<?php echo number_format($product['price']); ?></td>
                    <td><?php echo htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8'); ?>個</td>
                    <td><?php echo htmlspecialchars($product['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <p><a href="product_form.php">新しい商品を追加</a></p>
</body>
</html>
```

**✅ セキュリティポイント**：

- ✅ INNER JOINでリレーションを正しく結合
- ✅ `htmlspecialchars()`でXSS対策
- ✅ 外部キー制約でデータ整合性を保証
- ✅ `ON DELETE SET NULL`で削除時の安全性を確保

**💡 コードのポイント**：

- **INNER JOIN**：カテゴリーIDが一致する商品のみ取得
- **AS（エイリアス）**：`products.name AS product_name`でカラム名の衝突を回避
- **ORDER BY**：最新の商品から順に表示
- **外部キー制約**：カテゴリーが削除されたら商品のcategory_idはNULLになる

**🎓 INNER JOIN vs LEFT JOIN**：

```sql
-- INNER JOIN: カテゴリーが設定されている商品のみ取得
SELECT * FROM products
INNER JOIN categories ON products.category_id = categories.id;
-- → category_idがNULLの商品は表示されない

-- LEFT JOIN: カテゴリーが設定されていない商品も取得
SELECT * FROM products
LEFT JOIN categories ON products.category_id = categories.id;
-- → category_idがNULLの商品も表示される（category_nameはNULL）
```

---

### 問題3-8：LEFT JOINでコメント数を表示 - 解答例

まず、レビューテーブルとサンプルデータを準備します：

```sql
-- レビューテーブルを作成
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_name VARCHAR(100) NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- サンプルレビューを挿入
INSERT INTO reviews (product_id, user_name, rating, comment) VALUES
(1, '太郎', 5, '素晴らしい商品です！'),
(1, '花子', 4, '満足しています。'),
(2, '次郎', 5, 'とても良いです。'),
(2, '美咲', 3, '普通です。');
-- 商品ID=3にはレビューなし
```

**product_list_with_review_count.php**：

```php
<?php
// config.phpを読み込み（データベース接続）
require_once 'config.php';

// 商品一覧をレビュー数とともに取得（LEFT JOIN + COUNT）
try {
    $stmt = $pdo->query("
        SELECT
            products.id,
            products.name AS product_name,
            products.price,
            products.stock,
            categories.name AS category_name,
            COUNT(reviews.id) AS review_count,
            AVG(reviews.rating) AS avg_rating
        FROM products
        INNER JOIN categories ON products.category_id = categories.id
        LEFT JOIN reviews ON products.id = reviews.product_id
        GROUP BY products.id
        ORDER BY products.created_at DESC
    ");

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品一覧 with レビュー数</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .rating { color: #FFD700; font-weight: bold; }
        .no-reviews { color: #999; }
    </style>
</head>
<body>
    <h1>📦 商品一覧（レビュー数表示）</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>商品名</th>
                <th>カテゴリー</th>
                <th>価格</th>
                <th>在庫</th>
                <th>レビュー数</th>
                <th>平均評価</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>¥<?php echo number_format($product['price']); ?></td>
                <td><?php echo htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8'); ?>個</td>
                <td>
                    <?php if ($product['review_count'] > 0): ?>
                        <?php echo htmlspecialchars($product['review_count'], ENT_QUOTES, 'UTF-8'); ?>件
                    <?php else: ?>
                        <span class="no-reviews">レビューなし</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($product['avg_rating']): ?>
                        <span class="rating">
                            ⭐ <?php echo number_format($product['avg_rating'], 1); ?>
                        </span>
                    <?php else: ?>
                        <span class="no-reviews">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="product_detail.php?id=1">商品詳細を見る</a></p>
</body>
</html>
```

**✅ セキュリティポイント**：

- ✅ LEFT JOINでレビューがない商品も表示
- ✅ `COUNT()`と`GROUP BY`で正確なカウント
- ✅ `htmlspecialchars()`でXSS対策
- ✅ `number_format()`で安全な数値表示

**💡 コードのポイント**：

- **LEFT JOIN**：レビューがない商品も取得（`review_count`は0になる）
- **COUNT(reviews.id)**：レビューの数をカウント（NULLは除外される）
- **AVG(reviews.rating)**：レビューの平均評価を計算
- **GROUP BY products.id**：商品ごとにグループ化（集約関数を使うため）

**🎓 COUNT()の注意点**：

```sql
-- ❌ 間違い：COUNT(*)だとLEFT JOINで1になる
SELECT products.id, COUNT(*) AS review_count
FROM products
LEFT JOIN reviews ON products.id = reviews.product_id
GROUP BY products.id;
-- → レビューがない商品でもCOUNT(*)は1になる（NULLの行も数える）

-- ✅ 正しい：COUNT(reviews.id)ならNULLは除外される
SELECT products.id, COUNT(reviews.id) AS review_count
FROM products
LEFT JOIN reviews ON products.id = reviews.product_id
GROUP BY products.id;
-- → レビューがない商品はCOUNT(reviews.id)は0になる
```

---

### 問題3-9：3つのテーブルをJOIN - 解答例

**product_detail.php**（商品詳細ページ）：

```php
<?php
// config.phpを読み込み（データベース接続）
require_once 'config.php';

// 商品IDを取得
$id = $_GET['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    die("不正なIDです。");
}

try {
    // 商品情報とカテゴリー、平均評価を取得（3テーブルJOIN）
    $stmt = $pdo->prepare("
        SELECT
            products.id,
            products.name AS product_name,
            products.description,
            products.price,
            products.stock,
            categories.name AS category_name,
            categories.description AS category_description,
            AVG(reviews.rating) AS avg_rating,
            COUNT(reviews.id) AS review_count
        FROM products
        INNER JOIN categories ON products.category_id = categories.id
        LEFT JOIN reviews ON products.id = reviews.product_id
        WHERE products.id = :id
        GROUP BY products.id
    ");

    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("商品が見つかりませんでした。");
    }

    // レビュー一覧を取得
    $stmt = $pdo->prepare("
        SELECT
            user_name,
            rating,
            comment,
            created_at
        FROM reviews
        WHERE product_id = :id
        ORDER BY created_at DESC
    ");

    $stmt->execute([':id' => $id]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?> - 商品詳細</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .product-info { background-color: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .category { display: inline-block; background-color: #4CAF50; color: white; padding: 5px 10px; border-radius: 3px; font-size: 0.9em; }
        .price { font-size: 1.5em; color: #E91E63; font-weight: bold; }
        .rating { color: #FFD700; font-weight: bold; font-size: 1.2em; }
        .reviews { margin-top: 30px; }
        .review-item { border-bottom: 1px solid #ddd; padding: 15px 0; }
        .review-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .stars { color: #FFD700; }
    </style>
</head>
<body>
    <h1>📦 商品詳細</h1>

    <div class="product-info">
        <h2><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></h2>

        <p>
            <span class="category">
                <?php echo htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8'); ?>
            </span>
        </p>

        <p class="price">¥<?php echo number_format($product['price']); ?></p>

        <p><strong>説明：</strong><?php echo nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')); ?></p>

        <p><strong>在庫：</strong><?php echo htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8'); ?>個</p>

        <div>
            <?php if ($product['review_count'] > 0): ?>
                <span class="rating">
                    ⭐ <?php echo number_format($product['avg_rating'], 1); ?>
                </span>
                （<?php echo htmlspecialchars($product['review_count'], ENT_QUOTES, 'UTF-8'); ?>件のレビュー）
            <?php else: ?>
                <span style="color: #999;">まだレビューがありません</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="reviews">
        <h3>💬 レビュー</h3>

        <?php if (empty($reviews)): ?>
            <p>まだレビューが投稿されていません。</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
            <div class="review-item">
                <div class="review-header">
                    <strong><?php echo htmlspecialchars($review['user_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span class="stars">
                        <?php echo str_repeat('⭐', $review['rating']); ?>
                        <?php echo str_repeat('☆', 5 - $review['rating']); ?>
                    </span>
                </div>
                <p><?php echo nl2br(htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8')); ?></p>
                <small style="color: #999;">
                    <?php echo htmlspecialchars($review['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                </small>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <p><a href="product_list_with_review_count.php">← 商品一覧に戻る</a></p>
</body>
</html>
```

**✅ セキュリティポイント**：

- ✅ プリペアドステートメントでSQLインジェクション対策
- ✅ `(int)`キャストで型安全性を確保
- ✅ `htmlspecialchars()`でXSS対策
- ✅ `nl2br()`で改行を安全にHTMLに変換
- ✅ データが見つからない場合の適切なエラー処理

**💡 コードのポイント**：

- **3テーブルJOIN**：products → categories (INNER), products → reviews (LEFT)
- **2回のクエリ**：商品情報とレビュー一覧を別々に取得（効率的）
- **AVG()とCOUNT()**：集約関数で統計情報を計算
- **GROUP BY**：商品ごとにグループ化して集約

**🎓 JOINの順序**：

```sql
-- 商品を起点に、カテゴリーとレビューを結合
FROM products                                      -- 起点
INNER JOIN categories ON products.category_id = categories.id  -- カテゴリーは必須
LEFT JOIN reviews ON products.id = reviews.product_id          -- レビューは任意
```

---

### 問題3-10：カテゴリー別フィルタリング with JOIN - 解答例

**product_list_by_category.php**：

```php
<?php
// config.phpを読み込み（データベース接続）
require_once 'config.php';

// カテゴリーIDを取得
$category_id = $_GET['category_id'] ?? 0;
$category_id = (int)$category_id;

try {
    // カテゴリー一覧を取得
    $categories = $pdo->query("
        SELECT id, name
        FROM categories
        ORDER BY name
    ")->fetchAll(PDO::FETCH_ASSOC);

    // 商品を取得
    if ($category_id > 0) {
        // 特定カテゴリーの商品のみ
        $stmt = $pdo->prepare("
            SELECT
                products.id,
                products.name AS product_name,
                products.description,
                products.price,
                products.stock,
                categories.name AS category_name
            FROM products
            INNER JOIN categories ON products.category_id = categories.id
            WHERE categories.id = :category_id
            ORDER BY products.created_at DESC
        ");
        $stmt->execute([':category_id' => $category_id]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 選択中のカテゴリー名を取得
        $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = :id");
        $stmt->execute([':id' => $category_id]);
        $selected_category_name = $stmt->fetchColumn();

    } else {
        // すべての商品
        $stmt = $pdo->query("
            SELECT
                products.id,
                products.name AS product_name,
                products.description,
                products.price,
                products.stock,
                categories.name AS category_name
            FROM products
            INNER JOIN categories ON products.category_id = categories.id
            ORDER BY products.created_at DESC
        ");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $selected_category_name = 'すべて';
    }

} catch (PDOException $e) {
    die("エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品一覧 - カテゴリー別</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }
        .category-nav { margin-bottom: 20px; padding: 15px; background-color: #f0f0f0; border-radius: 5px; }
        .category-nav a { display: inline-block; margin: 5px; padding: 8px 15px; background-color: #fff; border: 1px solid #ddd; border-radius: 3px; text-decoration: none; color: #333; }
        .category-nav a:hover { background-color: #4CAF50; color: white; }
        .category-nav a.active { background-color: #4CAF50; color: white; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>📦 商品一覧 - カテゴリー別</h1>

    <!-- カテゴリーナビゲーション -->
    <div class="category-nav">
        <strong>カテゴリー：</strong>
        <a href="?category_id=0" class="<?php echo ($category_id === 0) ? 'active' : ''; ?>">
            すべて
        </a>
        <?php foreach ($categories as $category): ?>
        <a href="?category_id=<?php echo htmlspecialchars($category['id'], ENT_QUOTES, 'UTF-8'); ?>"
           class="<?php echo ($category_id === $category['id']) ? 'active' : ''; ?>">
            <?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?>
        </a>
        <?php endforeach; ?>
    </div>

    <h2>
        カテゴリー：<?php echo htmlspecialchars($selected_category_name, ENT_QUOTES, 'UTF-8'); ?>
        （<?php echo count($products); ?>件）
    </h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>商品名</th>
                <th>カテゴリー</th>
                <th>説明</th>
                <th>価格</th>
                <th>在庫</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="7">このカテゴリーには商品がありません。</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>¥<?php echo number_format($product['price']); ?></td>
                    <td><?php echo htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8'); ?>個</td>
                    <td>
                        <a href="product_detail.php?id=<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            詳細
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <p><a href="product_form.php">新しい商品を追加</a></p>
</body>
</html>
```

**✅ セキュリティポイント**：

- ✅ `(int)`キャストでSQLインジェクション対策
- ✅ プリペアドステートメントで安全なクエリ実行
- ✅ `htmlspecialchars()`でXSS対策
- ✅ URLパラメータの適切な検証

**💡 コードのポイント**：

- **条件分岐**：category_idが0ならすべて、それ以外は特定カテゴリー
- **動的クラス**：選択中のカテゴリーに`active`クラスを付与
- **count()**：PHP関数で商品数を表示
- **リンク生成**：GETパラメータでカテゴリーを切り替え

**🎓 改善版（クエリを1つにまとめる）**：

```php
// WHERE句を動的に構築する方法
$where = "";
$params = [];

if ($category_id > 0) {
    $where = "WHERE categories.id = :category_id";
    $params[':category_id'] = $category_id;
}

$stmt = $pdo->prepare("
    SELECT
        products.id,
        products.name AS product_name,
        products.price,
        categories.name AS category_name
    FROM products
    INNER JOIN categories ON products.category_id = categories.id
    $where
    ORDER BY products.created_at DESC
");

$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

---

## 🛡️ セキュリティチャレンジ

### 問題3-11：権限チェックの実装 - 解答例

**functions.php（共通関数）**：

```php
<?php
/**
 * ログインチェック関数
 */
function requireLogin() {
    // セッション開始（まだ開始していない場合）
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // ログインチェック
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    // セッションハイジャック対策（一定時間ごとにID再生成）
    if (!isset($_SESSION['last_regenerate'])) {
        $_SESSION['last_regenerate'] = time();
        session_regenerate_id(true);
    }

    // 30分ごとにセッションIDを再生成
    if (time() - $_SESSION['last_regenerate'] > 1800) {
        $_SESSION['last_regenerate'] = time();
        session_regenerate_id(true);
    }
}
?>
```

**create.php（先頭に追加）**：

```php
<?php
require_once 'functions.php';
requireLogin(); // ログインチェック

require_once 'config.php';

// 以下、既存のコード...
?>
```

**login.php（簡易ログインページ）**：

```php
<?php
session_start();

// すでにログイン済みの場合はリダイレクト
if (isset($_SESSION['user_id'])) {
    header("Location: list.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // ここでは簡易的な認証（実際はデータベースで確認）
    if ($username === 'admin' && $password === 'password') {
        // セッションハイジャック対策
        session_regenerate_id(true);

        // ログイン成功
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $username;
        $_SESSION['last_regenerate'] = time();

        header("Location: list.php");
        exit;
    } else {
        $error = "ユーザー名またはパスワードが間違っています。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
</head>
<body>
    <h1>ログイン</h1>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>ユーザー名: <input type="text" name="username" required></label><br>
        <label>パスワード: <input type="password" name="password" required></label><br>
        <button type="submit">ログイン</button>
    </form>
</body>
</html>
```

**解説**：

✅ **セキュリティポイント**

- セッションでログイン状態を管理
- `session_regenerate_id(true)`でセッションハイジャック対策
- 定期的にセッションIDを再生成（30分ごと）

---

### 問題3-12：SQLインジェクション攻撃からの防御 - 解答例

**脆弱なコード（再掲）**：

```php
<?php
// 🚨 危険！SQLインジェクション脆弱性あり
$id = $_GET['id'];
$sql = "DELETE FROM products WHERE id = $id";
$pdo->exec($sql);
echo "削除しました。";
?>
```

**修正版（セキュア）**：

```php
<?php
require_once 'config.php';

// POSTメソッドに変更
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("不正なリクエストです。");
}

// IDを取得して整数型にキャスト
$id = $_POST['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    die("不正なIDです。");
}

try {
    // プリペアドステートメントで削除
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // 削除件数を確認
    $rowCount = $stmt->rowCount();

    if ($rowCount > 0) {
        echo "削除しました。";
    } else {
        echo "商品が見つかりませんでした。";
    }

} catch (PDOException $e) {
    echo "エラーが発生しました。";
    // 本番環境ではログに記録
    // error_log($e->getMessage());
}
?>
```

**解説**：

✅ **修正のポイント**

1. GETからPOSTに変更
2. プリペアドステートメント使用
3. 整数型キャスト
4. `rowCount()`で削除件数確認
5. try-catchでエラーハンドリング

---

## 💪 総合チャレンジ - 完全な商品管理システムの構成例

ファイル構成と各ファイルのポイントを示します。

### config.php

```php
<?php
define('DB_HOST', 'localhost');
define('DB_PORT', '8889');
define('DB_NAME', 'phase5_practice');
define('DB_USER', 'root');
define('DB_PASS', 'root');

$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die("データベース接続エラー");
}
?>
```

### functions.php

```php
<?php
/**
 * XSS対策関数
 */
function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * ログインチェック関数
 */
function requireLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

/**
 * CSRFトークン生成
 */
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRFトークン検証
 */
function verifyCsrfToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
```

---

## 🎓 まとめ

### 学んだこと

✅ **CRUD操作のすべて**

- Create: INSERT + `lastInsertId()`
- Read: SELECT + `fetch()`/`fetchAll()`
- Update: UPDATE + `rowCount()` + リダイレクト
- Delete: DELETE + 確認画面 + CSRF対策

✅ **セキュリティ対策**

- プリペアドステートメント（SQLインジェクション対策）
- `htmlspecialchars()`（XSS対策）
- CSRFトークン（CSRF対策）
- トランザクション処理（データ整合性）
- セッション管理（認証・認可）

✅ **実践的なテクニック**

- PRGパターン（Post/Redirect/Get）
- フォームの初期値設定
- バリデーション
- エラーハンドリング

### 次のステップ

👉 **[Lesson 04: セキュリティ対策](../../04-security/README.md)**に進んで、さらに高度なセキュリティ対策を学ぼう！

---

**Let's vibe and code! 🎉**

CRUD操作とセキュリティ対策をマスターしたね！実用的なアプリケーションが作れるようになったよ！
