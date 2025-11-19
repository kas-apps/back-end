# Lesson 04: セキュリティ対策 - 解答例 🛡️

このファイルでは、Lesson 04のセキュリティ演習問題の詳細な解答例と解説を提供します！

バックエンド開発で最も重要なセキュリティ対策を、実践的なコード例とともに学びましょう。

---

## 🌱 基礎編

### 問題4-1：SQLインジェクション対策 - 解答例

**❌ 脆弱なコード**（再掲）：

```php
<?php
// 危険！SQLインジェクションの脆弱性あり
$email = $_GET['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $pdo->query($sql);
```

**攻撃例**：

```text
?email=test@example.com' OR '1'='1
→ SELECT * FROM users WHERE email = 'test@example.com' OR '1'='1'
→ すべてのユーザー情報が漏洩！
```

**✅ セキュアな解答**：

```php
<?php
require_once 'config.php';

// ユーザー入力を取得
$email = $_GET['email'] ?? '';

// バリデーション
if (empty($email)) {
    die("メールアドレスを入力してください。");
}

try {
    // プリペアドステートメントで安全に検索
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "<h3>ユーザー情報</h3>";
        echo "ID: " . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . "<br>";
        echo "名前: " . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "<br>";
        echo "メール: " . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . "<br>";
    } else {
        echo "ユーザーが見つかりませんでした。";
    }

} catch (PDOException $e) {
    error_log("データベースエラー: " . $e->getMessage());
    die("エラーが発生しました。");
}
?>
```

**✅ セキュリティポイント**：

- ✅ **プリペアドステートメント**：SQL文とデータを分離
- ✅ **パラメータバインド**：`:email`プレースホルダーを使用
- ✅ **型指定**：`PDO::PARAM_STR`で文字列として扱う
- ✅ **XSS対策**：出力時に`htmlspecialchars()`を使用
- ✅ **エラーハンドリング**：詳細なエラーメッセージを表示しない

**💡 コードのポイント**：

- `prepare()`でSQL文のテンプレートを作成
- `bindParam()`でプレースホルダーに値をバインド
- `execute()`で実際にクエリを実行
- SQL文と値が完全に分離されているため、攻撃コードが実行されない

**🎓 プリペアドステートメントの仕組み**：

```php
// ❌ 危険：文字列連結
$sql = "SELECT * FROM users WHERE email = '$email'";
// ユーザー入力がSQL文の一部として解釈される

// ✅ 安全：プリペアドステートメント
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([':email' => $email]);
// ユーザー入力は「データ」として扱われ、SQL文として解釈されない
```

---

### 問題4-2：XSS対策（クロスサイトスクリプティング）- 解答例

**❌ 脆弱なコード**：

```php
<?php
// 危険！XSSの脆弱性あり
$name = $_POST['name'];
echo "こんにちは、" . $name . "さん！";
?>
```

**攻撃例**：

```text
name=<script>alert('XSS攻撃！')</script>
→ こんにちは、<script>alert('XSS攻撃！')</script>さん！
→ JavaScriptコードが実行される！
```

**✅ セキュアな解答**：

**display_name.php**：

```php
<?php
require_once 'config.php';

// ユーザー入力を取得
$name = $_POST['name'] ?? '';

// バリデーション
if (empty($name)) {
    die("名前を入力してください。");
}

// 名前の長さチェック（100文字以内）
if (mb_strlen($name) > 100) {
    die("名前は100文字以内で入力してください。");
}

// データベースに保存（プリペアドステートメント）
try {
    $stmt = $pdo->prepare("INSERT INTO comments (name, created_at) VALUES (:name, NOW())");
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->execute();

    echo "<h3>登録完了</h3>";
    // XSS対策：htmlspecialchars()でエスケープ
    echo "<p>こんにちは、" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "さん！</p>";
    echo "<p>登録が完了しました。</p>";

} catch (PDOException $e) {
    error_log("データベースエラー: " . $e->getMessage());
    die("エラーが発生しました。");
}
?>
```

**コメント表示（view_comments.php）**：

```php
<?php
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM comments ORDER BY created_at DESC LIMIT 10");
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>最近のコメント</h3>";

    foreach ($comments as $comment) {
        echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 10px 0;'>";
        // XSS対策：すべての出力をエスケープ
        echo "<strong>" . htmlspecialchars($comment['name'], ENT_QUOTES, 'UTF-8') . "</strong><br>";
        echo "<small>" . htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8') . "</small>";
        echo "</div>";
    }

} catch (PDOException $e) {
    error_log("データベースエラー: " . $e->getMessage());
    die("エラーが発生しました。");
}
?>
```

**✅ セキュリティポイント**：

- ✅ **htmlspecialchars()**：特殊文字をHTMLエンティティに変換
- ✅ **ENT_QUOTES**：シングルクォートとダブルクォートの両方をエスケープ
- ✅ **UTF-8指定**：文字コードを明示的に指定
- ✅ **入力バリデーション**：文字数制限でバッファオーバーフロー対策
- ✅ **すべての出力をエスケープ**：信頼できないデータは必ずエスケープ

**💡 コードのポイント**：

