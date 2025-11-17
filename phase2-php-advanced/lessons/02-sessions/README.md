# Lesson 02: セッション管理 🔐

**学習目標**：セッションの仕組みを理解し、セキュアなログイン機能を実装できるようになる！

---

## 📖 このレッスンで学ぶこと

- セッションの仕組み（クライアント・サーバー間の状態保持）
- セッションの開始と終了（session_start、session_destroy）
- セッション変数の使い方（$_SESSION）
- ログイン機能の実装
- ログアウト機能の実装
- **セキュアなセッション管理**（最重要！） 🔒
  - **session_regenerate_id() の重要性**
  - **セッションハイジャック対策**
  - **セッション固定攻撃対策**
- セッションのタイムアウト処理
- パスワードのハッシュ化（password_hash、password_verify）

---

## 🎯 なぜセッション管理を学ぶの？（Why）

### ログイン機能には必須！

**セッション**は、「会員カード」みたいなもの！

**会員カードの仕組み**：

1. 店に行って、会員登録する
2. 会員カードをもらう
3. 次回から、カードを見せるだけで「会員さん」として扱われる
4. 買い物の履歴や、ポイントが記録される

**セッションの仕組み**：

1. Webサイトでログインする
2. セッションIDが発行される（会員カードのID）
3. 次回アクセス時、セッションIDを送ることで「ログイン済み」として認識される
4. ユーザー情報や、カートの中身が保持される

### HTTPはステートレス = 毎回初対面

Phase 0で学んだ通り、HTTPは基本的に「ステートレス」。つまり、サーバーは前回のやり取りを覚えていない！

**セッションなしの場合**：

```text
1回目：ログイン成功！
2回目：「誰？ログインして」（覚えてない）
3回目：「誰？ログインして」（また覚えてない）
```

毎回ログインが必要で、超不便！😱

**セッションありの場合**：

```text
1回目：ログイン成功！（セッションIDを発行）
2回目：セッションIDで「田中さん、こんにちは」
3回目：セッションIDで「田中さん、こんにちは」
```

一度ログインすれば、ずっとログイン状態を保てる！便利！✨

### バックエンド開発における重要性

セッション管理は、**ユーザー認証の中核**！

でも、**セキュリティリスクも超高い**！

- 🚨 **セッションハイジャック**：他人のセッションIDを盗んで、なりすます
- 🚨 **セッション固定攻撃**：攻撃者がセッションIDを仕掛けて、ユーザーにそのIDでログインさせる
- 🚨 **セッションIDの予測**：予測可能なセッションIDを生成してしまう

だからこそ、**セキュアなセッション管理**を学ぶことが超重要！

---

## 🏗️ セッションの基礎知識（What）

### セッションとは？

**セッション**：サーバー側でユーザーの状態を保持する仕組み

**セッションID**：ユーザーを識別するためのランダムな文字列

```text
例：abc123def456ghi789jkl012mno345pqr678stu901vwx234yz
```

### セッションの流れ

```text
【1回目のアクセス：ログイン】

1. ユーザー：「ログインしたい（ID: taro, パスワード: ****）」
   ↓
2. サーバー：認証OK！セッション開始
   ↓
3. サーバー：ランダムなセッションIDを生成（abc123def...）
   ↓
4. サーバー：セッションIDをCookieでブラウザに送る
   ↓
5. ブラウザ：セッションIDを保存
   ↓
6. サーバー：$_SESSION['user_id'] = 123; // サーバー側に保存
   ↓
7. サーバー：「ログイン成功！」と返す


【2回目以降のアクセス】

1. ユーザー：別のページにアクセス
   ↓
2. ブラウザ：自動的にセッションIDをCookieで送る
   ↓
3. サーバー：セッションIDを確認
   ↓
4. サーバー：$_SESSION['user_id'] を取得 → 123
   ↓
5. サーバー：「あ、田中さんだ！ログイン済み！」
   ↓
6. サーバー：ログイン済みユーザー用のページを返す
```

### セッションとCookieの違い

