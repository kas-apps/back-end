# Lesson 04: 演習問題の解答例 📚

---

## 問題1：シンプルなフォーム

```php
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8');
    echo "こんにちは、" . $name . "さん！";
}
?>

<form method="POST">
    <label>名前：</label>
    <input type="text" name="name">
    <button type="submit">送信</button>
</form>
```

---

## 問題2：脆弱性を修正

```php
<?php
// 修正版（XSS対策済み）
$comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
echo $comment;  // 安全！
?>
```

---

**Let's vibe and code! 🎉**