```php
// htmlspecialchars()の動作
$name = "<script>alert('XSS')</script>";
echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
// 出力: &lt;script&gt;alert(&#039;XSS&#039;)&lt;/script&gt;
// ブラウザには "<script>alert('XSS')</script>" と表示され、実行されない
```

**🎓 XSS攻撃の種類**：

1. **Reflected XSS（反射型）**：URLパラメータからの攻撃
2. **Stored XSS（格納型）**：データベースに保存された攻撃コード
3. **DOM-based XSS**：JavaScriptでのDOM操作による攻撃

**すべてのケースで`htmlspecialchars()`が有効！**

---

### 問題4-3：CSRF対策（クロスサイトリクエストフォージェリ）- 解答例

**❌ 脆弱なコード**：

```php
<?php
// 危険！CSRF攻撃の脆弱性あり
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    // メールアドレスを変更
    $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE id = :id");
    $stmt->execute([':email' => $email, ':id' => $_SESSION['user_id']]);
}
?>
```

**攻撃例**：
悪意のあるサイトから以下のHTMLを送信：

```html
<form action="https://target-site.com/change_email.php" method="POST">
    <input type="hidden" name="email" value="attacker@evil.com">
    <script>document.forms[0].submit();</script>
</form>
```

→ ユーザーが気づかないうちにメールアドレスが変更される！

**✅ セキュアな解答**：

**change_email_form.php**（フォーム表示）：

```php
<?php
session_start();

// セッションにCSRFトークンがない場合は生成
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メールアドレス変更</title>
</head>
<body>
    <h2>メールアドレス変更</h2>

    <form action="change_email_process.php" method="POST">
        <!-- CSRFトークンを隠しフィールドに埋め込む -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

        <label>新しいメールアドレス:</label><br>
        <input type="email" name="email" required><br><br>

        <button type="submit">変更する</button>
    </form>
</body>
</html>
```

**change_email_process.php**（処理実行）：

```php
<?php
session_start();
require_once 'config.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    die("ログインしてください。");
}

// POSTリクエストかチェック
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("不正なリクエストです。");
}

// CSRFトークンを検証
$token = $_POST['csrf_token'] ?? '';

if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    die("不正なリクエストです。CSRFトークンが一致しません。");
}

// メールアドレスを取得
$email = $_POST['email'] ?? '';

// バリデーション
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("有効なメールアドレスを入力してください。");
}

try {
    // メールアドレスを更新
    $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE id = :id");
    $stmt->execute([
        ':email' => $email,
        ':id' => $_SESSION['user_id']
    ]);

    // CSRFトークンを再生成（ワンタイムトークン）
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    echo "メールアドレスを変更しました。";
    echo "<br><a href='change_email_form.php'>戻る</a>";

} catch (PDOException $e) {
    error_log("データベースエラー: " . $e->getMessage());
    die("エラーが発生しました。");
}
?>
```

**✅ セキュリティポイント**：

- ✅ **CSRFトークン生成**：`bin2hex(random_bytes(32))`で推測不可能なトークン
- ✅ **トークン検証**：`hash_equals()`でタイミング攻撃対策
- ✅ **ワンタイムトークン**：処理後にトークンを再生成
- ✅ **POSTメソッド限定**：GET リクエストでは処理しない
- ✅ **ログイン確認**：セッションでユーザー認証を確認

**💡 コードのポイント**：

```php
// ❌ 危険：単純な比較（タイミング攻撃の可能性）
if ($_SESSION['csrf_token'] === $token) { ... }

// ✅ 安全：hash_equals()を使用（タイミング攻撃対策）
if (hash_equals($_SESSION['csrf_token'], $token)) { ... }
```

**🎓 CSRF対策の原則**：

1. **すべての状態変更操作にCSRFトークンを使用**（POST, PUT, DELETEなど）
2. **トークンはセッションに保存**
3. **トークンはランダム生成**（`random_bytes()`）
4. **トークンは使い捨て**（ワンタイムトークン推奨）
5. **hash_equals()で比較**（タイミング攻撃対策）

---

### 問題4-4：パスワードハッシュ化 - 解答例

**❌ 脆弱なコード**：

```php
<?php
// 危険！平文パスワードの保存
$password = $_POST['password'];
$stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
$stmt->execute([':email' => $email, ':password' => $password]);
?>
```

**危険性**：

- データベースが漏洩したらパスワードが丸見え
- 管理者もユーザーのパスワードを見ることができる
- 他のサイトで同じパスワードを使っている場合、そちらも危険

**✅ セキュアな解答**：

**register.php**（ユーザー登録）：

