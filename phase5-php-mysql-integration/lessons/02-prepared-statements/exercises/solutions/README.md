# Lesson 02: プリペアドステートメント - 解答例 🔒

各問題の解答例と解説を示します。コードをコピーして試してみよう！

---

## 🌱 基礎編

### 問題2-1：基本的なSELECT - 解答例

**解答**：

```php
<?php
// データベース接続
require_once 'config.php'; // Phase 5 Lesson 01で作成した接続設定

// 検索するユーザーID
$id = 1; // 実際にはフォームから受け取る

try {
    // プリペアドステートメントを準備
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");

    // パラメータをバインド（整数型として）
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // 実行
    $stmt->execute();

    // データを1件取得
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 結果を表示
    if ($user) {
        echo "ユーザーが見つかりました！<br>";
        echo "名前: " . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "<br>";
        echo "メール: " . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . "<br>";
    } else {
        echo "ユーザーが見つかりませんでした。";
    }

} catch (PDOException $e) {
    echo "エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**解説**：

✅ **セキュリティポイント**
- `prepare()`で SQL を準備 → SQLインジェクション対策
- `bindParam()`で型を指定（`PDO::PARAM_INT`） → 整数しか受け付けない
- `htmlspecialchars()`でXSS対策 → 出力時に必ず使う

💡 **コードのポイント**
- `fetch()`は1件だけ取得するメソッド
- データがない場合は`false`が返る
- `PDO::FETCH_ASSOC`で連想配列として取得

---

### 問題2-2：INSERT with プリペアドステートメント - 解答例

**解答**：

```php
<?php
// データベース接続
require_once 'config.php';

