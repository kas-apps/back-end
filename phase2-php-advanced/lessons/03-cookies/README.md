# Lesson 03: Cookie 🍪

**学習目標**：Cookieの仕組みを理解し、セキュアなCookie設定ができるようになる！

---

## 📖 このレッスンで学ぶこと

- Cookieの仕組み（クライアント側での保存）
- Cookieの設定と取得（setcookie、$_COOKIE）
- セッションとCookieの違い
- 有効期限の管理
- **セキュアなCookie設定**（最重要！） 🔒
  - **HttpOnlyフラグ**
  - **Secureフラグ**
  - **SameSite属性**
- 実用例：「次回から自動ログイン」機能（Remember Me）

---

## 🎯 なぜCookieを学ぶの？（Why）

### Cookieは「スタンプカード」みたいなもの！

**スタンプカードの仕組み**：

- カフェでスタンプカードをもらう
- 次回来店時、カードを見せる
- 「5回目ですね！ドリンク無料です」

**Cookieの仕組み**：

- Webサイトから小さなデータ（Cookie）を受け取る
- ブラウザに保存される
- 次回アクセス時、自動的に送信される
- 「前回の設定を覚えています」

### セッションとの違い

|項目|セッション|Cookie|
|---|---|---|
|保存場所|サーバー側|**クライアント側**|
|セキュリティ|高い|低い（改ざん可能）|
|容量|大きい|小さい（4KB程度）|
|有効期限|ブラウザを閉じると消える|**設定した期限まで保持**|
|用途|ログイン状態、カート|設定、テーマ、Remember Me|

---

## 💻 Cookieの基本操作（How）

### 1. Cookieの設定

```php
<?php
// Cookieを設定（シンプル版）
setcookie('username', 'taro');

echo "Cookieを設定しました！";
?>
```

**重要**：`setcookie()` は**HTMLやechoの前**に呼ぶ！

### 2. Cookieの取得

```php
<?php
// Cookieを取得
if (isset($_COOKIE['username'])) {
    echo "ようこそ、" . htmlspecialchars($_COOKIE['username']) . "さん！";
} else {
    echo "Cookieが設定されていません";
}
?>
```

### 3. Cookieの削除

```php
<?php
// Cookieを削除（有効期限を過去に設定）
setcookie('username', '', time() - 3600);

echo "Cookieを削除しました";
?>
```

### 4. 有効期限の設定

```php
<?php
// 1時間後に期限切れ
setcookie('username', 'taro', time() + 3600);

// 1日後に期限切れ
setcookie('username', 'taro', time() + 86400);

// 30日後に期限切れ
setcookie('username', 'taro', time() + (86400 * 30));
?>
```

---

## 🔒 セキュアなCookie設定（超重要！）

### 完全なsetcookie()の構文

```php
setcookie(
    $name,           // Cookie名
    $value,          // 値
    $expires,        // 有効期限
    $path,           // パス
    $domain,         // ドメイン
    $secure,         // Secureフラグ
    $httponly,       // HttpOnlyフラグ
    $samesite        // SameSite属性（PHP 7.3以降）
);
```

### セキュアな設定例

```php
<?php
// ✅ セキュアなCookie設定
setcookie(
    'user_pref',              // Cookie名
    'dark_mode',              // 値
    time() + (86400 * 30),    // 30日後に期限切れ
    '/',                      // パス（サイト全体）
    '',                       // ドメイン（現在のドメイン）
    true,                     // Secure: HTTPS通信でのみ送信
    true,                     // HttpOnly: JavaScriptからアクセス不可
    'Strict'                  // SameSite: CSRF対策
);
?>
```

### 各フラグの説明

#### HttpOnlyフラグ（XSS対策）

```php
// ✅ HttpOnly = true: JavaScriptからアクセス不可
setcookie('session_id', $id, 0, '/', '', false, true);
```

**XSS攻撃を防ぐ**：

- JavaScriptから `document.cookie` でCookieを読めなくする
- 悪意のあるスクリプトがCookieを盗むのを防ぐ