```php
<?php
require_once 'config.php';
session_start();

// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // バリデーション
    $errors = [];

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "有効なメールアドレスを入力してください。";
    }

    if (empty($password)) {
        $errors[] = "パスワードを入力してください。";
    } elseif (strlen($password) < 8) {
        $errors[] = "パスワードは8文字以上にしてください。";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "パスワードには大文字、小文字、数字を含めてください。";
    }

    if ($password !== $password_confirm) {
        $errors[] = "パスワードが一致しません。";
    }

    // エラーがない場合、登録処理
    if (empty($errors)) {
        try {
            // パスワードをハッシュ化（PASSWORD_DEFAULT = bcrypt）
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // ユーザーを登録
            $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, created_at) VALUES (:email, :password_hash, NOW())");
            $stmt->execute([
                ':email' => $email,
                ':password_hash' => $password_hash
            ]);

            echo "<h3>登録完了</h3>";
            echo "<p>ユーザー登録が完了しました。</p>";
            echo "<a href='login.php'>ログイン</a>";
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // 重複エラー
                $errors[] = "このメールアドレスは既に登録されています。";
            } else {
                error_log("データベースエラー: " . $e->getMessage());
                $errors[] = "エラーが発生しました。";
            }
        }
    }

    // エラー表示
    if (!empty($errors)) {
        echo "<h3>エラー</h3>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</li>";
        }
        echo "</ul>";
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
    <h2>ユーザー登録</h2>

    <form method="POST">
        <label>メールアドレス:</label><br>
        <input type="email" name="email" required><br><br>

        <label>パスワード（8文字以上、大文字・小文字・数字を含む）:</label><br>
        <input type="password" name="password" required><br><br>

        <label>パスワード（確認）:</label><br>
        <input type="password" name="password_confirm" required><br><br>

        <button type="submit">登録</button>
    </form>

    <p><a href="login.php">ログインはこちら</a></p>
</body>
</html>
```

**login.php**（ログイン）：

```php
<?php
require_once 'config.php';
session_start();

// POSTリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // バリデーション
    if (empty($email) || empty($password)) {
        $error = "メールアドレスとパスワードを入力してください。";
    } else {
        try {
            // ユーザーを検索
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // パスワードを検証
            if ($user && password_verify($password, $user['password_hash'])) {
                // ログイン成功

                // セッションハイジャック対策：セッションIDを再生成
                session_regenerate_id(true);

                // セッションにユーザー情報を保存
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['login_time'] = time();

                echo "<h3>ログイン成功</h3>";
                echo "<p>ようこそ、" . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . " さん！</p>";
                echo "<a href='dashboard.php'>ダッシュボードへ</a>";
                exit;

            } else {
                $error = "メールアドレスまたはパスワードが正しくありません。";
            }

        } catch (PDOException $e) {
            error_log("データベースエラー: " . $e->getMessage());
            $error = "エラーが発生しました。";
        }
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
    <h2>ログイン</h2>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>メールアドレス:</label><br>
        <input type="email" name="email" required><br><br>

        <label>パスワード:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">ログイン</button>
    </form>

    <p><a href="register.php">新規登録はこちら</a></p>
</body>
</html>
```

**✅ セキュリティポイント**：

- ✅ **password_hash()**：強力なハッシュアルゴリズム（bcrypt）を使用
- ✅ **PASSWORD_DEFAULT**：PHPの推奨アルゴリズムを自動選択
- ✅ **password_verify()**：タイミング攻撃に強い検証
- ✅ **パスワードポリシー**：最小8文字、大文字・小文字・数字を必須
- ✅ **session_regenerate_id()**：ログイン時にセッションIDを再生成
- ✅ **エラーメッセージ**：「メールアドレスまたはパスワードが正しくありません」（どちらが間違っているか特定させない）

**💡 コードのポイント**：

```php
// password_hash() の出力例
$hash = password_hash('MyPassword123', PASSWORD_DEFAULT);
// $2y$10$abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVW
// ↑ bcryptアルゴリズム、コスト10、ソルト、ハッシュ値

// 同じパスワードでも毎回異なるハッシュが生成される（ソルトのおかげ）
password_hash('MyPassword123', PASSWORD_DEFAULT); // $2y$10$xxx...
password_hash('MyPassword123', PASSWORD_DEFAULT); // $2y$10$yyy...（異なる）

// でもpassword_verify()はどちらでも検証できる
password_verify('MyPassword123', $hash); // true
```

**🎓 パスワードハッシュのベストプラクティス**：

1. **password_hash()を使う**（自前でハッシュ化しない）
2. **PASSWORD_DEFAULTを使う**（将来のアルゴリズム変更に対応）
3. **絶対にMD5やSHA1を使わない**（脆弱）
4. **ソルトは自動生成される**（手動でソルトを管理しない）
5. **パスワードポリシーを設定**（最小8文字、複雑性要件）

---

## 🚀 応用編

### 問題4-5：セッション管理（セッションハイジャック対策）- 解答例

**❌ 脆弱なコード**：

```php
<?php
// 危険！セッションハイジャックの脆弱性
session_start();
$_SESSION['user_id'] = $user['id'];
// セッションIDが盗まれたら、攻撃者がなりすまし可能
?>
```

**✅ セキュアな解答**：

**secure_session.php**（共通セッション管理）：

