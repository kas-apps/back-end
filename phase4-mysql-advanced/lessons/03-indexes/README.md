# Lesson 03: インデックス ⚡

**学習目標**：インデックスを使ってクエリのパフォーマンスを劇的に向上させる方法を理解する！

---

## 📖 このレッスンで学ぶこと

- インデックスとは何か（本の索引のようなもの）
- インデックスの仕組み（検索速度が劇的に向上する理由）
- インデックスの作成（単一カラム、複合インデックス）
- 適切なインデックスの選択（どのカラムにインデックスを作るか）
- EXPLAINでクエリを分析（パフォーマンスを確認）

---

## 🎯 なぜインデックスを学ぶの？（Why）

### アナロジー：本の索引

インデックスは「本の索引」みたいなもの！

**インデックスなし**：

```text
「MySQL」という単語を探したい
→ 1ページ目から順番に全ページを読む（遅い！）
```

**インデックスあり**：

```text
「MySQL」という単語を探したい
→ 索引を見て「35ページ」とわかる
→ 35ページに直接ジャンプ！（速い！）
```

データベースも同じ！インデックスがあると、検索が超高速になる！

---

## 💻 実例で理解しよう（How）

### 準備：大量データで実験

```sql
CREATE DATABASE IF NOT EXISTS test_db CHARACTER SET utf8mb4;
USE test_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 大量データを挿入（シミュレーション）
-- 実際は10万件以上のデータで実験すると効果がわかりやすい
```

### 実例1：インデックスなしの検索（遅い）

```sql
-- emailで検索（インデックスなし）
SELECT * FROM users WHERE email = 'taro@example.com';
```

**問題点**：

- 全行をスキャン（フルテーブルスキャン）
- データが増えると検索が遅くなる

---

### 実例2：インデックスを作成（速い！）

```sql
-- emailカラムにインデックスを作成
CREATE INDEX idx_email ON users(email);

-- 同じ検索を実行
SELECT * FROM users WHERE email = 'taro@example.com';
```

**効果**：

- インデックスを使って高速検索
- データが100万件でも一瞬で見つかる！✨

---

### 実例3：複合インデックス

```sql
-- nameとcreated_atの複合インデックス
CREATE INDEX idx_name_created ON users(name, created_at);

-- このクエリが速くなる
SELECT * FROM users WHERE name = '太郎' AND created_at > '2024-01-01';
```

**ポイント**：

- 複数のカラムを組み合わせたインデックス
- WHERE句でよく使う組み合わせに有効

---

### 実例4：EXPLAINで分析

```sql
-- クエリの実行計画を確認
EXPLAIN SELECT * FROM users WHERE email = 'taro@example.com';
```

**結果の見方**：

- `type: index`：インデックスが使われている✅
- `type: ALL`：フルテーブルスキャン（遅い！）⚠️

---

## 🤖 バイブコーディング実践

### AIへの指示例

```text
「usersテーブルのemailカラムにインデックスを作成してください。
検索速度を向上させたいです。」
```

### チェックポイント

- [ ] 適切なカラムにインデックスが作成されているか
- [ ] 過剰なインデックスになっていないか
- [ ] JOINの結合条件にインデックスがあるか

### よくある問題

**問題**：すべてのカラムにインデックスを作成

**修正**：

- WHERE句、JOIN条件、ORDER BYでよく使うカラムのみ
- インデックスは適度に！

---

## ✅ まとめ

- ✅ インデックスは「本の索引」
- ✅ 検索速度が劇的に向上
- ✅ WHERE句、JOIN、ORDER BYでよく使うカラムに作成
- ✅ EXPLAINで効果を確認

---

## 🚀 次のステップ

次のLesson 04では、**トランザクション**を学ぶよ！

👉 **[Lesson 04: トランザクションへ進む](../04-transactions/README.md)**

---

**Let's vibe and code! 🎉**

インデックスで、超高速なWebアプリを作ろう！
