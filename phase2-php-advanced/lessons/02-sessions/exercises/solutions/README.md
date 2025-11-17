# Lesson 02: セッション管理 - 解答例と解説 ✅

演習問題の解答例を載せています。実際のPHPファイルはこのディレクトリ内にあります。

---

## 演習 02-01: セッション変数の基本

### 解答ファイル

- `02-01-session-save.php`
- `02-01-session-show.php`

### ポイント

- `session_start()` は両方のファイルで最初に呼ぶ
- セッション変数は `$_SESSION['key']` で保存・取得
- XSS対策として `htmlspecialchars()` を使う

---

## 演習 02-02: ログインチェック機能

### 解答ファイル

- `02-02-login.php`
- `02-02-protected.php`
- `02-02-logout.php`

### 重要ポイント

**ログイン成功時に必ずsession_regenerate_id()を実行**：

```php
session_regenerate_id(true);
$_SESSION['user_id'] = 1;
```

**保護されたページでログインチェック**：

```php
if (!isset($_SESSION['user_id'])) {
    header('Location: 02-02-login.php');
    exit;
}
```

---

## 演習 02-03: ユーザー登録とログイン機能

### 解答ファイル

- `02-03-register.php`
- `02-03-login.php`
- `02-03-dashboard.php`
- `02-03-logout.php`

### セキュリティポイント

**ユーザー登録時**：

```php
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
```

**ログイン時**：

```php
if (password_verify($inputPassword, $storedHashedPassword)) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
}
```

---

## 演習 02-04: セッションタイムアウト機能

### ポイント

**タイムスタンプの保存と更新**：

```php
$timeout = 15 * 60;  // 15分

if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout) {
        session_destroy();
        header('Location: login.php?timeout=1');
        exit;
    }
}

$_SESSION['last_activity'] = time();
```

---

## 演習 02-05: セッションハイジャック脆弱性の修正

### 修正内容

**修正前**：

```php
// 🚨 session_regenerate_id() が無い
$_SESSION['user_id'] = 1;
```

**修正後**：

```php
// ✅ セッション固定攻撃対策
session_regenerate_id(true);
$_SESSION['user_id'] = 1;
```

---

## 演習 02-06: パスワード平文保存の修正

### 修正内容

**修正前**：

```php
// 🚨 平文で保存
$user = ['username' => $username, 'password' => $password];
```

**修正後**：

```php
// ✅ ハッシュ化して保存
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$user = ['username' => $username, 'password' => $hashedPassword];
```

---

## 演習 02-07: 会員制サイトの構築

### 機能一覧

- ユーザー登録（register.php）
- ログイン（login.php）
- ダッシュボード（dashboard.php）
- プロフィール編集（profile.php）
- ログアウト（logout.php）

### セキュリティチェックリスト

- [x] password_hash() を使用
- [x] session_regenerate_id(true) を使用
- [x] ログインチェック機能
- [x] XSS対策（htmlspecialchars）
- [x] セッションタイムアウト

---

**Let's vibe and code! 🎉**

セッション管理のセキュリティ対策を完璧にマスターしたね！✨