| 項目           | セッション           | Cookie               |
| -------------- | -------------------- | -------------------- |
| 保存場所       | サーバー側           | クライアント側       |
| セキュリティ   | 高い                 | 低い                 |
| 容量           | 大きい（制限なし）   | 小さい（4KB程度）    |
| 有効期限       | ブラウザを閉じると消える | 設定した期限まで保持 |
| 用途           | ログイン状態、カート | 設定、テーマ         |

**重要**：セッションIDはCookieで送られる！

- セッション自体はサーバー側
- セッションIDはCookieでブラウザに保存される

---

## 💻 セッションの基本操作（How）

### 1. セッションの開始

**超重要**：セッションを使う前に、**必ず `session_start()` を呼ぶ**！

```php
<?php
// セッションを開始（ページの最初で呼ぶ）
session_start();

echo "セッションが開始されました！";
echo "<br>セッションID：" . session_id();
?>
```

**ポイント**：

- `session_start()` は**HTMLやechoの前**に呼ぶ
- 1ページにつき1回だけ呼ぶ
- すでに開始されている場合は、既存のセッションを再開

### 2. セッション変数への保存

```php
<?php
session_start();

// セッション変数に値を保存
$_SESSION['username'] = "太郎";
$_SESSION['user_id'] = 123;
$_SESSION['is_logged_in'] = true;

echo "セッションに保存しました！";
?>
```

**アナロジー**：

- `$_SESSION` = 会員カードに紐づく「データベース」
- サーバー側に保存されるので、ユーザーから見えない

### 3. セッション変数の取得

```php
<?php
session_start();

// セッション変数を取得
if (isset($_SESSION['username'])) {
    echo "ようこそ、" . htmlspecialchars($_SESSION['username']) . "さん！";
} else {
    echo "ログインしていません";
}
?>
```

### 4. セッション変数の削除

**特定の変数だけ削除**：

```php
<?php
session_start();

// 特定のセッション変数を削除
unset($_SESSION['username']);
?>
```

**全セッション変数を削除（ログアウト時）**：

```php
<?php
session_start();

// すべてのセッション変数を削除
$_SESSION = [];

// セッションを完全に破棄
session_destroy();

echo "ログアウトしました";
?>
```

---

## 🔐 セキュアなログイン機能の実装

### 1. ユーザー登録（パスワードのハッシュ化）

```php
<?php
// register.php（ユーザー登録）

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // バリデーション
    if (empty($username) || empty($password)) {
        $error = "ユーザー名とパスワードを入力してください";
    } elseif (strlen($password) < 8) {
        $error = "パスワードは8文字以上にしてください";
    } else {

        // パスワードをハッシュ化（超重要！）
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 🚨 注意：本来はデータベースに保存
        // 今回は演習用にファイルに保存
        $userData = [
            'username' => $username,
            'password' => $hashedPassword
        ];

        // data/users.jsonに保存（簡易版）
        $usersFile = 'data/users.json';

        if (!is_dir('data')) {
            mkdir('data', 0755, true);
        }

        $users = [];
        if (file_exists($usersFile)) {
            $users = json_decode(file_get_contents($usersFile), true);
        }

        // ユーザー名の重複チェック
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                $error = "このユーザー名は既に使用されています";
                break;
            }
        }

        if (!isset($error)) {
            $users[] = $userData;
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $success = "登録完了！ログインしてください。";
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
    <h1>ユーザー登録</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;">❌ <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <p style="color: green;">✅ <?= htmlspecialchars($success) ?></p>
        <p><a href="login.php">ログインページへ</a></p>
    <?php else: ?>
        <form method="POST">
            <label>ユーザー名：</label><br>
            <input type="text" name="username" required><br><br>

            <label>パスワード：</label><br>
            <input type="password" name="password" required><br><br>

            <button type="submit">登録</button>
        </form>

        <p><a href="login.php">ログインページへ</a></p>
    <?php endif; ?>
</body>
</html>
```

**セキュリティポイント**：

