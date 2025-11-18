# Lesson 04: セキュリティ対策 - 演習問題 🔒

Webアプリケーションの5大セキュリティ対策をすべて実装して、セキュアなシステムを作ろう！

---

## 📝 準備

演習を始める前に、ユーザー管理用のデータベースとテーブルを準備しよう！

```sql
CREATE DATABASE IF NOT EXISTS phase5_security CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE phase5_security;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 🌱 基礎編

### 問題4-1：SQLインジェクション対策

**課題**：

以下の脆弱なコードをプリペアドステートメントを使って修正してください。

**脆弱なコード**：

```php
<?php
// 🚨 危険！SQLインジェクション脆弱性あり
$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = $pdo->query($sql);
$user = $result->fetch();

if ($user) {
    echo "ログイン成功！";
} else {
    echo "ログイン失敗";
}
?>
```

**要件**：
- プリペアドステートメントに書き換える
- `bindParam()`でパラメータをバインド
- try-catchでエラーハンドリング
- パスワードは`password_verify()`で検証（後の問題で実装）

**攻撃例を理解しよう**：

攻撃者が以下を入力すると：
```
username: admin' --
password: （何でも）
```

脆弱なコードでは：
```sql
SELECT * FROM users WHERE username = 'admin' --' AND password = '...'
-- コメントアウトされ、パスワードチェックが無効化される！
```

---

### 問題4-2：XSS対策（クロスサイトスクリプティング）

**課題**：

ユーザーが投稿した内容を表示するページを作成し、XSS対策を実装してください。

**脆弱なコード例**：

```php
<?php
// 🚨 危険！XSS脆弱性あり
$comment = $_POST['comment'];
echo "コメント: " . $comment;
?>
```

**要件**：
- `htmlspecialchars()`を使ってエスケープ
- `ENT_QUOTES`と`UTF-8`を指定
- フォームとPHP処理を1ファイルにまとめる
- 投稿内容をデータベースに保存し、一覧表示

**攻撃例を理解しよう**：

攻撃者が以下を入力すると：
```html
<script>alert('XSS攻撃！');</script>
```

脆弱なコードでは：
- JavaScriptが実行されてしまう！

対策後は：
- `&lt;script&gt;`として表示され、実行されない

---

### 問題4-3：CSRF対策（クロスサイトリクエストフォージェリ）

**課題**：

ユーザー情報を更新するフォームにCSRF対策を実装してください。

**要件**：
- CSRFトークンを生成（`bin2hex(random_bytes(32))`）
- セッションに保存
- フォームにhidden項目として埋め込む
- POSTリクエスト時に`hash_equals()`で検証
- トークンが一致しない場合はエラー

**ヒント**：

```php
// トークン生成
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// フォームに埋め込む
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

// 検証
$token = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'], $token)) {
    die("不正なリクエストです。");
}
```

---

## 🚀 応用編

### 問題4-4：パスワードハッシュ化

**課題**：

ユーザー登録とログイン機能を実装し、パスワードを安全に保存してください。

**要件**：

**登録処理**：
- `password_hash()`でパスワードをハッシュ化
- `PASSWORD_DEFAULT`（bcrypt）を使用
- ハッシュ化されたパスワードをデータベースに保存
- 平文パスワードは絶対に保存しない

**ログイン処理**：
- `password_verify()`でパスワードを検証
- ユーザー名でユーザーを検索
- `password_verify($password, $user['password_hash'])`で検証

**ヒント**：

```php
// 登録時
$password = $_POST['password'];
$password_hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
$stmt->execute([
    ':username' => $username,
    ':email' => $email,
    ':password_hash' => $password_hash
]);

// ログイン時
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute([':username' => $username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    echo "ログイン成功！";
}
```

---

### 問題4-5：セッション管理（セッションハイジャック対策）

**課題**：

ログイン機能にセッションハイジャック対策を実装してください。

**要件**：
- ログイン成功時に`session_regenerate_id(true)`を実行
- セッション固定攻撃を防ぐ
- 定期的にセッションIDを再生成（30分ごと）
- ログアウト機能を実装（`session_destroy()`）

**ヒント**：

```php
// ログイン成功時
session_start();
session_regenerate_id(true); // セッションID再生成

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['last_regenerate'] = time();

// 定期的な再生成
if (time() - $_SESSION['last_regenerate'] > 1800) { // 30分
    session_regenerate_id(true);
    $_SESSION['last_regenerate'] = time();
}

// ログアウト
session_start();
$_SESSION = [];
session_destroy();
```

---

### 問題4-6：入力バリデーション

**課題**：

ユーザー登録フォームに包括的なバリデーションを実装してください。

**要件**：
- ユーザー名：3文字以上、50文字以下、英数字とアンダースコアのみ
- メールアドレス：`filter_var()`で検証
- パスワード：8文字以上、大文字・小文字・数字を含む
- パスワード確認：パスワードと一致
- すべてのエラーメッセージを配列に格納して表示

**ヒント**：

```php
$errors = [];

// ユーザー名検証
if (strlen($username) < 3 || strlen($username) > 50) {
    $errors[] = "ユーザー名は3文字以上50文字以下で入力してください。";
}
if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $errors[] = "ユーザー名は英数字とアンダースコアのみ使用できます。";
}

