# Lesson 04: トランザクション 💼

**学習目標**：トランザクションを使ってデータの整合性を保証する方法を理解する！

---

## 📖 このレッスンで学ぶこと

- トランザクションとは何か（全部成功 or 全部失敗）
- トランザクションの基本操作（BEGIN, COMMIT, ROLLBACK）
- 実用例：銀行の振込処理（原子性の重要性）
- 実用例：ECサイトの注文処理（一貫性の保証）
- トランザクション分離レベル（概要のみ）

---

## 🎯 なぜトランザクションを学ぶの？（Why）

### アナロジー：銀行の振込

トランザクションは「銀行の振込」みたいなもの！

**トランザクションなし**：

```text
ステップ1：太郎さんの口座から1万円を引き落とし
→ 成功！

ステップ2：花子さんの口座に1万円を入金
→ エラー！サーバーがダウン！

結果：太郎さんの1万円が消えた！😱
```

**トランザクションあり**：

```text
ステップ1：太郎さんの口座から1万円を引き落とし
→ 成功！

ステップ2：花子さんの口座に1万円を入金
→ エラー！サーバーがダウン！

→ 全部取り消し（ROLLBACK）
→ 太郎さんの口座は元のまま！✨
```

**全部成功するか、全部失敗するか**、どちらかに保証される！

---

## 💻 実例で理解しよう（How）

### 準備：銀行口座テーブル

```sql
CREATE DATABASE IF NOT EXISTS bank_db CHARACTER SET utf8mb4;
USE bank_db;

CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    balance DECIMAL(10, 2) NOT NULL
);

INSERT INTO accounts (name, balance) VALUES
('太郎', 10000.00),
('花子', 5000.00);
```

---

### 実例1：トランザクションなし（危険！）

```sql
-- 危険：トランザクションなし
UPDATE accounts SET balance = balance - 1000 WHERE name = '太郎';
-- もしここでエラーが起きたら？
UPDATE accounts SET balance = balance + 1000 WHERE name = '花子';
```

**問題点**：

- 途中でエラーが起きると、データが不整合になる
- 太郎さんの1000円が消える可能性

---

### 実例2：トランザクションあり（安全！）

```sql
-- トランザクション開始
START TRANSACTION;

-- 太郎さんから1000円引き落とし
UPDATE accounts SET balance = balance - 1000 WHERE name = '太郎';

-- 花子さんに1000円入金
UPDATE accounts SET balance = balance + 1000 WHERE name = '花子';

-- すべて成功したらコミット（確定）
COMMIT;
```

**効果**：

- 全部成功したら`COMMIT`で確定
- 途中でエラーが起きたら`ROLLBACK`で取り消し
- データの整合性が保たれる！✨

---

### 実例3：エラー時のROLLBACK

```sql
-- トランザクション開始
START TRANSACTION;

-- 太郎さんから1000円引き落とし
UPDATE accounts SET balance = balance - 1000 WHERE name = '太郎';

-- エラーが発生！（残高不足など）
-- 手動でロールバック
ROLLBACK;

-- すべての変更が取り消される
```

---

### 実例4：ECサイトの注文処理

```sql
START TRANSACTION;

-- 1. 在庫を減らす
UPDATE products SET stock = stock - 1 WHERE id = 1;

-- 2. 注文を追加
INSERT INTO orders (product_id, user_id, quantity) VALUES (1, 1, 1);

-- 3. すべて成功したらコミット
COMMIT;

-- エラーが起きたらROLLBACK
-- ROLLBACK;
```

**ポイント**：

- 在庫減少と注文追加は必ずセット
- どちらか片方だけ成功してはいけない
- トランザクションで整合性を保証

---

## 🤖 バイブコーディング実践

### AIへの指示例

```text
「商品の在庫を減らして注文を追加する処理を、トランザクションを使って実装してください。
BEGIN（START TRANSACTION）、COMMIT、ROLLBACKを使ってください。
エラーが起きた場合はROLLBACKで取り消してください。」
```

### チェックポイント

- [ ] BEGINとCOMMITがペアになっているか
- [ ] エラー時のROLLBACK処理があるか
- [ ] 適切な範囲でトランザクションが使われているか

### よくある問題

**問題**：COMMITを忘れる

**修正**：

- 必ずBEGINとCOMMITをペアで書く
- エラー時はROLLBACK

---

## ✅ まとめ

- ✅ トランザクションは「全部成功 or 全部失敗」
- ✅ BEGIN（START TRANSACTION）、COMMIT、ROLLBACK
- ✅ 銀行振込、ECサイト注文処理で必須
- ✅ データの整合性を保証

---

## 🚀 次のステップ

次のLesson 05では、**高度なテーブル設計**を学ぶよ！

👉 **[Lesson 05: 高度なテーブル設計へ進む](../05-advanced-design/README.md)**

---

**Let's vibe and code! 🎉**

トランザクションで、安全なWebアプリを作ろう！