```php
// ✅ 安全：password_hash() でハッシュ化
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
// 保存される値：$2y$10$abcdefg... （元のパスワードは復元不可能）

// 🚨 危険：平文で保存（絶対NG！）
$password = $_POST['password'];  // "mypassword123"
// そのまま保存すると、データが漏洩したときに超危険！
```

### 2. ログイン機能（セッション開始）

```php
<?php
// login.php

session_start();

// すでにログイン済みの場合、ダッシュボードへリダイレクト
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // ユーザーデータを読み込む
    $usersFile = 'data/users.json';

    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true);

        // ユーザー名で検索
        $foundUser = null;
        foreach ($users as $index => $user) {
            if ($user['username'] === $username) {
                $foundUser = $user;
                $foundUser['id'] = $index;  // IDを追加
                break;
            }
        }

        if ($foundUser) {
            // パスワードを検証（ハッシュと比較）
            if (password_verify($password, $foundUser['password'])) {

                // 🔒 超重要：セッション固定攻撃対策
                session_regenerate_id(true);

                // セッションにユーザー情報を保存
                $_SESSION['user_id'] = $foundUser['id'];
                $_SESSION['username'] = $foundUser['username'];
                $_SESSION['logged_in_at'] = time();  // ログイン時刻

                // ダッシュボードへリダイレクト
                header('Location: dashboard.php');
                exit;

            } else {
                $error = "パスワードが正しくありません";
            }
        } else {
            $error = "ユーザー名が見つかりません";
        }

    } else {
        $error = "ユーザーデータが見つかりません";
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

    <?php if (isset($error)): ?>
        <p style="color: red;">❌ <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>ユーザー名：</label><br>
        <input type="text" name="username" required><br><br>

        <label>パスワード：</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">ログイン</button>
    </form>

    <p><a href="register.php">新規登録</a></p>
</body>
</html>
```

**最重要セキュリティポイント**：

```php
// 🔒 セッション固定攻撃対策（超重要！）
session_regenerate_id(true);
```

**なぜ必要？**

- ログイン前のセッションIDと、ログイン後のセッションIDを変える
- 攻撃者が事前に仕掛けたセッションIDを無効化する

**攻撃例（session_regenerate_id がない場合）**：

```text
1. 攻撃者：あらかじめセッションIDを取得（例：abc123）
2. 攻撃者：被害者に「このリンクをクリックして」と送る
   → http://example.com/login.php?PHPSESSID=abc123
3. 被害者：そのリンクでログイン（セッションIDはabc123のまま）
4. 攻撃者：同じセッションID（abc123）でアクセス
   → 被害者としてログインできてしまう！
```

**session_regenerate_id() を使うと**：

```text
3. 被害者：ログイン時にセッションIDが新しくなる（abc123 → xyz789）
4. 攻撃者：古いセッションID（abc123）でアクセス
   → 無効なので、ログインできない！
```

### 3. ダッシュボード（ログイン済みユーザーのみアクセス可能）

```php
<?php
// dashboard.php

session_start();

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    // ログインしていない場合、ログインページへリダイレクト
    header('Location: login.php');
    exit;
}

// セッションタイムアウトチェック（30分）
$timeout = 30 * 60;  // 30分（秒単位）
if (isset($_SESSION['logged_in_at'])) {
    $elapsed = time() - $_SESSION['logged_in_at'];
    if ($elapsed > $timeout) {
        // タイムアウト
        session_destroy();
        header('Location: login.php?timeout=1');
        exit;
    }
}

// アクティビティ更新
$_SESSION['logged_in_at'] = time();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ダッシュボード</title>
</head>
<body>
    <h1>ダッシュボード</h1>

    <p>ようこそ、<strong><?= htmlspecialchars($_SESSION['username']) ?></strong>さん！</p>

    <p>セッションID：<?= htmlspecialchars(session_id()) ?></p>
    <p>ログイン時刻：<?= date('Y-m-d H:i:s', $_SESSION['logged_in_at']) ?></p>

    <h2>メニュー</h2>
    <ul>
        <li><a href="profile.php">プロフィール</a></li>
        <li><a href="settings.php">設定</a></li>
        <li><a href="logout.php">ログアウト</a></li>
    </ul>
</body>
</html>
```