```php
<?php
/**
 * セキュアなセッション管理
 */

// セッション設定（session_start()の前に設定）
ini_set('session.cookie_httponly', 1);  // JavaScriptからのアクセスを防ぐ
ini_set('session.use_only_cookies', 1);  // URLにセッションIDを含めない
ini_set('session.cookie_secure', 0);     // HTTPS環境では1に設定
ini_set('session.cookie_samesite', 'Strict');  // CSRF対策

// セッション開始
session_start();

/**
 * ログイン処理（セッションIDを再生成）
 */
function secureLogin($user_id, $email) {
    // セッションハイジャック対策：セッションIDを再生成
    session_regenerate_id(true);

    // セッションにユーザー情報を保存
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $email;
    $_SESSION['login_time'] = time();
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
}

/**
 * ログインチェック（セッション検証）
 */
function isLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    // セッションタイムアウト（30分）
    $timeout = 1800; // 30分
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $timeout) {
        session_unset();
        session_destroy();
        return false;
    }

    // User-Agentの検証（セッション固定攻撃対策）
    $current_user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $current_user_agent) {
        // User-Agentが変わった場合、セッションを破棄
        session_unset();
        session_destroy();
        return false;
    }

    // IPアドレスの検証（オプション：厳しすぎる場合あり）
    // $current_ip = $_SERVER['REMOTE_ADDR'] ?? '';
    // if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $current_ip) {
    //     session_unset();
    //     session_destroy();
    //     return false;
    // }

    // セッションの最終アクセス時刻を更新
    $_SESSION['login_time'] = time();

    return true;
}

/**
 * ログアウト処理
 */
function secureLogout() {
    // セッション変数をすべて削除
    $_SESSION = [];

    // セッションクッキーを削除
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // セッションを破棄
    session_destroy();
}

/**
 * ログインが必要なページで呼び出す
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}
?>
```

**使用例（dashboard.php）**：

```php
<?php
require_once 'config.php';
require_once 'secure_session.php';

// ログインチェック
requireLogin();

// ここから先はログイン済みユーザーのみアクセス可能
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ダッシュボード</title>
</head>
<body>
    <h2>ダッシュボード</h2>

    <p>ようこそ、<?php echo htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8'); ?> さん！</p>

    <p>ログイン時刻: <?php echo date('Y-m-d H:i:s', $_SESSION['login_time']); ?></p>

    <p><a href="logout.php">ログアウト</a></p>
</body>
</html>
```

**logout.php**：

```php
<?php
require_once 'secure_session.php';

// ログアウト処理
secureLogout();

// ログインページにリダイレクト
header("Location: login.php");
exit;
?>
```

**✅ セキュリティポイント**：

- ✅ **session_regenerate_id()**：ログイン時にセッションIDを再生成
- ✅ **HttpOnly クッキー**：JavaScriptからセッションIDを読めなくする
- ✅ **セッションタイムアウト**：30分間操作がない場合、自動ログアウト
- ✅ **User-Agent検証**：ブラウザが変わったらセッションを破棄
- ✅ **セキュアなログアウト**：セッション変数とクッキーを完全に削除

**💡 コードのポイント**：

```php
// session_regenerate_id(true) の動作
session_regenerate_id(true);  // 引数 true で古いセッションファイルを削除
// Before: セッションID = abc123
// After:  セッションID = xyz789（新規生成）

// これにより、セッション固定攻撃を防ぐ
```

**🎓 セッションセキュリティのベストプラクティス**：

1. **ログイン時に`session_regenerate_id()`**
2. **HttpOnlyクッキーを有効化**
3. **HTTPS環境では`session.cookie_secure`を1に**
4. **セッションタイムアウトを設定**（30分推奨）
5. **User-Agentを検証**
6. **ログアウト時にセッションを完全に破棄**

---

### 問題4-6：入力バリデーション - 解答例

**✅ セキュアな解答**：

**validation_functions.php**（バリデーション関数集）：

