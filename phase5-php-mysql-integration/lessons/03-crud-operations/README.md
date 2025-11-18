# Lesson 03: CRUD操作の実装 📝

**学習目標**：Create、Read、Update、Deleteの完全なCRUD機能をプリペアドステートメントで実装し、セキュアな商品管理システムを構築できるようになる！

---

## 📖 このレッスンで学ぶこと

- **CRUDとは何か**（Create、Read、Update、Delete）
- 各CRUD操作のSQL文とPHPコード
- プリペアドステートメントを使った安全な実装
- **lastInsertId()** でINSERT後のIDを取得
- **rowCount()** でUPDATE/DELETE後の影響行数を取得
- バリデーション（入力チェック）の実装
- エラーハンドリングとユーザーへのフィードバック
- リダイレクトとメッセージ表示（PRGパターン）
- 実用的な商品管理システムの構築

---

## 🎯 なぜCRUD操作を学ぶの？（Why）

### CRUDはすべてのWebアプリケーションの基礎！

**CRUD = アプリケーションの4つの基本機能**

ほとんどすべてのWebアプリケーションは、データベースに対して以下の4つの操作を行う：

```text
C - Create  （作成）：新しいデータを追加
R - Read    （読み取り）：データを取得・表示
U - Update  （更新）：既存データを編集
D - Delete  （削除）：データを削除
```

**身近な例**：

| アプリケーション | Create | Read | Update | Delete |
|---------------|--------|------|--------|--------|
| **SNS** | 投稿を作成 | タイムラインを表示 | 投稿を編集 | 投稿を削除 |
| **ECサイト** | 商品を登録 | 商品一覧を表示 | 商品情報を更新 | 商品を削除 |
| **ブログ** | 記事を書く | 記事を読む | 記事を修正 | 記事を削除 |
| **タスク管理** | タスク追加 | タスク一覧 | タスク編集 | タスク完了 |

### CRUDができるようになると、こんなアプリが作れる！

✅ **ブログシステム**
- 記事の投稿（Create）
- 記事一覧・詳細表示（Read）
- 記事の編集（Update）
- 記事の削除（Delete）

✅ **商品管理システム**
- 商品登録（Create）
- 商品一覧・検索（Read）
- 在庫・価格変更（Update）
- 商品削除（Delete）

✅ **会員管理システム**
- 会員登録（Create）
- 会員情報照会（Read）
- プロフィール更新（Update）
- 退会処理（Delete）

### バックエンド開発における重要性

**CRUDはデータベース操作の基本**！

- 📊 **データの永続化**：ユーザーが入力したデータを保存
- 🔒 **セキュリティ**：プリペアドステートメントで安全に操作
- 🎯 **ユーザビリティ**：わかりやすいフィードバック
- 🛠️ **保守性**：統一されたパターンで実装

**CRUDをマスターすれば、ほとんどすべてのWebアプリケーションが作れる！**

---

## 🏗️ CRUDの基礎知識（What）

### CRUDの4つの操作とSQL文

| 操作 | 意味 | SQL文 | HTTPメソッド |
|-----|------|-------|-------------|
| **Create** | データを追加 | INSERT | POST |
| **Read** | データを取得 | SELECT | GET |
| **Update** | データを更新 | UPDATE | POST/PUT |
| **Delete** | データを削除 | DELETE | POST/DELETE |

### 実装の流れ（標準パターン）

**すべてのCRUD操作は同じパターンで実装できる！**

```text
1. データベース接続（config.phpを読み込み）
   ↓
2. ユーザー入力を受け取る（$_POST、$_GET）
   ↓
3. バリデーション（入力チェック）
   ↓
4. プリペアドステートメントでSQL実行
   ↓
5. 結果を確認（成功/失敗）
   ↓
6. ユーザーにフィードバック（メッセージ表示）
```

### セキュリティの3つの柱

**CRUD操作で必ず守るべきセキュリティ原則**：

🔒 **1. SQLインジェクション対策**
- プリペアドステートメントを**必ず**使う
- ユーザー入力を直接SQL文に埋め込まない

🔒 **2. XSS対策**
- 出力時に `htmlspecialchars()` を使う
- ユーザー入力をそのまま表示しない

🔒 **3. バリデーション**
- 入力値をチェック（空、型、範囲）
- サーバー側で必ずチェック（JavaScriptだけでは不十分）

---

## 💻 Create（作成）の実装（How）

### Createとは？