### 4. ログアウト機能

```php
<?php
// logout.php

session_start();

// セッション変数を全削除
$_SESSION = [];

// セッションCookieも削除
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// セッションを破棄
session_destroy();

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログアウト</title>
</head>
<body>
    <h1>ログアウトしました</h1>

    <p>✅ ログアウトが完了しました。</p>

    <p><a href="login.php">ログインページへ</a></p>
</body>
</html>
```

---

## 🔒 セキュリティ対策の徹底解説

### 1. session_regenerate_id() を使う（最重要！）

**呼び出すタイミング**：

- ✅ ログイン成功時
- ✅ 権限が変わるとき（一般ユーザー → 管理者）
- ✅ 重要な操作の前

```php
<?php
session_start();

// ログイン処理...

// セッションIDを再生成（セッション固定攻撃対策）
session_regenerate_id(true);

$_SESSION['user_id'] = 123;
?>
```

**引数 `true` の意味**：

- 古いセッションファイルを削除する
- 必ず `true` を指定しよう

### 2. パスワードのハッシュ化

**絶対ルール**：

- ✅ `password_hash()` を使う
- 🚨 平文で保存しない
- 🚨 MD5やSHA1は使わない（脆弱）

**正しい方法**：

```php
<?php
// 登録時
$password = $_POST['password'];
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
// データベースに $hashedPassword を保存

// ログイン時
$inputPassword = $_POST['password'];
$storedHashedPassword = /* データベースから取得 */;

if (password_verify($inputPassword, $storedHashedPassword)) {
    // パスワード正しい
} else {
    // パスワード間違い
}
?>
```

### 3. HTTPSの使用

**重要**：本番環境では必ずHTTPSを使う

- セッションIDがHTTPで送られると、盗聴される危険がある
- HTTPSで暗号化されていれば安全

**php.iniの設定**（本番環境）：

```ini
session.cookie_secure = 1     ; HTTPS通信でのみCookieを送信
session.cookie_httponly = 1   ; JavaScriptからアクセス不可（XSS対策）
session.cookie_samesite = "Strict"  ; CSRF対策
```

### 4. セッションタイムアウト

**一定時間操作がない場合、自動的にログアウト**：

```php
<?php
session_start();

$timeout = 30 * 60;  // 30分

if (isset($_SESSION['last_activity'])) {
    $elapsed = time() - $_SESSION['last_activity'];

    if ($elapsed > $timeout) {
        // タイムアウト
        session_destroy();
        header('Location: login.php?timeout=1');
        exit;
    }
}

// アクティビティ時刻を更新
$_SESSION['last_activity'] = time();
?>
```

---

## 🤖 バイブコーディング実践（最重要セクション！）

### AIへの指示例

#### 良い指示の例1：セキュアなログイン機能

```text
「PHPでログイン機能を実装してください。
以下のセキュリティ対策を含めてください：
- パスワードはpassword_hash()でハッシュ化
- ログイン成功時にsession_regenerate_id(true)を実行
- ログインチェック機能（ログインしていない場合はログインページへリダイレクト）
- ログアウト機能（セッションを完全に破棄）
ユーザーデータはJSONファイル（data/users.json）に保存してください。」
```

**なぜ良い？**

- ✅ セキュリティ要件を具体的に指示
- ✅ 使う関数を明示（password_hash、session_regenerate_id）
- ✅ データ保存方法も指示

#### 良い指示の例2：セッションタイムアウト

```text
「セッションタイムアウト機能を実装してください。
最後のアクティビティから30分経過したら、自動的にログアウトしてログインページへリダイレクトしてください。
$_SESSION['last_activity']に時刻を保存し、毎回time()との差分をチェックしてください。」
```

#### 危険な指示の例（絶対NG）

```text
「ログイン機能を作って。パスワードはそのまま保存して」
```

**なぜ危険？**