// メールアドレス検証
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "有効なメールアドレスを入力してください。";
}

// パスワード検証
if (strlen($password) < 8) {
    $errors[] = "パスワードは8文字以上で入力してください。";
}
if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $errors[] = "パスワードは大文字・小文字・数字を含む必要があります。";
}

// パスワード確認
if ($password !== $password_confirm) {
    $errors[] = "パスワードが一致しません。";
}
```

---

## 🛡️ セキュリティチャレンジ

### 問題4-7：5つのセキュリティ対策を統合

**課題**：

以下の5つのセキュリティ対策をすべて実装した、セキュアなユーザー登録・ログインシステムを作成してください。

**5つのセキュリティ対策**：
1. **SQLインジェクション対策**：プリペアドステートメント
2. **XSS対策**：`htmlspecialchars()`
3. **CSRF対策**：CSRFトークン
4. **パスワード保護**：`password_hash()`、`password_verify()`
5. **セッション管理**：`session_regenerate_id()`

**必要なページ**：
- `register.php`：ユーザー登録
- `login.php`：ログイン
- `dashboard.php`：ログイン後のダッシュボード（ログイン必須）
- `logout.php`：ログアウト

**セキュリティチェックリスト**：
- [ ] すべてのSQL文でプリペアドステートメント使用
- [ ] すべての出力で`htmlspecialchars()`使用
- [ ] CSRFトークンを実装（登録、ログイン、更新、削除）
- [ ] パスワードは`password_hash()`でハッシュ化
- [ ] ログイン成功時に`session_regenerate_id()`を実行
- [ ] バリデーション実施
- [ ] try-catchでエラーハンドリング

---

### 問題4-8：セキュリティ脆弱性を発見して修正

**課題**：

以下のコードには複数のセキュリティ脆弱性があります。すべて見つけて修正してください。

**脆弱なコード**：

```php
<?php
// 🚨 複数の脆弱性あり！
$id = $_GET['id'];
$comment = $_POST['comment'];

// データベース接続
$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'root');

// コメント保存
$sql = "INSERT INTO comments (post_id, content) VALUES ($id, '$comment')";
$pdo->exec($sql);

// コメント表示
$sql = "SELECT * FROM comments WHERE post_id = $id";
$result = $pdo->query($sql);

while ($row = $result->fetch()) {
    echo "<p>" . $row['content'] . "</p>";
}

echo "コメントを投稿しました！";
?>
```

**脆弱性リスト**：
1. SQLインジェクション（INSERT文）
2. SQLインジェクション（SELECT文）
3. XSS（出力時）
4. CSRF対策なし
5. エラーハンドリングなし
6. バリデーションなし

**すべて修正してください！**

---

### 問題4-9：HTTPSとセキュアクッキー

**課題**：

セッションクッキーをより安全に設定してください。

**要件**：
- `session_set_cookie_params()`でクッキーを設定
- `httponly`：JavaScriptからアクセス不可（XSS対策）
- `secure`：HTTPS通信のみ（盗聴対策）※本番環境のみ
- `samesite`：CSRF対策

**ヒント**：

```php
// セッション開始前に設定
session_set_cookie_params([
    'lifetime' => 0,        // ブラウザを閉じるまで
    'path' => '/',
    'domain' => '',
    'secure' => true,       // HTTPS必須（本番環境）
    'httponly' => true,     // JavaScriptからアクセス不可
    'samesite' => 'Strict'  // CSRF対策
]);