**Create = 新しいデータをデータベースに追加する操作**

- **SQL文**：`INSERT INTO`
- **使用場面**：ユーザー登録、商品登録、記事投稿など

### 基本的なCreateの流れ

```php
<?php
// 1. データベース接続
require_once 'config.php';

// 2. POSTデータを受け取る
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // 3. バリデーション
    $errors = [];
    if (empty($name)) {
        $errors[] = "名前は必須です。";
    }
    if (empty($email)) {
        $errors[] = "メールアドレスは必須です。";
    }

    // 4. エラーがなければINSERT
    if (empty($errors)) {
        try {
            // プリペアドステートメントでINSERT
            $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email
            ]);

            // 5. 登録されたIDを取得
            $lastId = $pdo->lastInsertId();

            echo "登録成功！ ID: " . htmlspecialchars($lastId, ENT_QUOTES, 'UTF-8');

        } catch (PDOException $e) {
            echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    } else {
        // エラー表示
        foreach ($errors as $error) {
            echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "<br>";
        }
    }
}
?>

<!-- 6. HTMLフォーム -->
<form method="POST" action="">
    <label>名前: <input type="text" name="name" required></label><br>
    <label>メール: <input type="email" name="email" required></label><br>
    <button type="submit">登録</button>
</form>
```

### lastInsertId()の使い方

**lastInsertId()**は、最後にINSERTされたレコードのAUTO_INCREMENT IDを取得する！

```php
<?php
// 商品を登録
$stmt = $pdo->prepare("INSERT INTO products (name, price) VALUES (:name, :price)");
$stmt->execute([
    ':name' => 'MacBook Pro',
    ':price' => 198000
]);

// 登録された商品のIDを取得
$productId = $pdo->lastInsertId();

echo "商品ID {$productId} で登録されました！";
// 出力例: 商品ID 42 で登録されました！
?>
```

**よくある使い方**：

```php
<?php
// 登録後、詳細ページにリダイレクト
$productId = $pdo->lastInsertId();
header("Location: product_detail.php?id={$productId}");
exit;
?>
```

### バリデーションの実装パターン

**サーバー側のバリデーションは必須！**

```php
<?php
$errors = [];

// 空チェック
if (empty($name)) {
    $errors[] = "商品名は必須です。";
}

// 文字数チェック
if (mb_strlen($name) > 255) {
    $errors[] = "商品名は255文字以内で入力してください。";
}

// 数値チェック
if (!is_numeric($price)) {
    $errors[] = "価格は数値で入力してください。";
}

// 範囲チェック
if ($price < 0 || $price > 10000000) {
    $errors[] = "価格は0〜10,000,000円の範囲で入力してください。";
}

// 整数チェック
if (!filter_var($stock, FILTER_VALIDATE_INT)) {
    $errors[] = "在庫は整数で入力してください。";
}

// エラーがなければ処理を続行
if (empty($errors)) {
    // INSERT処理
} else {
    // エラー表示
    foreach ($errors as $error) {
        echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "<br>";
    }
}
?>
```

---

## 💻 Read（読み取り）の実装（How）

### Readとは？

**Read = データベースからデータを取得して表示する操作**

- **SQL文**：`SELECT`
- **使用場面**：一覧表示、詳細表示、検索など

### Read操作の2つのパターン

**1. 複数件取得（一覧表示）**
- `fetchAll()` を使う
- 全商品、全記事、全ユーザーなど

**2. 1件取得（詳細表示）**
- `fetch()` を使う
- 特定の商品、特定の記事、特定のユーザー

### パターン1: 一覧表示（fetchAll）

```php
<?php
require_once 'config.php';

try {
    // すべての商品を取得
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品一覧</title>
</head>
<body>
    <h1>商品一覧</h1>

    <?php if (count($products) > 0): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>商品名</th>
                <th>価格</th>
                <th>在庫</th>
            </tr>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo number_format($product['price']); ?>円</td>
                    <td><?php echo htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>商品がまだ登録されていません。</p>
    <?php endif; ?>
</body>
</html>
```

### パターン2: 詳細表示（fetch）

```php
<?php
require_once 'config.php';

// URLパラメータからIDを取得
$id = $_GET['id'] ?? 0;

try {
    // 特定の商品を取得（プリペアドステートメント）
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // 商品が見つからない場合
    if (!$product) {
        die("商品が見つかりません。");
    }

} catch (PDOException $e) {
    die("エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
    <p>価格: <?php echo number_format($product['price']); ?>円</p>
    <p>在庫: <?php echo htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8'); ?>個</p>
    <p><?php echo nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')); ?></p>
</body>
</html>
```

