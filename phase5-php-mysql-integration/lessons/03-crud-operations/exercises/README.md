# Lesson 03: CRUD操作の実装 - 演習問題 📝

CRUD（Create、Read、Update、Delete）のすべてを実装して、実用的なアプリケーションを作ろう！

---

## 📝 準備

演習を始める前に、商品管理用のデータベースとテーブルを準備しよう！

```sql
CREATE DATABASE IF NOT EXISTS phase5_practice CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE phase5_practice;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- サンプルデータ
INSERT INTO products (name, description, price, stock) VALUES
('MacBook Pro', '高性能ノートパソコン', 198000.00, 10),
('iPhone 15', '最新スマートフォン', 118800.00, 25),
('iPad Air', 'タブレット端末', 84800.00, 15);
```

---

## 🌱 基礎編

### 問題3-1：Create - 商品登録フォーム

**課題**：

商品を登録するフォームとPHPコードを作成してください。

**要件**：
- HTMLフォーム（商品名、説明、価格、在庫数）
- POSTメソッドで送信
- プリペアドステートメントでINSERT
- バリデーション（空チェック、価格は数値、在庫は整数）
- 登録成功時に`lastInsertId()`で登録IDを表示
- XSS対策（`htmlspecialchars()`）

**ヒント**：

```php
// バリデーション例
if (empty($name) || empty($price)) {
    $errors[] = "商品名と価格は必須です。";
}

if (!is_numeric($price) || $price < 0) {
    $errors[] = "価格は0以上の数値で入力してください。";
}
```

---

### 問題3-2：Read - 商品一覧表示

**課題**：

すべての商品を一覧表示するPHPコードを書いてください。

**要件**：
- すべての商品をSELECT（`fetchAll()`）
- HTMLテーブルで表示
- 各商品の情報（ID、商品名、価格、在庫）を表示
- 価格は3桁区切りで表示（`number_format()`）
- XSS対策
- 商品が0件の場合のメッセージ

**ヒント**：

```php
// 価格を3桁区切りで表示
echo number_format($product['price']) . "円";

// HTMLテーブル例
<table border="1">
    <tr>
        <th>ID</th>
        <th>商品名</th>
        <th>価格</th>
        <th>在庫</th>
    </tr>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo htmlspecialchars($product['id']); ?></td>
            ...
        </tr>
    <?php endforeach; ?>
</table>
```

---

### 問題3-3：Read - 商品詳細ページ

**課題**：

商品IDを指定して、1つの商品の詳細を表示するPHPコードを書いてください。

**要件**：
- URLパラメータ（`?id=1`）で商品IDを受け取る
- プリペアドステートメントでSELECT
- `fetch()`で1件取得
- すべての商品情報を表示（説明、登録日、更新日も含む）
- 商品が見つからない場合のエラーメッセージ
- 一覧ページへの戻るリンク

**ヒント**：

```php
// GETパラメータを取得
$id = $_GET['id'] ?? 0;

// 整数型にキャスト（セキュリティ対策）
$id = (int)$id;

if ($id <= 0) {
    die("不正なIDです。");
}
```

---

## 🚀 応用編

### 問題3-4：Update - 商品編集フォーム

**課題**：

既存の商品情報を編集するフォームとPHPコードを作成してください。

**要件**：
- GETパラメータで商品IDを受け取る
- 現在の商品情報をSELECTして、フォームの初期値として表示
- POSTで編集内容を受け取り、UPDATE
- バリデーション（空チェック、数値チェック）
- `rowCount()`で更新件数を確認
- 更新成功時に一覧ページへリダイレクト（`header()`）

**ヒント**：

```php
// フォームの初期値設定
<input type="text" name="name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>">

// 更新後のリダイレクト
header("Location: list.php");
exit;
```

---

### 問題3-5：Delete - 商品削除機能

**課題**：

商品を削除する機能を実装してください。