session_start();
```

---

## 💪 総合チャレンジ

### 問題4-10：完全にセキュアなブログシステム

**課題**：

以下の機能を持つ、セキュアなブログシステムを作成してください。

**機能一覧**：

1. **ユーザー登録**
   - すべてのバリデーション
   - パスワードハッシュ化
   - CSRF対策

2. **ログイン/ログアウト**
   - セッション管理
   - `session_regenerate_id()`
   - CSRF対策

3. **記事投稿**
   - ログインユーザーのみ
   - SQLインジェクション対策
   - XSS対策
   - CSRF対策

4. **記事一覧・詳細**
   - すべての出力でXSS対策
   - ページング（Lesson 05で学習）

5. **記事編集・削除**
   - 自分の記事のみ編集・削除可能
   - CSRF対策
   - 確認メッセージ

**セキュリティ要件**：
- **SQLインジェクション対策**：すべてプリペアドステートメント
- **XSS対策**：すべての出力で`htmlspecialchars()`
- **CSRF対策**：すべてのフォームでトークン検証
- **パスワード保護**：`password_hash()`、`password_verify()`
- **セッション管理**：`session_regenerate_id()`、権限チェック
- **入力バリデーション**：すべての入力を検証
- **エラーハンドリング**：try-catch、ユーザーフレンドリーなエラーメッセージ

**ファイル構成例**：

```
/secure-blog/
├── config.php              # データベース接続
├── functions.php           # 共通関数（セキュリティ関数）
├── register.php           # ユーザー登録
├── login.php              # ログイン
├── logout.php             # ログアウト
├── dashboard.php          # ダッシュボード（ログイン後）
├── post_create.php        # 記事投稿
├── post_list.php          # 記事一覧
├── post_detail.php        # 記事詳細
├── post_edit.php          # 記事編集
└── post_delete.php        # 記事削除
```

---

## 🤖 バイブコーディングのヒント

### AIへの良い指示例

```text
「セキュアなユーザー登録ページを作成してください。

要件：
1. SQLインジェクション対策：プリペアドステートメント使用
2. XSS対策：htmlspecialchars()ですべての出力をエスケープ
3. CSRF対策：CSRFトークンを生成・検証
4. パスワード保護：password_hash()でハッシュ化
5. セッション管理：session_regenerate_id()でセッションハイジャック対策
6. バリデーション：ユーザー名、メールアドレス、パスワードをすべて検証
7. エラーハンドリング：try-catchで例外処理

セキュリティを最優先し、OWASP Top 10の脆弱性をすべて防いでください。」
```

### セキュリティチェックリスト

✅ **SQLインジェクション対策**
- [ ] すべてのSQL文でプリペアドステートメント使用
- [ ] `prepare()`と`bindParam()`または`execute([])`
- [ ] 直接SQL文に変数を埋め込んでいないか

✅ **XSS対策**
- [ ] すべての出力で`htmlspecialchars()`使用
- [ ] `ENT_QUOTES`と`UTF-8`を指定
- [ ] ユーザー入力をそのまま表示していないか

✅ **CSRF対策**
- [ ] CSRFトークンを生成（`bin2hex(random_bytes(32))`）
- [ ] セッションに保存
- [ ] フォームにhidden項目として埋め込む
- [ ] `hash_equals()`で検証

✅ **パスワード保護**
- [ ] `password_hash()`でハッシュ化
- [ ] `PASSWORD_DEFAULT`を使用
- [ ] `password_verify()`で検証
- [ ] 平文パスワードを保存していないか

✅ **セッション管理**
- [ ] ログイン成功時に`session_regenerate_id(true)`
- [ ] 定期的にセッションIDを再生成
- [ ] ログアウト時に`session_destroy()`
- [ ] セキュアクッキー設定（httponly、secure、samesite）

---

## 💡 よくある問題

### 問題：CSRFトークンが一致しない

**原因**：セッションが正しく開始されていない

**解決**：

```php
// セッションを開始してからトークンを生成
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
```

---

### 問題：`password_verify()`が常にfalseを返す

**原因**：パスワードハッシュが正しく保存されていない

**解決**：

```php
// VARCHAR(255)で保存する（60文字では不足）
CREATE TABLE users (
    password_hash VARCHAR(255) NOT NULL
);

// ハッシュ化したパスワードを保存
$password_hash = password_hash($password, PASSWORD_DEFAULT);
```

---

### 問題：セッションが維持されない

**原因**：`session_start()`を呼び忘れている

**解決**：

```php
// すべてのページの先頭で
session_start();
```

---

## 📚 セキュリティのベストプラクティス

### OWASP Top 10（2021）

1. **Broken Access Control**：権限チェックを実装
2. **Cryptographic Failures**：`password_hash()`使用
3. **Injection**：プリペアドステートメント使用
4. **Insecure Design**：セキュリティを設計段階から考慮
5. **Security Misconfiguration**：エラー表示を本番環境では無効化
6. **Vulnerable Components**：PHPとライブラリを最新に保つ
7. **Authentication Failures**：強固なパスワードポリシー
8. **Software and Data Integrity**：CSRFトークン使用
9. **Security Logging Failures**：エラーログを記録
10. **Server-Side Request Forgery**：外部URLへのリクエストを検証

---

👉 **[解答例を見る](solutions/README.md)**

**Let's vibe and code! 🎉**

セキュリティ対策をマスターして、安全なWebアプリケーションを作ろう！