```php
<?php
/**
 * セキュアな入力バリデーション関数
 */

/**
 * メールアドレスのバリデーション
 */
function validateEmail($email) {
    $errors = [];

    if (empty($email)) {
        $errors[] = "メールアドレスを入力してください。";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "有効なメールアドレスを入力してください。";
    } elseif (strlen($email) > 255) {
        $errors[] = "メールアドレスは255文字以内で入力してください。";
    }

    return $errors;
}

/**
 * パスワードのバリデーション
 */
function validatePassword($password) {
    $errors = [];

    if (empty($password)) {
        $errors[] = "パスワードを入力してください。";
    } elseif (strlen($password) < 8) {
        $errors[] = "パスワードは8文字以上にしてください。";
    } elseif (strlen($password) > 255) {
        $errors[] = "パスワードは255文字以内で入力してください。";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "パスワードには大文字を含めてください。";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $errors[] = "パスワードには小文字を含めてください。";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors[] = "パスワードには数字を含めてください。";
    }

    return $errors;
}

/**
 * 名前のバリデーション
 */
function validateName($name) {
    $errors = [];

    if (empty($name)) {
        $errors[] = "名前を入力してください。";
    } elseif (mb_strlen($name) < 2) {
        $errors[] = "名前は2文字以上で入力してください。";
    } elseif (mb_strlen($name) > 100) {
        $errors[] = "名前は100文字以内で入力してください。";
    } elseif (!preg_match('/^[ぁ-んァ-ヶー一-龠a-zA-Z\s]+$/u', $name)) {
        $errors[] = "名前には日本語、英字、スペースのみ使用できます。";
    }

    return $errors;
}

/**
 * 年齢のバリデーション
 */
function validateAge($age) {
    $errors = [];

    if ($age === '' || $age === null) {
        $errors[] = "年齢を入力してください。";
    } elseif (!is_numeric($age)) {
        $errors[] = "年齢は数値で入力してください。";
    } elseif ($age < 0 || $age > 150) {
        $errors[] = "年齢は0〜150の範囲で入力してください。";
    }

    return $errors;
}

/**
 * 電話番号のバリデーション
 */
function validatePhone($phone) {
    $errors = [];

    if (empty($phone)) {
        $errors[] = "電話番号を入力してください。";
    } elseif (!preg_match('/^0\d{9,10}$/', $phone)) {
        $errors[] = "有効な電話番号を入力してください（例: 09012345678）。";
    }

    return $errors;
}

/**
 * URLのバリデーション
 */
function validateUrl($url) {
    $errors = [];

    if (empty($url)) {
        $errors[] = "URLを入力してください。";
    } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
        $errors[] = "有効なURLを入力してください。";
    } elseif (!preg_match('/^https?:\/\//', $url)) {
        $errors[] = "URLは http:// または https:// で始まる必要があります。";
    }

    return $errors;
}

/**
 * 日付のバリデーション
 */
function validateDate($date) {
    $errors = [];

    if (empty($date)) {
        $errors[] = "日付を入力してください。";
    } else {
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            $errors[] = "有効な日付を入力してください（YYYY-MM-DD形式）。";
        }
    }

    return $errors;
}

/**
 * すべてのバリデーションエラーをまとめて表示
 */
function displayErrors($errors) {
    if (!empty($errors)) {
        echo "<div style='background-color: #ffdddd; padding: 15px; border-left: 4px solid #f44336; margin: 10px 0;'>";
        echo "<strong>エラー:</strong><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</li>";
        }
        echo "</ul></div>";
    }
}
?>
```

**使用例（user_profile_form.php）**：

```php
<?php
require_once 'config.php';
require_once 'validation_functions.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 入力値を取得
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $age = $_POST['age'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $birthday = $_POST['birthday'] ?? '';

    // バリデーション
    $errors = array_merge($errors, validateName($name));
    $errors = array_merge($errors, validateEmail($email));
    $errors = array_merge($errors, validateAge($age));
    $errors = array_merge($errors, validatePhone($phone));
    $errors = array_merge($errors, validateUrl($url));
    $errors = array_merge($errors, validateDate($birthday));

    // エラーがなければ保存
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO user_profiles (name, email, age, phone, website, birthday, created_at)
                VALUES (:name, :email, :age, :phone, :website, :birthday, NOW())
            ");

            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':age' => (int)$age,
                ':phone' => $phone,
                ':website' => $url,
                ':birthday' => $birthday
            ]);

            echo "<div style='background-color: #ddffdd; padding: 15px; border-left: 4px solid #4CAF50; margin: 10px 0;'>";
            echo "<strong>登録完了:</strong> プロフィールを登録しました。";
            echo "</div>";

        } catch (PDOException $e) {
            error_log("データベースエラー: " . $e->getMessage());
            $errors[] = "エラーが発生しました。";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>プロフィール登録</title>
</head>
<body>
    <h2>プロフィール登録</h2>

    <?php displayErrors($errors); ?>

    <form method="POST">
        <label>名前:</label><br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>

        <label>メールアドレス:</label><br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>

        <label>年齢:</label><br>
        <input type="number" name="age" value="<?php echo htmlspecialchars($age ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>

        <label>電話番号:</label><br>
        <input type="tel" name="phone" value="<?php echo htmlspecialchars($phone ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>

        <label>ウェブサイト:</label><br>
        <input type="url" name="url" value="<?php echo htmlspecialchars($url ?? '', ENT_QUOTES, 'UTF-8'); ?>"><br><br>

        <label>生年月日:</label><br>
        <input type="date" name="birthday" value="<?php echo htmlspecialchars($birthday ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>

        <button type="submit">登録</button>
    </form>
</body>
</html>
```

**✅ セキュリティポイント**：

- ✅ **個別バリデーション関数**：再利用可能で保守しやすい
- ✅ **filter_var()**：PHPの組み込みフィルタを活用
- ✅ **正規表現**：複雑なパターンマッチング
- ✅ **文字数制限**：バッファオーバーフロー対策
- ✅ **型変換**：`(int)$age`で整数に変換
- ✅ **trim()**：前後の空白を削除

**💡 コードのポイント**：

```php
// filter_var() の活用例
filter_var('test@example.com', FILTER_VALIDATE_EMAIL);  // true
filter_var('invalid-email', FILTER_VALIDATE_EMAIL);     // false

filter_var('https://example.com', FILTER_VALIDATE_URL); // true
filter_var('not-a-url', FILTER_VALIDATE_URL);           // false

// 正規表現の例
preg_match('/^0\d{9,10}$/', '09012345678');  // true（携帯電話番号）
preg_match('/^https?:\/\//', 'https://...');  // true（http/https）
```

---

## 🛡️ セキュリティチャレンジ

### 問題4-7：5つのセキュリティ対策を統合 - 解答例

すべてのセキュリティ対策を組み合わせた完全なシステムを作成します。