#### Secureフラグ（盗聴対策）

```php
// ✅ Secure = true: HTTPS通信でのみ送信
setcookie('session_id', $id, 0, '/', '', true, true);
```

**HTTP通信では送信されない**：

- HTTPS（暗号化通信）でのみCookieを送信
- 盗聴されても安全

#### SameSite属性（CSRF対策）

```php
// ✅ SameSite = 'Strict': 最も厳格
setcookie('session_id', $id, 0, '/', '', true, true, 'Strict');

// SameSite = 'Lax': やや緩い（推奨）
setcookie('user_pref', $value, 0, '/', '', true, true, 'Lax');

// SameSite = 'None': 制限なし（Secureと併用必須）
setcookie('tracking', $value, 0, '/', '', true, true, 'None');
```

**CSRF攻撃を防ぐ**：

- `Strict`: 他サイトからのリクエストでは一切送信されない
- `Lax`: リンククリックでは送信される（フォーム送信では送信されない）
- `None`: 制限なし（第三者Cookie）

---

## 🔐 実用例：Remember Me機能

### ログイン時

```php
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    // 認証処理（簡易版）
    if ($username === 'admin' && $password === 'password123') {

        // セッション設定
        session_regenerate_id(true);
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $username;

        // Remember Me がチェックされている場合
        if ($remember) {
            // ランダムなトークンを生成
            $token = bin2hex(random_bytes(32));

            // トークンをファイルに保存（本来はDB）
            $tokens = [];
            if (file_exists('data/tokens.json')) {
                $tokens = json_decode(file_get_contents('data/tokens.json'), true);
            }
            $tokens[$token] = ['user_id' => 1, 'username' => $username];
            file_put_contents('data/tokens.json', json_encode($tokens));

            // セキュアなCookieに保存（30日間有効）
            setcookie(
                'remember_token',
                $token,
                time() + (86400 * 30),
                '/',
                '',
                true,   // Secure
                true,   // HttpOnly
                'Lax'   // SameSite
            );
        }

        header('Location: dashboard.php');
        exit;
    }
}
?>
```

### 自動ログイン処理

```php
<?php
session_start();

// まだログインしていない場合
if (!isset($_SESSION['user_id'])) {

    // Remember Me トークンをチェック
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];

        // トークンを検証
        if (file_exists('data/tokens.json')) {
            $tokens = json_decode(file_get_contents('data/tokens.json'), true);

            if (isset($tokens[$token])) {
                // トークンが有効：自動ログイン
                $user = $tokens[$token];

                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
            }
        }
    }
}
?>
```

---

## 🤖 バイブコーディング実践

### AIへの指示例

**良い指示**：

```text
「ユーザーの選択したテーマカラー（light/dark）をCookieに保存するPHPコードを書いてください。
セキュリティのため、HttpOnly=true、SameSite='Lax'を設定してください。
有効期限は30日間です。」
```

### セキュリティチェック

- [ ] **HttpOnlyフラグ**：JavaScriptからアクセスできないようにする
- [ ] **Secureフラグ**：HTTPS通信でのみ送信（本番環境）
- [ ] **SameSite属性**：CSRF対策
- [ ] **XSS対策**：Cookie値を出力する際に `htmlspecialchars()` を使う

---

## 💪 演習

👉 **[演習問題を見る](exercises/README.md)**

---

## ✅ まとめ

- ✅ Cookieはクライアント側に保存される
- ✅ `setcookie()` で設定、`$_COOKIE` で取得
- ✅ **HttpOnlyフラグ**：XSS対策
- ✅ **Secureフラグ**：HTTPS通信でのみ送信
- ✅ **SameSite属性**：CSRF対策
- ✅ Remember Me機能で自動ログイン

---

## 🚀 次のステップ

👉 **[Lesson 04: エラーハンドリングへ進む](../04-error-handling/README.md)**

---

**Let's vibe and code! 🎉**
