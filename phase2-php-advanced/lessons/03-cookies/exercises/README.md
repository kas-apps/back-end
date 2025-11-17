# Lesson 03: Cookie - 演習問題 💪

Cookieの使い方とセキュアな設定を練習しよう！

---

## 🌱 基礎編

### 演習 03-01: テーマ設定の保存

**課題**：ユーザーが選んだテーマ（light/dark）をCookieに保存し、次回訪問時に適用しよう。

**要件**：

- フォームでテーマを選択（light/dark）
- Cookieに保存（有効期限30日）
- 次回訪問時、Cookieから読み込んで背景色を変更
- セキュリティ：HttpOnly=true、SameSite='Lax'

👉 **[解答例を見る](solutions/README.md#演習-03-01)**

---

### 演習 03-02: 訪問回数カウンター

**課題**：ユーザーの訪問回数をCookieで記録しよう。

**要件**：

- 訪問回数をCookieに保存
- 毎回アクセス時に1増やす
- 「これが〇回目の訪問です」と表示

👉 **[解答例を見る](solutions/README.md#演習-03-02)**

---

## 🚀 応用編

### 演習 03-03: Remember Me機能

**課題**：「次回から自動ログイン」機能を実装しよう。

**要件**：

- ログインフォームに「Remember Me」チェックボックス
- チェックされている場合、トークンを生成してCookieに保存
- 次回訪問時、トークンで自動ログイン
- セキュリティ：HttpOnly=true、Secure=true、SameSite='Lax'

👉 **[解答例を見る](solutions/README.md#演習-03-03)**

---

## 🔒 セキュリティチャレンジ

### 演習 03-04: 脆弱なCookie設定の修正

**課題**：以下の脆弱なコードを修正しよう。

```php
<?php
// 🚨 セキュリティフラグなし
setcookie('session_id', $id);
?>
```

**修正要件**：

- HttpOnlyフラグを設定
- Secureフラグを設定
- SameSite属性を設定

👉 **[解答例を見る](solutions/README.md#演習-03-04)**

---

**Let's vibe and code! 🎉**