// フォームからデータを受け取る（POST送信を想定）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';

    // 簡単なバリデーション
    if (empty($name) || empty($email)) {
        die("名前とメールアドレスは必須です。");
    }

    try {
        // プリペアドステートメントを準備
        $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");

        // パラメータをバインド
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        // 実行
        $stmt->execute();

        // 挿入したIDを取得
        $lastId = $pdo->lastInsertId();

        echo "ユーザー登録が完了しました！<br>";
        echo "登録ID: " . htmlspecialchars($lastId, ENT_QUOTES, 'UTF-8');

    } catch (PDOException $e) {
        // メールアドレスの重複エラー（UNIQUE制約）の場合
        if ($e->getCode() == 23000) {
            echo "このメールアドレスは既に登録されています。";
        } else {
            echo "エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
</head>
<body>
    <h1>ユーザー登録フォーム</h1>
    <form method="POST">
        <label>名前: <input type="text" name="name" required></label><br>
        <label>メールアドレス: <input type="email" name="email" required></label><br>
        <button type="submit">登録</button>
    </form>
</body>
</html>
```

**解説**：

✅ **セキュリティポイント**
- プリペアドステートメントでINSERT → SQLインジェクション対策
- `PDO::PARAM_STR`で文字列型を指定
- バリデーションで空チェック

💡 **コードのポイント**
- `lastInsertId()`でAUTO_INCREMENTの値を取得
- UNIQUE制約違反は`getCode() == 23000`でキャッチ
- フォームとPHP処理を1ファイルにまとめた

---

### 問題2-3：複数件取得（fetchAll）- 解答例

**解答**：

```php
<?php
// データベース接続
require_once 'config.php';

// 検索するドメイン
$domain = '%@example.com'; // LIKE句で使うパターン

try {
    // プリペアドステートメントを準備
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email LIKE :domain");

    // パラメータをバインド
    $stmt->bindParam(':domain', $domain, PDO::PARAM_STR);

    // 実行
    $stmt->execute();

    // 全件取得
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 結果を表示
    if (count($users) > 0) {
        echo "<h2>検索結果: " . count($users) . "件</h2>";
        echo "<ul>";
        foreach ($users as $user) {
            echo "<li>";
            echo "名前: " . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . " / ";
            echo "メール: " . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "該当するユーザーが見つかりませんでした。";
    }

} catch (PDOException $e) {
    echo "エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**解説**：

✅ **セキュリティポイント**
- LIKE句でもプリペアドステートメント使用
- `htmlspecialchars()`でXSS対策（各ユーザーに対して）

💡 **コードのポイント**
- `fetchAll()`で全件を配列で取得
- `%`はワイルドカード（「example.comで終わる」を検索）
- `count($users)`で件数を確認
- `foreach`で一覧表示

---

## 🚀 応用編

### 問題2-4：UPDATE with プリペアドステートメント - 解答例

**解答**：

```php
<?php
// データベース接続
require_once 'config.php';

// 更新するデータ（実際にはフォームから受け取る）
$id = 1;
$name = "太郎（更新済み）";
$email = "taro_updated@example.com";

try {
    // プリペアドステートメントを準備
    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");

    // パラメータをバインド
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // 実行
    $stmt->execute();

    // 更新された行数を確認
    $affected = $stmt->rowCount();

    if ($affected > 0) {
        echo "ユーザー情報を更新しました！（{$affected}件）";
    } else {
        echo "該当するユーザーが見つからないか、変更がありませんでした。";
    }

} catch (PDOException $e) {
    echo "エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**解説**：

✅ **セキュリティポイント**
- **WHERE句は必須！** → 全データが更新されるのを防ぐ
- プリペアドステートメントでUPDATE

💡 **コードのポイント**
- `rowCount()`で更新された行数を取得
- WHERE句がないと全データが更新されるので注意！
- IDで特定のユーザーだけを更新

---

### 問題2-5：DELETE with プリペアドステートメント - 解答例

**解答**：

```php
<?php
// データベース接続
require_once 'config.php';

// 削除するユーザーID
$id = 3;

// 確認メッセージ（実際にはJavaScriptのconfirm()などを使う）
$confirmed = true; // この例では削除を実行

if ($confirmed) {
    try {
        // プリペアドステートメントを準備
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");

        // パラメータをバインド
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // 実行
        $stmt->execute();

        // 削除された行数を確認
        $affected = $stmt->rowCount();

        if ($affected > 0) {
            echo "ユーザーを削除しました。（{$affected}件）";
        } else {
            echo "該当するユーザーが見つかりませんでした。";
        }

    } catch (PDOException $e) {
        echo "エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
} else {
    echo "削除がキャンセルされました。";
}
?>
```

**HTMLフォームと組み合わせた例**：

```php
<?php
require_once 'config.php';

// POSTリクエストの場合のみ削除実行
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo "削除しました。";
    } catch (PDOException $e) {
        echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ユーザー削除</title>
</head>
<body>
    <h1>ユーザー削除</h1>
    <form method="POST" onsubmit="return confirm('本当に削除しますか？');">
        <input type="hidden" name="id" value="3">
        <button type="submit">削除する</button>
    </form>
</body>
</html>
```

**解説**：

✅ **セキュリティポイント**
- **WHERE句は必須！** → 全データが削除されるのを防ぐ
- 削除前に確認メッセージを表示
- POSTメソッドで削除（GETで削除は危険！）

💡 **コードのポイント**
- `rowCount()`で削除された行数を確認
- JavaScriptの`confirm()`で確認ダイアログ表示
- 削除は元に戻せないので慎重に！

---

### 問題2-6：疑問符プレースホルダーを使う - 解答例

**解答**：

```php
<?php
// データベース接続
require_once 'config.php';

// 検索するユーザーID
$id = 1;

try {
    // 疑問符プレースホルダーを使用
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");

    // 位置でバインド（1から始まる）
    $stmt->bindParam(1, $id, PDO::PARAM_INT);

    // 実行
    $stmt->execute();

    // データを1件取得
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 結果を表示
    if ($user) {
        echo "ユーザーが見つかりました！<br>";
        echo "名前: " . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "<br>";
        echo "メール: " . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . "<br>";
    } else {
        echo "ユーザーが見つかりませんでした。";
    }

} catch (PDOException $e) {
    echo "エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**execute()に配列を渡す方法（もっとシンプル）**：

```php
<?php
require_once 'config.php';

$id = 1;

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");

    // execute()に配列で渡す（bindParam()不要）
    $stmt->execute([$id]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "名前: " . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8');
    }

} catch (PDOException $e) {
    echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**解説**：

💡 **コードのポイント**
- `?`プレースホルダーは位置で指定（1, 2, 3...）
- `execute([$value])`で配列を渡すと`bindParam()`不要
- 複数の`?`がある場合は順序が重要
- 名前付きプレースホルダー（`:name`）の方が分かりやすい

**どちらを使うべき？**
- **名前付き（`:name`）**: パラメータが多い場合、分かりやすい
- **疑問符（`?`）**: シンプルなクエリ、短いコード

---

## 🛡️ セキュリティチャレンジ

### 問題2-7：脆弱性を修正（重要！）- 解答例

**脆弱なコード（再掲）**：

```php
<?php
// 🚨 危険！SQLインジェクション脆弱性あり
$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $pdo->query($sql);
$user = $result->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "ようこそ、" . $user['name'] . "さん！";
}
?>
```

**修正版（セキュア）**：

```php
<?php
// ✅ セキュア！プリペアドステートメント使用
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    // バリデーション
    if (empty($email)) {
        die("メールアドレスを入力してください。");
    }

    try {
        // プリペアドステートメントを準備
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");

        // パラメータをバインド
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        // 実行
        $stmt->execute();

        // データを取得
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 結果を表示（XSS対策）
        if ($user) {
            echo "ようこそ、" . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "さん！";
        } else {
            echo "ユーザーが見つかりませんでした。";
        }

    } catch (PDOException $e) {
        echo "エラーが発生しました。";
        // 本番環境ではエラーの詳細を表示しない
        // error_log($e->getMessage());
    }
}
?>
```

**解説**：

🚨 **脆弱なコードの問題点**
1. **SQLインジェクション脆弱性**: ユーザー入力を直接SQL文に埋め込んでいる
2. **XSS脆弱性**: 出力時に`htmlspecialchars()`を使っていない
3. **バリデーション不足**: 入力値のチェックがない
4. **エラーハンドリング不足**: try-catchがない

✅ **修正のポイント**
1. `prepare()`と`bindParam()`でSQLインジェクション対策
2. `htmlspecialchars()`でXSS対策
3. 空チェックでバリデーション
4. try-catchでエラーハンドリング

**攻撃例（脆弱なコードの場合）**：

攻撃者が以下を入力すると：
```
' OR '1'='1
```

脆弱なコードでは：
```sql
SELECT * FROM users WHERE email = '' OR '1'='1'
-- 常にtrueになり、全ユーザーが取得される！
```

修正版では：
```sql
SELECT * FROM users WHERE email = '\' OR \'1\'=\'1'
-- 文字列として扱われ、攻撃が無効化される
```

---

### 問題2-8：複数の条件でSELECT - 解答例

**解答**：

```php
<?php
// データベース接続
require_once 'config.php';

// 検索条件
$name = "太郎";
$email = "taro@example.com";

try {
    // プリペアドステートメントを準備（AND条件）
    $stmt = $pdo->prepare("SELECT * FROM users WHERE name = :name AND email = :email");

    // 両方のパラメータをバインド
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    // 実行
    $stmt->execute();

    // データを取得
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 結果を表示
    if ($user) {
        echo "ユーザーが見つかりました！<br>";
        echo "ID: " . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "<br>";
        echo "名前: " . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "<br>";
        echo "メール: " . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . "<br>";
    } else {
        echo "該当するユーザーが見つかりませんでした。";
    }

} catch (PDOException $e) {
    echo "エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**OR条件の例**：

```php
<?php
require_once 'config.php';

$searchTerm = "太郎";

try {
    // OR条件で名前またはメールアドレスで検索
    $stmt = $pdo->prepare("SELECT * FROM users WHERE name = :term OR email = :term");

    // 同じ値を両方にバインド
    $stmt->bindParam(':term', $searchTerm, PDO::PARAM_STR);

    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
        echo "<h2>検索結果: " . count($users) . "件</h2>";
        foreach ($users as $user) {
            echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "<br>";
        }
    } else {
        echo "見つかりませんでした。";
    }

} catch (PDOException $e) {
    echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**解説**：

💡 **コードのポイント**
- AND条件：両方が一致するデータを検索
- OR条件：どちらかが一致するデータを検索
- 複数のプレースホルダーを使う場合は、すべてバインド必須
- 同じプレースホルダー名（`:term`）を複数箇所で使える

---

## 💪 総合チャレンジ

### 問題2-9：完全なユーザー検索システム - 解答例

**解答**：

```php
<?php
// データベース接続
require_once 'config.php';

// 検索結果を格納する配列
$users = [];
$searchPerformed = false;

// フォームが送信された場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchPerformed = true;
    $searchTerm = $_POST['search'] ?? '';

    // 検索語が入力されている場合のみ検索
    if (!empty($searchTerm)) {
        try {
            // 名前またはメールアドレスで検索（部分一致）
            $stmt = $pdo->prepare("
                SELECT * FROM users
                WHERE name LIKE :term OR email LIKE :term
                ORDER BY created_at DESC
            ");

            // LIKE用にワイルドカードを追加
            $searchPattern = '%' . $searchTerm . '%';
            $stmt->bindParam(':term', $searchPattern, PDO::PARAM_STR);

            // 実行
            $stmt->execute();

            // 全件取得
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "<p style='color: red;'>エラーが発生しました。</p>";
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
    <title>ユーザー検索システム</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .search-box {
            margin-bottom: 30px;
        }
        input[type="text"] {
            padding: 10px;
            width: 300px;
            font-size: 16px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .user-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .no-results {
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>🔍 ユーザー検索システム</h1>

    <div class="search-box">
        <form method="POST">
            <input
                type="text"
                name="search"
                placeholder="名前またはメールアドレスで検索"
                value="<?php echo htmlspecialchars($_POST['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            >
            <button type="submit">検索</button>
        </form>
    </div>

    <?php if ($searchPerformed): ?>
        <?php if (count($users) > 0): ?>
            <h2>検索結果: <?php echo count($users); ?>件</h2>
            <?php foreach ($users as $user): ?>
                <div class="user-card">
                    <strong>名前:</strong> <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?><br>
                    <strong>メール:</strong> <?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?><br>
                    <strong>登録日:</strong> <?php echo htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-results">該当するユーザーが見つかりませんでした。</p>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
```

**解説**：

✅ **セキュリティ対策**
1. **SQLインジェクション対策**: プリペアドステートメント使用
2. **XSS対策**: すべての出力に`htmlspecialchars()`使用
3. **バリデーション**: 空チェック実施
4. **エラーハンドリング**: try-catch使用、エラー詳細は非表示

💡 **機能のポイント**
- OR条件で名前またはメールアドレスで検索
- LIKE句で部分一致検索（`%検索語%`）
- 検索結果を新しい順に表示（`ORDER BY created_at DESC`）
- 検索語が空の場合は検索を実行しない
- 検索語をフォームに保持（ユーザビリティ向上）
- レスポンシブデザイン対応

🎨 **改善ポイント**
- カテゴリ別検索（名前のみ、メールのみ）を追加できる
- ページング機能を追加できる（Lesson 05で学習）
- 検索語のハイライト表示
- ソート機能（名前順、日付順）

---

## 🎓 まとめ

### 学んだこと

✅ **プリペアドステートメントの基本**
- `prepare()` → `bindParam()` → `execute()`の流れ
- 名前付きプレースホルダー（`:name`）と疑問符プレースホルダー（`?`）

✅ **CRUD操作すべて**
- SELECT（`fetch()`, `fetchAll()`）
- INSERT（`lastInsertId()`）
- UPDATE（`rowCount()`）
- DELETE（確認メッセージ）

✅ **セキュリティ対策**
- SQLインジェクション対策（プリペアドステートメント）
- XSS対策（`htmlspecialchars()`）
- バリデーション（入力チェック）
- エラーハンドリング（try-catch）

### 次のステップ

👉 **[Lesson 03: CRUD操作](../../03-crud-operations/README.md)**に進んで、より実践的なアプリケーション開発を学ぼう！

---

**Let's vibe and code! 🎉**

セキュアなコードが書けるようになったね！次のレッスンも楽しもう！