**secure_blog_post.php**（ブログ記事投稿）：

```php
<?php
require_once 'config.php';
require_once 'secure_session.php';
require_once 'validation_functions.php';

// ログインチェック
requireLogin();

// CSRFトークン生成
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF対策：トークン検証
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        die("不正なリクエストです。");
    }

    // 入力値を取得
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = $_POST['category_id'] ?? '';

    // バリデーション
    if (empty($title)) {
        $errors[] = "タイトルを入力してください。";
    } elseif (mb_strlen($title) < 3 || mb_strlen($title) > 200) {
        $errors[] = "タイトルは3〜200文字で入力してください。";
    }

    if (empty($content)) {
        $errors[] = "本文を入力してください。";
    } elseif (mb_strlen($content) < 10) {
        $errors[] = "本文は10文字以上で入力してください。";
    }

    if (!is_numeric($category_id) || $category_id <= 0) {
        $errors[] = "カテゴリーを選択してください。";
    }

    // エラーがなければ保存
    if (empty($errors)) {
        try {
            // SQLインジェクション対策：プリペアドステートメント
            $stmt = $pdo->prepare("
                INSERT INTO blog_posts (user_id, category_id, title, content, created_at)
                VALUES (:user_id, :category_id, :title, :content, NOW())
            ");

            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
                ':category_id' => (int)$category_id,
                ':title' => $title,
                ':content' => $content
            ]);

            // CSRFトークンを再生成
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            $success_message = "ブログ記事を投稿しました。";

        } catch (PDOException $e) {
            error_log("データベースエラー: " . $e->getMessage());
            $errors[] = "エラーが発生しました。";
        }
    }
}

// カテゴリー一覧を取得
try {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("データベースエラー: " . $e->getMessage());
    $categories = [];
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ブログ記事投稿</title>
</head>
<body>
    <h2>ブログ記事投稿</h2>

    <?php if (isset($success_message)): ?>
        <div style="background-color: #ddffdd; padding: 15px; border-left: 4px solid #4CAF50;">
            <?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php displayErrors($errors); ?>

    <form method="POST">
        <!-- CSRF対策：トークンを隠しフィールドに -->
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

        <label>タイトル:</label><br>
        <input type="text" name="title" size="50" value="<?php echo htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8'); ?>" required><br><br>

        <label>カテゴリー:</label><br>
        <select name="category_id" required>
            <option value="">選択してください</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>本文:</label><br>
        <textarea name="content" rows="10" cols="50" required><?php echo htmlspecialchars($content ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea><br><br>

        <button type="submit">投稿</button>
    </form>

    <p><a href="blog_list.php">記事一覧に戻る</a> | <a href="logout.php">ログアウト</a></p>
</body>
</html>
```

**blog_list.php**（ブログ記事一覧）：

```php
<?php
require_once 'config.php';

try {
    // 記事一覧を取得（JOIN で作成者とカテゴリーも取得）
    $stmt = $pdo->query("
        SELECT
            blog_posts.id,
            blog_posts.title,
            blog_posts.content,
            blog_posts.created_at,
            users.email AS author_email,
            categories.name AS category_name
        FROM blog_posts
        INNER JOIN users ON blog_posts.user_id = users.id
        INNER JOIN categories ON blog_posts.category_id = categories.id
        ORDER BY blog_posts.created_at DESC
        LIMIT 20
    ");

    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("データベースエラー: " . $e->getMessage());
    $posts = [];
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ブログ記事一覧</title>
    <style>
        .post { border: 1px solid #ddd; padding: 15px; margin: 10px 0; }
        .post-title { font-size: 1.3em; font-weight: bold; color: #333; }
        .post-meta { color: #666; font-size: 0.9em; }
        .post-content { margin-top: 10px; line-height: 1.6; }
    </style>
</head>
<body>
    <h2>ブログ記事一覧</h2>

    <?php if (empty($posts)): ?>
        <p>まだ記事がありません。</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
        <div class="post">
            <!-- XSS対策：すべての出力をエスケープ -->
            <div class="post-title">
                <?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="post-meta">
                カテゴリー: <?php echo htmlspecialchars($post['category_name'], ENT_QUOTES, 'UTF-8'); ?> |
                投稿者: <?php echo htmlspecialchars($post['author_email'], ENT_QUOTES, 'UTF-8'); ?> |
                投稿日時: <?php echo htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'], ENT_QUOTES, 'UTF-8')); ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <p><a href="secure_blog_post.php">新しい記事を投稿</a></p>
</body>
</html>
```

**✅ セキュリティポイント（統合）**：

1. ✅ **SQLインジェクション対策**：プリペアドステートメント
2. ✅ **XSS対策**：`htmlspecialchars()`で全出力をエスケープ
3. ✅ **CSRF対策**：トークン検証と再生成
4. ✅ **セッション管理**：`requireLogin()`でログイン確認
5. ✅ **入力バリデーション**：すべての入力を検証
6. ✅ **エラーハンドリング**：詳細なエラーをログに記録
7. ✅ **型変換**：`(int)$category_id`で安全な型変換

---

### 問題4-8：セキュリティ脆弱性を発見して修正 - 解答例