### 検索機能の実装

```php
<?php
require_once 'config.php';

// 検索キーワードを取得
$keyword = $_GET['keyword'] ?? '';

try {
    if (!empty($keyword)) {
        // キーワードで検索（LIKE）
        $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :keyword OR description LIKE :keyword");
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
    } else {
        // すべて取得
        $stmt = $pdo->query("SELECT * FROM products");
    }

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>

<!-- 検索フォーム -->
<form method="GET" action="">
    <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>" placeholder="商品名で検索">
    <button type="submit">検索</button>
</form>

<!-- 検索結果 -->
<p>検索結果: <?php echo count($products); ?>件</p>
```

---

## 💻 Update（更新）の実装（How）

### Updateとは？

**Update = 既存データを変更する操作**

- **SQL文**：`UPDATE`
- **使用場面**：プロフィール編集、商品情報変更、記事修正など

### Updateの2つのステップ

```text
ステップ1（GET）：編集フォームを表示
  ↓ 既存データをSELECTして、フォームに表示

ステップ2（POST）：編集内容を保存
  ↓ フォームから送信されたデータでUPDATE
```

### Update実装例（商品編集）

```php
<?php
require_once 'config.php';

// 商品IDを取得
$id = $_GET['id'] ?? 0;

// ステップ2: POST送信（編集保存）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // バリデーション
    $errors = [];
    if (empty($name)) {
        $errors[] = "商品名は必須です。";
    }
    if (!is_numeric($price) || $price < 0) {
        $errors[] = "価格は0以上の数値で入力してください。";
    }

    // エラーがなければUPDATE
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = :name, price = :price, stock = :stock WHERE id = :id");
            $stmt->execute([
                ':name' => $name,
                ':price' => $price,
                ':stock' => $stock,
                ':id' => $id
            ]);

            // 更新された行数を確認
            $affectedRows = $stmt->rowCount();

            if ($affectedRows > 0) {
                echo "商品を更新しました！";
            } else {
                echo "更新する商品が見つかりませんでした。";
            }

        } catch (PDOException $e) {
            echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    } else {
        foreach ($errors as $error) {
            echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "<br>";
        }
    }
}

// ステップ1: GET（編集フォーム表示）
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("商品が見つかりません。");
    }

} catch (PDOException $e) {
    die("エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>

<!-- 編集フォーム（既存データを表示） -->
<h1>商品編集</h1>
<form method="POST" action="">
    <label>商品名: <input type="text" name="name" value="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" required></label><br>
    <label>価格: <input type="number" name="price" value="<?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?>" required></label><br>
    <label>在庫: <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock'], ENT_QUOTES, 'UTF-8'); ?>"></label><br>
    <button type="submit">更新</button>
</form>
```

### rowCount()の使い方

**rowCount()**は、UPDATE/DELETEで影響を受けた行数を取得する！

```php
<?php
// 商品を更新
$stmt = $pdo->prepare("UPDATE products SET price = :price WHERE id = :id");
$stmt->execute([
    ':price' => 150000,
    ':id' => 42
]);

// 更新された行数を確認
$affectedRows = $stmt->rowCount();

if ($affectedRows > 0) {
    echo "{$affectedRows}件の商品を更新しました！";
} else {
    echo "更新する商品が見つかりませんでした。";
}
?>
```

**注意点**：

- `rowCount()`は実際に**変更された行数**を返す
- 同じ値で更新した場合、`rowCount()`は0を返すことがある
- SELECTには使えない（`fetchAll()`の結果をカウントする）

---

## 💻 Delete（削除）の実装（How）

### Deleteとは？

**Delete = データを削除する操作**

- **SQL文**：`DELETE`
- **使用場面**：商品削除、記事削除、退会処理など

### Delete実装例（確認付き削除）