**要件**：
- POSTメソッドで削除（GETは禁止！）
- 商品IDを受け取る
- プリペアドステートメントでDELETE
- 削除前に確認メッセージ（JavaScriptの`confirm()`）
- `rowCount()`で削除件数を確認
- 削除成功時に一覧ページへリダイレクト
- CSRFトークンを実装（セキュリティ強化）

**ヒント**：

```php
// 削除確認のJavaScript
<form method="POST" onsubmit="return confirm('本当に削除しますか？');">
    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
    <button type="submit">削除</button>
</form>

// CSRFトークン生成
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
```

---

### 問題3-6：Update - 在庫数の増減

**課題**：

在庫数を増減させる機能を実装してください（在庫管理）。

**要件**：
- 商品IDと増減数を受け取る
- 現在の在庫数を取得
- 新しい在庫数を計算（マイナスにならないようチェック）
- UPDATEで在庫数を更新
- トランザクション処理を使用（重要！）

**ヒント**：

```php
// トランザクション処理
try {
    $pdo->beginTransaction();

    // 現在の在庫を取得（ロック）
    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id FOR UPDATE");
    $stmt->execute([':id' => $id]);
    $currentStock = $stmt->fetchColumn();

    // 新しい在庫数を計算
    $newStock = $currentStock + $change;

    if ($newStock < 0) {
        throw new Exception("在庫が不足しています。");
    }

    // 在庫を更新
    $stmt = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
    $stmt->execute([':stock' => $newStock, ':id' => $id]);

    $pdo->commit();
    echo "在庫を更新しました。";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "エラー: " . htmlspecialchars($e->getMessage());
}
```

---

## 🛡️ セキュリティチャレンジ

### 問題3-7：権限チェックの実装

**課題**：

ログインしているユーザーのみが商品を追加・編集・削除できるように権限チェックを実装してください。

**要件**：
- セッションでログイン状態を管理
- 未ログイン時は操作を禁止
- ログインページへリダイレクト
- `session_regenerate_id()`でセッションハイジャック対策

**ヒント**：

```php
session_start();

// ログインチェック関数
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// 各ページの先頭で呼び出す
requireLogin();
```

---

### 問題3-8：SQLインジェクション攻撃からの防御

**課題**：

以下の脆弱なコードをセキュアに書き換えてください。

**脆弱なコード**：

```php
<?php
// 🚨 危険！SQLインジェクション脆弱性あり
$id = $_GET['id'];
$sql = "DELETE FROM products WHERE id = $id";
$pdo->exec($sql);
echo "削除しました。";
?>
```

**要件**：
- プリペアドステートメントに書き換える
- 入力値の型チェック（整数）
- `rowCount()`で削除件数を確認
- try-catchでエラーハンドリング
- POSTメソッドに変更

---

## 💪 総合チャレンジ

### 問題3-9：完全な商品管理システム

**課題**：

以下の機能を持つ完全な商品管理システムを作成してください。

**機能一覧**：

1. **商品一覧ページ（list.php）**
   - 全商品を表示
   - 各商品に「詳細」「編集」「削除」ボタン
   - 新規登録ボタン

2. **商品登録ページ（create.php）**
   - 商品登録フォーム
   - バリデーション
   - 登録後に一覧へリダイレクト

3. **商品詳細ページ（detail.php）**
   - 1つの商品の詳細表示
   - 編集ボタン
   - 戻るボタン

4. **商品編集ページ（edit.php）**
   - 編集フォーム（初期値あり）
   - バリデーション
   - 更新後に一覧へリダイレクト

5. **商品削除（delete.php）**
   - POST送信で削除
   - 確認メッセージ
   - 削除後に一覧へリダイレクト

**セキュリティ要件**：
- すべてプリペアドステートメント
- すべてXSS対策（`htmlspecialchars()`）
- CSRFトークン実装（削除、編集で）
- バリデーション実施
- エラーハンドリング（try-catch）