**❌ 脆弱なコード**（再掲）：

```php
<?php
session_start();
$id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id = $id";
$result = $pdo->query($sql);
$product = $result->fetch();

echo "<h2>" . $product['name'] . "</h2>";
echo "<p>価格: ¥" . $product['price'] . "</p>";
echo "<p>" . $product['description'] . "</p>";
?>
```

**🔍 脆弱性の発見**：

1. **SQLインジェクション**：`$id`を直接SQL文に埋め込んでいる
2. **XSS（クロスサイトスクリプティング）**：出力時にエスケープしていない
3. **エラーハンドリング不足**：`try-catch`がない
4. **型検証なし**：`$id`が数値かチェックしていない
5. **存在チェックなし**：商品が見つからない場合の処理がない

**✅ セキュアな修正版**：

```php
<?php
require_once 'config.php';

// 商品IDを取得
$id = $_GET['id'] ?? 0;

// 型検証：整数に変換
$id = (int)$id;

// IDの妥当性チェック
if ($id <= 0) {
    die("無効な商品IDです。");
}

try {
    // SQLインジェクション対策：プリペアドステートメント
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // 存在チェック
    if (!$product) {
        echo "<h2>商品が見つかりませんでした</h2>";
        echo "<p><a href='product_list.php'>商品一覧に戻る</a></p>";
        exit;
    }

    // XSS対策：すべての出力をエスケープ
    echo "<h2>" . htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . "</h2>";
    echo "<p>価格: ¥" . number_format($product['price']) . "</p>";
    echo "<p>" . nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')) . "</p>";

} catch (PDOException $e) {
    // エラーハンドリング：詳細をログに記録
    error_log("データベースエラー: " . $e->getMessage());
    die("エラーが発生しました。");
}
?>
```

**✅ 修正ポイント**：

1. ✅ **SQLインジェクション対策**：プリペアドステートメントを使用
2. ✅ **XSS対策**：`htmlspecialchars()`で全出力をエスケープ
3. ✅ **型変換**：`(int)$id`で整数に変換
4. ✅ **バリデーション**：`$id <= 0`のチェック
5. ✅ **存在チェック**：商品が見つからない場合の処理
6. ✅ **エラーハンドリング**：`try-catch`で例外処理
7. ✅ **セキュアな数値表示**：`number_format()`を使用
8. ✅ **改行の安全な処理**：`nl2br()`を使用

---

### 問題4-9：HTTPSとセキュアクッキー - 解答例

HTTPS環境用のセキュアな設定を実装します。

**secure_https_config.php**：

```php
<?php
/**
 * HTTPS環境用のセキュアな設定
 */

// HTTPS強制リダイレクト（本番環境のみ）
$is_production = ($_ENV['APP_ENV'] ?? 'development') === 'production';

if ($is_production && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on')) {
    $redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $redirect_url", true, 301);
    exit;
}

// セッション設定（HTTPS環境用）
if ($is_production) {
    ini_set('session.cookie_secure', 1);     // HTTPS接続でのみクッキーを送信
    ini_set('session.cookie_httponly', 1);   // JavaScriptからのアクセスを防ぐ
    ini_set('session.use_only_cookies', 1);  // URLにセッションIDを含めない
    ini_set('session.cookie_samesite', 'Strict');  // CSRF対策
    ini_set('session.cookie_lifetime', 0);   // ブラウザを閉じたら削除
} else {
    // 開発環境用（HTTP）
    ini_set('session.cookie_secure', 0);     // HTTPでも動作
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
}

// セッション開始
session_start();

/**
 * セキュアなクッキー設定
 */
function setSecureCookie($name, $value, $expire = 0) {
    global $is_production;

    $options = [
        'expires' => $expire,
        'path' => '/',
        'domain' => '',  // 現在のドメイン
        'secure' => $is_production,  // HTTPS環境でのみtrue
        'httponly' => true,  // JavaScriptからアクセス不可
        'samesite' => 'Strict'  // CSRF対策
    ];

    setcookie($name, $value, $options);
}

/**
 * HSTS（HTTP Strict Transport Security）ヘッダーを送信
 */
function sendSecurityHeaders() {
    global $is_production;

    if ($is_production) {
        // HSTS: ブラウザに常にHTTPSを使うよう指示（1年間）
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");

        // X-Frame-Options: クリックジャッキング対策
        header("X-Frame-Options: DENY");

        // X-Content-Type-Options: MIMEタイプスニッフィング対策
        header("X-Content-Type-Options: nosniff");

        // X-XSS-Protection: ブラウザのXSSフィルタを有効化
        header("X-XSS-Protection: 1; mode=block");

        // Content-Security-Policy: XSS対策
        header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline';");
    }
}

// セキュリティヘッダーを送信
sendSecurityHeaders();
?>
```

**使用例（secure_login.php）**：