```php
<?php
require_once 'config.php';

// 商品IDを取得
$id = $_GET['id'] ?? 0;

// ステップ2: POST送信（削除実行）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 削除実行
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $deletedRows = $stmt->rowCount();

        if ($deletedRows > 0) {
            echo "商品を削除しました！";
            echo '<a href="product_list.php">商品一覧に戻る</a>';
        } else {
            echo "削除する商品が見つかりませんでした。";
        }

    } catch (PDOException $e) {
        echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
    exit;
}

// ステップ1: GET（削除確認画面）
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("商品が見つかりません。");
    }

} catch (PDOException $e) {
    die("エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>

<!-- 削除確認画面 -->
<h1>商品削除の確認</h1>
<p>以下の商品を削除してもよろしいですか？</p>

<table border="1">
    <tr>
        <th>商品名</th>
        <td><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <tr>
        <th>価格</th>
        <td><?php echo number_format($product['price']); ?>円</td>
    </tr>
</table>

<form method="POST" action="">
    <button type="submit" onclick="return confirm('本当に削除しますか？');">削除する</button>
    <a href="product_list.php">キャンセル</a>
</form>
```

### 削除のセキュリティ対策

**誤削除を防ぐ3つの対策**：

**1. 確認画面を表示**

```php
// GETリクエスト → 確認画面
// POSTリクエスト → 削除実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 削除処理
} else {
    // 確認画面表示
}
```

**2. JavaScript確認ダイアログ**

```html
<button type="submit" onclick="return confirm('本当に削除しますか？');">削除する</button>
```

**3. 論理削除（ソフトデリート）**

物理的に削除せず、`deleted`フラグを立てる：

```php
<?php
// 論理削除（物理削除しない）
$stmt = $pdo->prepare("UPDATE products SET deleted = 1, deleted_at = NOW() WHERE id = :id");
$stmt->execute([':id' => $id]);

// 一覧表示時は削除されていないものだけ取得
$stmt = $pdo->query("SELECT * FROM products WHERE deleted = 0");
$products = $stmt->fetchAll();
?>
```

---

## 🔄 PRGパターン（Post-Redirect-Get）

### PRGパターンとは？

**PRG = Post後にRedirectしてGetで表示するパターン**

**問題**：POST後にそのまま表示すると、**ブラウザの更新ボタンで重複送信される**！

```text
悪い例：POST → 直接結果表示
  ↓ ブラウザ更新（F5）
  ↓ 「フォームを再送信しますか？」
  ↓ 同じデータが再度INSERT → 重複登録！
```

**解決策**：POST後にリダイレクト（PRGパターン）

```text
良い例：POST → リダイレクト → GET
  ↓ ブラウザ更新（F5）
  ↓ GETリクエストが再送信される
  ↓ 安全！（重複登録されない）
```

### PRGパターンの実装

```php
<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];

    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, price) VALUES (:name, :price)");
        $stmt->execute([':name' => $name, ':price' => $price]);

        // セッションにメッセージを保存
        $_SESSION['message'] = "商品を登録しました！";

        // リダイレクト（PRGパターン）
        header("Location: product_list.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "エラー: " . $e->getMessage();
        header("Location: product_create.php");
        exit;
    }
}
?>
```

**リダイレクト先でメッセージを表示**：

```php
<?php
session_start();

// メッセージがあれば表示して削除
if (isset($_SESSION['message'])) {
    echo '<p style="color: green;">' . htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') . '</p>';
    unset($_SESSION['message']);
}

if (isset($_SESSION['error'])) {
    echo '<p style="color: red;">' . htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') . '</p>';
    unset($_SESSION['error']);
}
?>
```

---

## 🤖 バイブコーディング実践

### AIへの指示例

**良い指示の例**：

```text
✅ 具体的でセキュアな指示：

「商品管理システムのCRUD機能を作成してください。

テーブル構成：
- products (id, name, description, price, stock, created_at, updated_at)

要件：
1. Create: 商品登録フォーム（name, description, price, stockを入力）
2. Read: 商品一覧表示（テーブル形式、価格は3桁区切り）
3. Update: 商品編集フォーム（既存データを表示、更新）
4. Delete: 削除確認画面付き削除機能

セキュリティ要件：
- すべてプリペアドステートメントを使用
- XSS対策（htmlspecialchars）
- バリデーション（空チェック、数値チェック）
- PRGパターンでリダイレクト

技術要件：
- PDOを使用
- lastInsertId()でINSERT後のIDを取得
- rowCount()でUPDATE/DELETE後の行数を確認
- セッションでメッセージを表示」
```

```text
❌ 曖昧な指示（セキュリティリスク）：

「商品の登録、表示、編集、削除ができるPHPコードを書いて」

問題点：
- セキュリティ要件がない → SQLインジェクション脆弱性
- 具体的な機能が不明 → 想定と違うコードが生成される
- バリデーションの指示なし → エラーハンドリングがない
```

