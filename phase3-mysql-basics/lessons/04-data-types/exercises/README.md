# Lesson 04: データ型とテーブル設計 - 演習問題 💪

データ型の選択を練習して、適切なテーブル設計をマスターしよう！

---

## 🎯 基礎編：データ型の選択

### 演習 1-1: 適切なデータ型を選ぶ

以下のデータに適切なデータ型を選んでください。

1. **ユーザーの年齢**（0〜150歳）
2. **商品の価格**（例：1,234.56円）
3. **商品名**（最大100文字）
4. **商品の説明文**（最大1000文字）
5. **生年月日**（例：1990-05-20）
6. **登録日時**（例：2024-01-15 14:30:00）
7. **在庫数**（0以上の整数）
8. **販売中かどうか**（ON/OFF）

**ヒント**：

- 年齢 → 小さい整数
- 金額 → 正確な小数
- 文字列 → 長さに応じてVARCHARまたはTEXT
- 日付・時刻 → DATE、DATETIME、TIMESTAMP
- ON/OFF → BOOLEAN

---

### 演習 1-2: テーブル作成（データ型指定）

以下の構造の `students` テーブルを作成するCREATE TABLE文を書いてください。

| カラム名   | データ型    | 制約                         |
| ---------- | ----------- | ---------------------------- |
| id         | INT         | 主キー、AUTO_INCREMENT       |
| name       | VARCHAR(100)| NOT NULL                     |
| email      | VARCHAR(255)| NOT NULL、UNIQUE             |
| age        | TINYINT     | UNSIGNED                     |
| gpa        | DECIMAL(3,2)| 例：3.75（最大4.00）         |
| enrolled_date | DATE     | NOT NULL                     |
| created_at | TIMESTAMP   | DEFAULT CURRENT_TIMESTAMP    |

---

## 🚀 応用編：実践的なテーブル設計

### 演習 2-1: ECサイトのproductsテーブル

ECサイトの商品管理システムを作成します。以下の要件を満たす `products` テーブルを設計してください。

**要件**：

- 商品ID（自動採番）
- 商品名（最大200文字、必須）
- 商品説明（長文、任意）
- 価格（正確な金額、必須）
- 在庫数（0以上の整数、必須、デフォルト0）
- 販売中かどうか（ON/OFF、デフォルトON）
- 登録日時（自動設定）

**作成するSQL**：

CREATE TABLE文を書いてください。

---

### 演習 2-2: データ型の間違いを修正

以下のテーブル定義には、データ型の選択に問題があります。問題点を指摘し、修正してください。

```sql
CREATE TABLE users (
    id VARCHAR(255),  -- 問題1
    name TEXT,  -- 問題2
    age VARCHAR(10),  -- 問題3
    balance FLOAT,  -- 問題4
    birth_date VARCHAR(50),  -- 問題5
    is_active VARCHAR(10)  -- 問題6
);
```

**問題点を指摘し、正しいデータ型を提案してください。**

---

## 💡 チャレンジ問題

### チャレンジ 1: ブログシステムのテーブル設計

ブログシステムの `posts` テーブルを設計してください。

**要件**：

- 投稿ID（自動採番）
- ユーザーID（外部キー、※Lesson 05で学ぶので、今回はINT型のみ）
- タイトル（最大200文字、必須）
- 本文（長文、必須）
- 公開済みかどうか（ON/OFF、デフォルトOFF）
- 閲覧数（0以上の整数、デフォルト0）
- 作成日時（自動設定）
- 更新日時（自動設定、更新時に自動更新）

**CREATE TABLE文を書いてください。**

---

### チャレンジ 2: AIにテーブル設計を依頼

以下の指示をAIに出して、テーブル設計をしてもらってください。

**指示例**：

```text
「MySQLで、タスク管理システムのtasksテーブルを設計してください。以下のカラムを含めてください：
- id：INT型、主キー、AUTO_INCREMENT
- title：VARCHAR(200)型、NOT NULL（タスクのタイトル）
- description：TEXT型（タスクの詳細）
- status：ENUM型（'pending', 'in_progress', 'completed'）、デフォルトは'pending'
- priority：TINYINT型、UNSIGNED、デフォルト1（優先度：1-5）
- due_date：DATE型（期限日）
- created_at：TIMESTAMP型、デフォルトCURRENT_TIMESTAMP
- completed_at：TIMESTAMP型、NULL許可

文字コードはutf8mb4を使用してください。」
```

**生成されたSQLを確認し、データ型が適切かチェックしてください。**

---

## 🎓 演習を終えて

お疲れさま！これらの演習を通じて：

- ✅ 数値型（INT、DECIMAL、FLOAT）の使い分けができるようになった
- ✅ 文字列型（VARCHAR、TEXT）の使い分けができるようになった
- ✅ 日付・時刻型（DATE、DATETIME、TIMESTAMP）の使い分けができるようになった
- ✅ 適切なデータ型を選択してテーブルを設計できるようになった

次のステップでは、テーブル間の関係と制約を学んでいくよ！

👉 **[解答例を見る](solutions/README.md)**

---

**Let's vibe and code! 🎉**