- 🚨 パスワードを平文で保存する指示（超危険！）
- 🚨 セキュリティ対策がゼロ

### 生成されたコードのチェックポイント

#### セキュリティチェック（最優先！）

- [ ] **session_regenerate_id()**：ログイン成功時に使われているか
- [ ] **password_hash()**：パスワードがハッシュ化されているか
- [ ] **password_verify()**：ログイン時にハッシュと比較しているか
- [ ] **ログインチェック**：保護されたページで `isset($_SESSION['user_id'])` をチェックしているか
- [ ] **セッション破棄**：ログアウト時に `session_destroy()` を呼んでいるか
- [ ] **XSS対策**：セッション変数を出力する際に `htmlspecialchars()` を使っているか

#### 機能チェック

- [ ] **エラーハンドリング**：ログイン失敗時のエラーメッセージがあるか
- [ ] **リダイレクト**：ログイン成功後、適切なページへリダイレクトしているか
- [ ] **バリデーション**：ユーザー名とパスワードの入力チェックがあるか

### よくある問題と修正方法

#### 問題1：session_regenerate_id() の忘れ

**AIが生成しがちなコード**：

```php
<?php
session_start();

// ログイン成功
$_SESSION['user_id'] = 123;
$_SESSION['username'] = $username;

// session_regenerate_id() が無い！
?>
```

**原因**：セキュリティ意識の不足

**修正**：

```php
<?php
session_start();

// セッションIDを再生成（セッション固定攻撃対策）
session_regenerate_id(true);

$_SESSION['user_id'] = 123;
$_SESSION['username'] = $username;
?>
```

**AIへの修正指示**：

```text
「セッション固定攻撃対策として、ログイン成功時にsession_regenerate_id(true)を呼んでください。」
```

#### 問題2：パスワードの平文保存

**AIが生成しがちな危険なコード**：

```php
<?php
// 🚨 超危険：パスワードを平文で保存
$password = $_POST['password'];
$userData = ['username' => $username, 'password' => $password];
file_put_contents('users.json', json_encode($userData));
?>
```

**原因**：セキュリティ知識の不足

**修正**：

```php
<?php
// ✅ 安全：パスワードをハッシュ化
$password = $_POST['password'];
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$userData = ['username' => $username, 'password' => $hashedPassword];
file_put_contents('users.json', json_encode($userData));
?>
```

**AIへの修正指示**：

```text
「パスワードは絶対に平文で保存しないでください。
password_hash($password, PASSWORD_DEFAULT)でハッシュ化してから保存してください。」
```

---

## 💪 演習

実際に手を動かして、セッション管理をマスターしよう！

演習問題はこちら：
👉 **[演習問題を見る](exercises/README.md)**

---

## ✅ まとめ

### セッションの基本

- ✅ `session_start()` でセッション開始
- ✅ `$_SESSION` にデータを保存
- ✅ `session_destroy()` でセッション破棄

### セキュアなセッション管理（超重要！）

- ✅ **session_regenerate_id(true)**：ログイン時に必ず実行
- ✅ **password_hash()**：パスワードは必ずハッシュ化
- ✅ **password_verify()**：ログイン時にハッシュと比較
- ✅ **ログインチェック**：保護されたページでセッションチェック
- ✅ **セッションタイムアウト**：一定時間後に自動ログアウト

### セッションハイジャック対策

- ✅ HTTPS通信を使う
- ✅ session.cookie_httponly を有効にする
- ✅ session_regenerate_id() を適切に使う

---

## 🚀 次のステップ

セッション管理をマスターしたね！すごい！✨

次は**Lesson 03: Cookie**で：

- Cookieの仕組み
- セッションとCookieの違い
- セキュアなCookie設定（HttpOnly、Secure、SameSite）
- 「Remember Me」機能の実装

を学んでいくよ！

👉 **[Lesson 03: Cookieへ進む](../03-cookies/README.md)**

---

**Let's vibe and code! 🎉**

セッション管理の基礎が身についた！セキュリティ対策も忘れずにね！🔒
