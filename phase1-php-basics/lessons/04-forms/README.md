# Lesson 04: フォームデータの扱い 📝

**学習目標**：HTMLフォームからデータを受け取り、安全に処理できるようになる！

---

## 📖 このレッスンで学ぶこと

- HTMLフォームの基本
- $_GET と $_POST の違い
- フォームデータの受け取り方
- バリデーション（入力チェック）
- **XSS対策（htmlspecialchars）** 🔒
- エラーメッセージの表示

---

## 🎯 なぜフォームデータの扱いを学ぶの？（Why）

### ユーザーとの「対話」ができる！

フォームは、ユーザーからの入力を受け取る窓口！

- お問い合わせフォーム
- ログインフォーム
- ユーザー登録フォーム
- 検索フォーム

**でも、超重要な注意点**：
🚨 **ユーザー入力は「信用できない」** 🚨

悪意のあるユーザーが、変なコードを送り込んでくる可能性がある！
だから、**セキュリティ対策が必須**！

---

## 💾 フォームの基本（What）

### HTMLフォームの作成

```html
<form method="POST" action="process.php">
    <label>名前：</label>
    <input type="text" name="username">

    <label>メールアドレス：</label>
    <input type="email" name="email">

    <button type="submit">送信</button>
</form>
```

### PHPでデータを受け取る

```php
<?php
// POSTメソッドで送信されたデータを受け取る
$username = $_POST['username'];
$email = $_POST['email'];

// データがあるかチェック
if (isset($_POST['username'])) {
    echo "ようこそ、" . htmlspecialchars($username) . "さん！";
}
?>
```

### XSS対策：htmlspecialchars()

**超重要！** ユーザー入力を出力するときは、**必ず** `htmlspecialchars()` を使う！

```php
<?php
// 悪い例（XSS脆弱性あり）🚨
$name = $_POST['name'];
echo $name;  // 危険！

// 良い例（XSS対策済み）✅
$name = $_POST['name'];
echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8');  // 安全！
?>
```

**なぜ必要？**

悪意のあるユーザーが以下を入力したら：
```
<script>alert('攻撃！')</script>
```

対策なしだと、JavaScriptが実行されてしまう！
`htmlspecialchars()` を使えば、HTMLタグが無効化されて安全！

---

## 🤖 バイブコーディング実践

### AIへの指示例

```text
「名前とメールアドレスを受け取るフォームを作ってください。
POSTメソッドを使い、受け取ったデータをhtmlspecialchars()でエスケープして表示してください。
バリデーション（必須チェック、メール形式チェック）も含めてください。」
```

### セキュリティチェック（最重要！）

- [ ] **XSS対策**：htmlspecialchars()が使われているか
- [ ] ユーザー入力を直接出力していないか
- [ ] バリデーションがあるか

---

## 💪 演習

演習問題はこちら：
👉 **[演習問題を見る](exercises/README.md)**

---

## ✅ まとめ

- ✅ $_POST/$_GETでフォームデータを受け取れる
- ✅ **htmlspecialchars()でXSS対策が必須**
- ✅ バリデーションで入力チェック
- ✅ セキュリティを最優先に考える

---

## 🚀 次のステップ

Phase 1が完了！次は総合プロジェクトに挑戦しよう！

👉 **[mini-calculatorプロジェクトを見る](../../projects/mini-calculator/README.md)**

---

**Let's vibe and code! 🎉**
