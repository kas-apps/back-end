# Lesson 02: プリペアドステートメント 🔒

**学習目標**：SQLインジェクション攻撃を理解し、プリペアドステートメントで完璧に防御できるようになる！

## SQLインジェクションとは？

**攻撃者が悪意のあるSQLを埋め込む攻撃**

```php
// 🚨 危険！
$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $pdo->query($sql);
```

攻撃者の入力: `' OR '1'='1`

## プリペアドステートメントで防御

```php
// ✅ 安全！
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
```

👉 **[演習問題を見る](exercises/README.md)**

**Let's vibe and code! 🎉**