**追加機能（オプション）**：
- 検索機能（商品名で部分一致）
- ソート機能（価格順、在庫順）
- ページング（10件ずつ表示）

**ファイル構成例**：

```
/crud-system/
├── config.php          # データベース接続
├── functions.php       # 共通関数（バリデーション、XSS対策など）
├── list.php           # 商品一覧
├── create.php         # 商品登録
├── detail.php         # 商品詳細
├── edit.php           # 商品編集
└── delete.php         # 商品削除
```

---

## 🤖 バイブコーディングのヒント

### AIへの良い指示例

```text
「商品管理システムの商品一覧ページを作成してください。

要件：
1. MySQLのproductsテーブルから全商品を取得
2. プリペアドステートメントを使用（SQLインジェクション対策）
3. HTMLテーブルで表示（ID、商品名、価格、在庫）
4. 価格は3桁区切りで表示（number_format関数）
5. htmlspecialchars()でXSS対策
6. 各商品に「詳細」「編集」「削除」ボタンを追加
7. 商品が0件の場合のメッセージも表示
8. try-catchでエラーハンドリング

セキュリティを最優先してください。」
```

### チェックポイント

✅ **セキュリティチェック**
- [ ] すべてのSQL文でプリペアドステートメント使用
- [ ] すべての出力で`htmlspecialchars()`使用
- [ ] 削除・更新はPOSTメソッド（GETは禁止）
- [ ] CSRFトークンを実装
- [ ] バリデーション実施（型チェック、空チェック）

✅ **CRUD操作チェック**
- [ ] Create：INSERT + `lastInsertId()`
- [ ] Read：SELECT + `fetch()`/`fetchAll()`
- [ ] Update：UPDATE + `rowCount()`
- [ ] Delete：DELETE + 確認メッセージ + `rowCount()`

✅ **ユーザビリティチェック**
- [ ] フォームの初期値設定（編集時）
- [ ] 成功メッセージの表示
- [ ] エラーメッセージの表示
- [ ] リダイレクト処理
- [ ] 戻るボタン・リンク

---

## 💡 よくある問題

### 問題：リダイレクト後に「Headers already sent」エラー

**原因**：`header()`の前に出力がある

**❌ 間違い**：

```php
<?php
echo "処理中...";
header("Location: list.php"); // エラー！
?>
```

**✅ 正解**：

```php
<?php
header("Location: list.php");
exit; // リダイレクト後は必ずexit
?>
```

---

### 問題：フォームの初期値が表示されない

**原因**：XSS対策を忘れている

**❌ 間違い**：

```php
<input type="text" name="name" value="<?php echo $product['name']; ?>">
```

**✅ 正解**：

```php
<input type="text" name="name" value="<?php echo htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
```

---

### 問題：削除がGETメソッドになっている

**原因**：セキュリティリスク（CSRFに脆弱）

**❌ 間違い**：

```php
<a href="delete.php?id=1">削除</a>
```

**✅ 正解**：

```php
<form method="POST" action="delete.php" onsubmit="return confirm('削除しますか？');">
    <input type="hidden" name="id" value="1">
    <button type="submit">削除</button>
</form>
```

---

## 📚 データベース設計のポイント

### AUTO_INCREMENT

```sql
id INT AUTO_INCREMENT PRIMARY KEY
```
→ `lastInsertId()`で新しいIDを取得できる

### DECIMAL型（価格）

```sql
price DECIMAL(10, 2)
```
→ 小数点以下2桁まで正確に保存（浮動小数点型は誤差あり）

### ON UPDATE CURRENT_TIMESTAMP

```sql
updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```
→ 更新時に自動的に更新日時が記録される

---

👉 **[解答例を見る](solutions/README.md)**

**Let's vibe and code! 🎉**

CRUD操作をマスターして、実用的なアプリケーションを作ろう！