### 生成されたコードのチェックポイント

✅ **セキュリティチェック（最優先）**

**SQLインジェクション対策**：
- [ ] すべてのSQL文でプリペアドステートメント（`prepare()` → `execute()`）を使っているか
- [ ] ユーザー入力を直接SQL文に埋め込んでいないか（`"INSERT INTO ... VALUES ('$name')"`は危険）
- [ ] プレースホルダー（`?`または`:name`）を使っているか

**XSS対策**：
- [ ] 出力時に `htmlspecialchars()` を使っているか
- [ ] `htmlspecialchars()` に `ENT_QUOTES, 'UTF-8'` を指定しているか
- [ ] ユーザー入力をそのまま `echo` していないか

**バリデーション**：
- [ ] サーバー側でバリデーションしているか（JavaScriptだけでは不十分）
- [ ] 空チェック（`empty()`）があるか
- [ ] 型チェック（`is_numeric()`、`filter_var()`）があるか

✅ **機能チェック**

**Create（INSERT）**：
- [ ] `lastInsertId()` で登録IDを取得しているか
- [ ] エラーハンドリング（`try-catch`）があるか
- [ ] 登録成功時にメッセージを表示しているか

**Read（SELECT）**：
- [ ] 一覧表示で `fetchAll()` を使っているか
- [ ] 詳細表示で `fetch()` を使っているか
- [ ] データが0件の場合の処理があるか

**Update（UPDATE）**：
- [ ] 編集フォームに既存データを表示しているか
- [ ] `rowCount()` で更新行数を確認しているか
- [ ] 更新成功時にメッセージを表示しているか

**Delete（DELETE）**：
- [ ] 削除確認画面があるか
- [ ] `rowCount()` で削除行数を確認しているか
- [ ] 誤削除防止策（確認ダイアログなど）があるか

**PRGパターン**：
- [ ] POST後にリダイレクト（`header("Location: ...")`）しているか
- [ ] セッションでメッセージを渡しているか
- [ ] リダイレクト後に `exit` しているか

✅ **コード品質チェック**

- [ ] `config.php` でデータベース接続を分離しているか
- [ ] 変数名がわかりやすいか（`$stmt`, `$product`, `$errors`など）
- [ ] 重複コードがないか（関数化できるか）
- [ ] エラーメッセージがユーザーフレンドリーか

### よくあるAI生成コードの問題と修正

**問題1: SQLインジェクション脆弱性**

```php
// ❌ 悪い例（AIが生成しがちな危険なコード）
$id = $_GET['id'];
$sql = "DELETE FROM products WHERE id = $id";
$pdo->exec($sql);
```

**原因**：ユーザー入力を直接SQL文に埋め込んでいる

**修正**：

```php
// ✅ 良い例（プリペアドステートメント）
$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
```

**AIへの修正指示**：
「SQLインジェクション対策のため、すべてのSQL文をプリペアドステートメントに書き換えてください」

---

**問題2: XSS脆弱性**

```php
// ❌ 悪い例（XSS脆弱性）
<td><?php echo $product['name']; ?></td>
```

**原因**：ユーザー入力をそのまま出力している

**攻撃例**：
```text
商品名に "<script>alert('XSS')</script>" を登録
→ 一覧表示時にJavaScriptが実行される
```

**修正**：

```php
// ✅ 良い例（htmlspecialchars）
<td><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
```

**AIへの修正指示**：
「XSS対策のため、すべての出力にhtmlspecialchars()を追加してください」

---

**問題3: バリデーションがない**

```php
// ❌ 悪い例（バリデーションなし）
$name = $_POST['name'];
$price = $_POST['price'];

$stmt = $pdo->prepare("INSERT INTO products (name, price) VALUES (:name, :price)");
$stmt->execute([':name' => $name, ':price' => $price]);
```

**問題点**：
- 空の値でも登録されてしまう
- 価格に文字列が入力されてもエラーにならない

**修正**：

```php
// ✅ 良い例（バリデーション付き）
$name = $_POST['name'];
$price = $_POST['price'];

$errors = [];
if (empty($name)) {
    $errors[] = "商品名は必須です。";
}
if (!is_numeric($price) || $price < 0) {
    $errors[] = "価格は0以上の数値で入力してください。";
}

if (empty($errors)) {
    $stmt = $pdo->prepare("INSERT INTO products (name, price) VALUES (:name, :price)");
    $stmt->execute([':name' => $name, ':price' => $price]);
} else {
    foreach ($errors as $error) {
        echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "<br>";
    }
}
```

