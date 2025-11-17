# Lesson 03: Cookie - 解答例 ✅

## 演習 03-01: テーマ設定の保存

### ポイント

```php
// セキュアなCookie設定
setcookie('theme', $theme, time() + (86400 * 30), '/', '', false, true, 'Lax');
```

---

## 演習 03-02: 訪問回数カウンター

### ポイント

```php
$count = isset($_COOKIE['visit_count']) ? (int)$_COOKIE['visit_count'] + 1 : 1;
setcookie('visit_count', $count, time() + (86400 * 365));
```

---

## 演習 03-03: Remember Me機能

### セキュリティポイント

- ランダムなトークンを生成：`bin2hex(random_bytes(32))`
- Cookieフラグ：HttpOnly=true、Secure=true、SameSite='Lax'
- トークンは期限付きで保存

---

## 演習 03-04: 脆弱なCookie設定の修正

### 修正後

```php
setcookie(
    'session_id',
    $id,
    0,
    '/',
    '',
    true,    // Secure
    true,    // HttpOnly
    'Strict' // SameSite
);
```

---

**Let's vibe and code! 🎉**