```php
<?php
require_once 'config.php';
require_once 'secure_https_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // ログイン成功
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['login_time'] = time();

            // 「ログイン状態を保持」がチェックされた場合
            if ($remember) {
                // セキュアなクッキーを設定（30日間有効）
                $token = bin2hex(random_bytes(32));
                $expire = time() + (30 * 24 * 60 * 60);  // 30日後

                // トークンをデータベースに保存
                $stmt = $pdo->prepare("
                    INSERT INTO remember_tokens (user_id, token, expires_at)
                    VALUES (:user_id, :token, FROM_UNIXTIME(:expires_at))
                ");
                $stmt->execute([
                    ':user_id' => $user['id'],
                    ':token' => hash('sha256', $token),  // ハッシュ化して保存
                    ':expires_at' => $expire
                ]);

                // セキュアなクッキーを設定
                setSecureCookie('remember_token', $token, $expire);
            }

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "メールアドレスまたはパスワードが正しくありません。";
        }

    } catch (PDOException $e) {
        error_log("データベースエラー: " . $e->getMessage());
        $error = "エラーが発生しました。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>セキュアログイン</title>
</head>
<body>
    <h2>ログイン</h2>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>メールアドレス:</label><br>
        <input type="email" name="email" required><br><br>

        <label>パスワード:</label><br>
        <input type="password" name="password" required><br><br>

        <label>
            <input type="checkbox" name="remember">
            ログイン状態を保持（30日間）
        </label><br><br>

        <button type="submit">ログイン</button>
    </form>
</body>
</html>
```

**✅ セキュリティポイント**：

- ✅ **HTTPS強制**：本番環境では自動的にHTTPSにリダイレクト
- ✅ **セキュアクッキー**：`secure`フラグでHTTPSのみ送信
- ✅ **HttpOnlyクッキー**：JavaScriptからアクセス不可
- ✅ **SameSite属性**：CSRF対策
- ✅ **HSTSヘッダー**：ブラウザに常にHTTPSを使うよう指示
- ✅ **CSPヘッダー**：XSS対策
- ✅ **記憶トークン**：セキュアなトークン管理

---

### 問題4-10：完全にセキュアなブログシステム - 解答例

すべてのセキュリティ対策を統合した完全なブログシステムの実装です。

この問題は非常に大規模なため、主要な構成要素のみを示します：

**構成**：

1. `config.php` - データベース接続
2. `secure_https_config.php` - HTTPS設定
3. `secure_session.php` - セッション管理
4. `validation_functions.php` - バリデーション
5. `register.php` - ユーザー登録
6. `login.php` - ログイン
7. `dashboard.php` - ダッシュボード
8. `create_post.php` - 記事作成
9. `view_posts.php` - 記事一覧
10. `edit_post.php` - 記事編集
11. `delete_post.php` - 記事削除
12. `logout.php` - ログアウト

**データベーススキーマ**：

```sql
-- ユーザーテーブル
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- カテゴリーテーブル
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ブログ記事テーブル
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_category_id (category_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 記憶トークンテーブル
CREATE TABLE remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**セキュリティチェックリスト**：

✅ **SQLインジェクション対策**

- すべてのクエリでプリペアドステートメントを使用
- パラメータバインドで型を指定

✅ **XSS対策**

- すべての出力で`htmlspecialchars()`を使用
- `ENT_QUOTES`と`UTF-8`を指定

✅ **CSRF対策**

- すべての状態変更操作でCSRFトークンを使用
- `hash_equals()`でトークンを検証
- 処理後にトークンを再生成

✅ **パスワードセキュリティ**

- `password_hash()`でハッシュ化
- `PASSWORD_DEFAULT`を使用
- パスワードポリシー（8文字以上、複雑性）

✅ **セッション管理**

- ログイン時に`session_regenerate_id()`
- セッションタイムアウト（30分）
- User-Agent検証
- セキュアクッキー設定

✅ **入力バリデーション**

- すべての入力を検証
- 型変換と文字数制限
- `filter_var()`と正規表現

✅ **エラーハンドリング**

- `try-catch`で例外処理
- 詳細なエラーをログに記録
- ユーザーにはフレンドリーなメッセージ

✅ **HTTPS設定**

- 本番環境でHTTPS強制
- セキュアクッキー
- HSTSヘッダー
- CSPヘッダー

✅ **データベースセキュリティ**

- 外部キー制約
- インデックス最適化
- ON DELETE CASCADE/RESTRICT

✅ **権限管理**

- ログイン必須ページで認証確認
- 自分の記事のみ編集・削除可能

---

## 🎉 まとめ

お疲れ様でした！バックエンドセキュリティの重要な対策をすべて学びました！

**学んだこと**：

- ✅ SQLインジェクション対策（プリペアドステートメント）
- ✅ XSS対策（htmlspecialchars）
- ✅ CSRF対策（トークン検証）
- ✅ パスワードハッシュ化（password_hash）
- ✅ セッション管理（session_regenerate_id）
- ✅ 入力バリデーション（filter_var, 正規表現）
- ✅ HTTPS設定（セキュアクッキー、HSTS）
- ✅ エラーハンドリング（try-catch、ログ記録）
- ✅ 包括的なセキュリティシステム

**次のステップ**：
👉 **[Lesson 05: ページネーション](../../05-pagination/README.md)** で大量データの効率的な表示を学ぼう！

**Let's vibe and code securely! 🛡️🎉**

セキュリティは最優先！常にセキュアなコードを書こう！
