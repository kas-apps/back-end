# 総合プロジェクト1: ログインシステム 🔐

Phase 2で学んだ内容を統合した、セキュアなログインシステムを構築します！

---

## 🎯 プロジェクト概要

### 機能

- ✅ ユーザー登録（パスワードハッシュ化）
- ✅ ログイン（セッション管理）
- ✅ ログアウト
- ✅ ダッシュボード（ログインユーザーのみアクセス可）
- ✅ セッションタイムアウト（30分）

### セキュリティ対策

- 🔒 **password_hash() / password_verify()**：パスワードのハッシュ化
- 🔒 **session_regenerate_id()**：セッション固定攻撃対策
- 🔒 **htmlspecialchars()**：XSS対策
- 🔒 **CSRFトークン**：フォーム送信の保護
- 🔒 **セッションタイムアウト**：自動ログアウト

---

## 📁 ファイル構成

```text
login-system/
├── README.md              # このファイル
├── index.php             # トップページ
├── register.php          # ユーザー登録
├── login.php             # ログインフォーム
├── dashboard.php         # ダッシュボード（ログイン後）
├── logout.php            # ログアウト処理
├── includes/
│   ├── session.php       # セッション管理関数
│   └── functions.php     # 共通関数
└── data/
    └── users.json        # ユーザーデータ（本来はDB）
```

---

## 🚀 セットアップ

### 1. ファイルの配置

MAMP環境の `htdocs` フォルダ内に配置：

```bash
/Applications/MAMP/htdocs/login-system/
```

### 2. ブラウザでアクセス

```text
http://localhost:8888/login-system/
```

---

## 💡 実装のポイント

### セッション管理（includes/session.php）

```php
<?php
// セッション開始
session_start();

// セッションタイムアウトチェック（30分）
$timeout = 30 * 60;

if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout) {
        session_destroy();
        header('Location: login.php?timeout=1');
        exit;
    }
}

$_SESSION['last_activity'] = time();

// CSRFトークン生成
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
```

### パスワード管理（register.php）

```php
<?php
// パスワードをハッシュ化
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// ユーザーデータ
$user = [
    'id' => uniqid(),
    'username' => $username,
    'email' => $email,
    'password' => $hashedPassword,
    'created_at' => date('Y-m-d H:i:s')
];

// JSONファイルに保存
$users = json_decode(file_get_contents('data/users.json'), true) ?: [];
$users[] = $user;
file_put_contents('data/users.json', json_encode($users, JSON_PRETTY_PRINT));
?>
```

### ログイン処理（login.php）

```php
<?php
if (password_verify($inputPassword, $storedHashedPassword)) {
    // ✅ セッション固定攻撃対策
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['logged_in_at'] = time();

    header('Location: dashboard.php');
    exit;
}
?>
```

### ログインチェック（dashboard.php）

```php
<?php
require_once 'includes/session.php';

// ログインチェック
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
```

---

## 🔒 セキュリティチェックリスト

実装時に必ず確認すること：

- [ ] パスワードは `password_hash()` でハッシュ化されているか
- [ ] ログイン成功時に `session_regenerate_id(true)` を実行しているか
- [ ] ログインチェックがすべての保護ページにあるか
- [ ] `htmlspecialchars()` でXSS対策をしているか
- [ ] CSRFトークンがフォームに含まれているか
- [ ] セッションタイムアウトが実装されているか

---

## 🎨 拡張アイデア

さらに機能を追加してみよう！

- プロフィール編集機能
- パスワード変更機能
- パスワードリセット機能（メール送信）
- Remember Me機能（Cookie使用）
- ユーザー権限管理（一般ユーザー/管理者）
- データベース（MySQL）への移行

---

**Let's vibe and code! 🎉**

このプロジェクトを通じて、セキュアな認証システムの実装を学ぼう！
