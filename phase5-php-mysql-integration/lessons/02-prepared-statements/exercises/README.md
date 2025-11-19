# Lesson 02: プリペアドステートメント - 演習問題 🔒

プリペアドステートメントをマスターして、SQLインジェクションから守れるようになろう！

---

## 📝 準備

演習を始める前に、データベースとテーブルを準備しよう！

```sql
CREATE DATABASE IF NOT EXISTS phase5_practice CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE phase5_practice;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (name, email) VALUES
('太郎', 'taro@example.com'),
('花子', 'hanako@example.com'),
('次郎', 'jiro@example.com');
```

---

## 🌱 基礎編

### 問題2-1：基本的なSELECT

**課題**：

プリペアドステートメントを使って、ユーザーIDでユーザーを検索するPHPコードを書いてください。

**要件**：

- `prepare()`、`bindParam()`、`execute()`を使う
- 名前付きプレースホルダー（`:id`）を使う
- `fetch()`でデータを1件取得
- ユーザーが見つかったら名前を表示（`htmlspecialchars()`でXSS対策）

**ヒント**：

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
```

---

### 問題2-2：INSERT with プリペアドステートメント

**課題**：

ユーザー登録フォームからデータを受け取り、プリペアドステートメントでデータベースに保存するコードを書いてください。

**要件**：

- フォームから名前とメールアドレスを受け取る（`$_POST`）
- プリペアドステートメントでINSERT
- `lastInsertId()`で挿入したIDを表示

**ヒント**：

```php
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
```

---

### 問題2-3：複数件取得（fetchAll）

**課題**：

メールアドレスのドメイン（`@example.com`）で検索して、該当するユーザーを全件取得するコードを書いてください。

**要件**：

- LIKE句を使う
- `fetchAll()`で複数件取得
- `foreach`で一覧表示
- 各ユーザーの名前とメールアドレスを表示（XSS対策）

**ヒント**：

```php
$domain = '%@example.com';
$stmt = $pdo->prepare("SELECT * FROM users WHERE email LIKE :domain");
```

---

## 🚀 応用編

### 問題2-4：UPDATE with プリペアドステートメント

**課題**：

ユーザー情報（名前とメールアドレス）を更新するコードを書いてください。

**要件**：

- プリペアドステートメントでUPDATE
- WHERE句でユーザーIDを指定（必須！）
- 更新成功時にメッセージを表示

---

### 問題2-5：DELETE with プリペアドステートメント

**課題**：

ユーザーを削除するコードを書いてください。

**要件**：

- プリペアドステートメントでDELETE
- WHERE句でユーザーIDを指定（必須！）
- 削除前に確認メッセージを表示

---

### 問題2-6：疑問符プレースホルダーを使う

**課題**：

問題2-1を疑問符プレースホルダー（`?`）を使って書き換えてください。

**要件**：

- `?`形式のプレースホルダーを使う
- `bindParam()`の第1引数に位置（1, 2, 3...）を指定

**ヒント**：

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bindParam(1, $id, PDO::PARAM_INT);
```

---

## 🛡️ セキュリティチャレンジ

### 問題2-7：脆弱性を修正（重要！）

**課題**：

以下の脆弱なコードをプリペアドステートメントに書き換えてください。

**脆弱なコード**：

```php
<?php
// 🚨 危険！SQLインジェクション脆弱性あり
$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $pdo->query($sql);
$user = $result->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "ようこそ、" . $user['name'] . "さん！";
}
?>
```

**要件**：

- プリペアドステートメントに書き換える
- XSS対策も含める（`htmlspecialchars()`）

---

### 問題2-8：複数の条件でSELECT

**課題**：

名前とメールアドレスの両方で検索するコードを書いてください。

**要件**：

- WHERE句で複数条件（AND）
- プリペアドステートメント使用
- 両方の値をバインド

**ヒント**：

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE name = :name AND email = :email");
```

---

## 💪 総合チャレンジ

### 問題2-9：完全なユーザー検索システム

**課題**：

以下の機能を持つユーザー検索システムを作成してください。

**機能**：

1. 検索フォーム（名前またはメールアドレスで検索）
2. プリペアドステートメントでSELECT
3. 検索結果を一覧表示（`fetchAll()`）
4. 該当するユーザーがいない場合のメッセージ
5. すべてのセキュリティ対策（SQLインジェクション、XSS）

**ヒント**：

- OR句を使って名前またはメールアドレスで検索
- `htmlspecialchars()`でXSS対策

---

## 🤖 バイブコーディングのヒント

### AIへの良い指示例

```text
「ユーザーのメールアドレスで検索するPHPコードを書いてください。

要件：
1. プリペアドステートメントを使う（SQLインジェクション対策）
2. 名前付きプレースホルダー（:email）を使う
3. PDO::PARAM_STRで型指定
4. fetch()でデータを1件取得
5. htmlspecialchars()でXSS対策
6. ユーザーが見つからない場合のメッセージも表示

セキュリティを最優先してください。」
```

### チェックポイント

✅ **プリペアドステートメント**

- [ ] `prepare()`が使われているか
- [ ] プレースホルダー（`:name`または`?`）が使われているか
- [ ] `bindParam()`または`execute()`でデータをバインドしているか

✅ **XSS対策**

- [ ] 出力時に`htmlspecialchars()`が使われているか

✅ **エラーハンドリング**

- [ ] try-catchが使われているか

---

## 💡 よくある問題

### 問題：プレースホルダーにクォートを付けてしまう

**❌ 間違い**：

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ':email'");
```

**✅ 正解**：

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
```

プレースホルダーにクォートは不要！

---

👉 **[解答例を見る](solutions/README.md)**

**Let's vibe and code! 🎉**

セキュアなコードを書いて、SQLインジェクションから守ろう！
