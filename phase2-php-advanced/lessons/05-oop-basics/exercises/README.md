# Lesson 05: OOP基礎 - 演習問題 💪

## 🌱 基礎編

### 演習 05-01: Productクラスの作成

**課題**：商品情報を管理するProductクラスを作ろう。

**要件**：

- プロパティ：name、price、stock（すべてprivate）
- コンストラクタ：name、price、stockを受け取る
- メソッド：getName()、getPrice()、isInStock()

👉 **[解答例を見る](solutions/README.md#演習-05-01)**

---

## 🚀 応用編

### 演習 05-02: Userクラスの作成

**課題**：ユーザー情報とパスワード管理を持つUserクラスを作ろう。

**要件**：

- プロパティ：name、email、password（passwordはprivate）
- コンストラクタ：name、emailを受け取る
- setPassword()：password_hash()でハッシュ化
- verifyPassword()：password_verify()で検証

👉 **[解答例を見る](solutions/README.md#演習-05-02)**

---

## 🔒 セキュリティチャレンジ

### 演習 05-03: カプセル化の修正

**課題**：以下の脆弱なコードを修正しよう。

```php
<?php
class User {
    public $password;  // 🚨 publicは危険
}

$user = new User();
$user->password = "secret123";  // 平文で保存
?>
```

**修正要件**：

- passwordをprivateに変更
- setPassword()メソッドでハッシュ化

👉 **[解答例を見る](solutions/README.md#演習-05-03)**

---

**Let's vibe and code! 🎉**