**AIへの修正指示**：
「入力値のバリデーションを追加してください。空チェック、数値チェック、範囲チェックを含めてください」

---

**問題4: PRGパターンがない**

```php
// ❌ 悪い例（POST後に直接表示）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // INSERT処理
    echo "登録しました！";
}
?>
<form method="POST">...</form>
```

**問題点**：ブラウザ更新（F5）で重複登録される

**修正**：

```php
// ✅ 良い例（PRGパターン）
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // INSERT処理
    $_SESSION['message'] = "登録しました！";
    header("Location: product_list.php");
    exit;
}
```

**AIへの修正指示**：
「POST-Redirect-Getパターンを実装してください。POST後にリダイレクトし、セッションでメッセージを渡してください」

---

## 💡 よくあるエラーと解決方法

### エラー1: "Call to a member function execute() on bool"

**原因**：`prepare()`が失敗している（SQL文の構文エラー）

```php
// ❌ SQL文にエラーがある
$stmt = $pdo->prepare("SELEC * FROM products");  // "SELECT"のスペルミス
$stmt->execute();  // エラー！
```

**解決**：

```php
// ✅ SQL文を確認
$stmt = $pdo->prepare("SELECT * FROM products");
$stmt->execute();
```

**デバッグ方法**：

```php
<?php
$stmt = $pdo->prepare("SELEC * FROM products");

if ($stmt === false) {
    echo "SQLエラー: ";
    print_r($pdo->errorInfo());
}
?>
```

---

### エラー2: "Headers already sent"

**原因**：`header()`の前に出力がある

```php
// ❌ 悪い例
echo "処理中...";
header("Location: product_list.php");  // エラー！
```

**解決**：

```php
// ✅ 良い例（header()の前に何も出力しない）
header("Location: product_list.php");
exit;
```

**注意点**：
- PHPファイルの先頭に空白や改行があってもエラーになる
- `<?php` の前に何も書かない

---

### エラー3: "Undefined array key 'name'"

**原因**：配列のキーが存在しない

```php
// ❌ POSTデータが送信されていないのにアクセス
$name = $_POST['name'];  // エラー！
```

**解決**：

```php
// ✅ 良い例（存在チェック）
$name = $_POST['name'] ?? '';

// または
if (isset($_POST['name'])) {
    $name = $_POST['name'];
} else {
    $name = '';
}
```

---

## 🎓 まとめ

このレッスンで学んだこと：

✅ **CRUDの基礎**
- Create（INSERT）、Read（SELECT）、Update（UPDATE）、Delete（DELETE）
- CRUD = すべてのWebアプリケーションの基本操作
- 実用的な商品管理システムの実装方法

✅ **各CRUD操作の実装**
- **Create**: `lastInsertId()`で登録IDを取得
- **Read**: `fetchAll()`（一覧）、`fetch()`（詳細）
- **Update**: 編集フォーム表示 → 更新実行、`rowCount()`で行数確認
- **Delete**: 確認画面 → 削除実行、誤削除防止策

✅ **セキュリティ対策**
- SQLインジェクション対策：プリペアドステートメント
- XSS対策：`htmlspecialchars()`
- バリデーション：空チェック、型チェック、範囲チェック

✅ **ベストプラクティス**
- PRGパターン（Post-Redirect-Get）で重複送信を防ぐ
- セッションでメッセージを表示
- `try-catch`でエラーハンドリング
- 論理削除（ソフトデリート）で誤削除を防ぐ

✅ **バイブコーディング**
- AIに具体的でセキュアな指示を出す方法
- 生成されたコードのセキュリティチェックポイント
- よくある脆弱性の発見と修正方法

### 次のステップ

CRUDができるようになったら、次は**セキュリティ**をさらに深く学ぼう！

👉 **[Lesson 04: セキュリティベストプラクティス](../04-security/README.md)**

CSRF対策、パスワードハッシュ化、セッション管理など、より高度なセキュリティ技術をマスターしよう！

👉 **[演習問題を見る](exercises/README.md)**

実際に商品管理システムを作って、CRUD操作をマスターしよう！

---

**Let's vibe and code! 🎉**

CRUDができれば、ほとんどすべてのWebアプリケーションが作れる！次はセキュリティを深掘りして、本番環境で使えるスキルを身につけよう！
