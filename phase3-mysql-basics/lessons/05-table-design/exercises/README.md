# Lesson 05: 基本的なテーブル設計 - 演習問題 💪

テーブル設計、制約、インデックスを練習して、効率的なデータベースをマスターしよう！

---

## 🎯 基礎編：制約とインデックス

### 演習 1-1: PRIMARY KEYとAUTO_INCREMENT

以下の `categories` テーブルを作成してください。

**要件**：

- id：INT型、主キー、AUTO_INCREMENT
- name：VARCHAR(100)型、NOT NULL、UNIQUE
- created_at：TIMESTAMP型、DEFAULT CURRENT_TIMESTAMP

**作成するSQL**：

CREATE TABLE文を書いてください。

---

### 演習 1-2: FOREIGN KEY制約

Lesson 04で作成した `students` テーブルと、新しい `enrollments`（履修登録）テーブルを設計してください。

**studentsテーブル**（既存）：

```sql
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**enrollmentsテーブル**（新規作成）：

**要件**：

- id：INT型、主キー、AUTO_INCREMENT
- student_id：INT型、NOT NULL、外部キー（studentsテーブルのidを参照）
- course_name：VARCHAR(100)型、NOT NULL
- enrolled_at：TIMESTAMP型、DEFAULT CURRENT_TIMESTAMP

**外部キー制約**：親レコード削除時、子レコードも削除（ON DELETE CASCADE）

**作成するSQL**：

CREATE TABLE文を書いてください。

---

### 演習 1-3: UNIQUE制約

以下の `users` テーブルを作成してください。

**要件**：

- id：INT型、主キー、AUTO_INCREMENT
- username：VARCHAR(50)型、NOT NULL、UNIQUE（重複不可）
- email：VARCHAR(255)型、NOT NULL、UNIQUE（重複不可）
- created_at：TIMESTAMP型、DEFAULT CURRENT_TIMESTAMP

**作成するSQL**：

CREATE TABLE文を書いてください。

---

## 🚀 応用編：実践的なテーブル設計

### 演習 2-1: ブログシステムのテーブル設計

ブログシステムの以下の3つのテーブルを設計してください。

**1. usersテーブル**：

- id：INT型、主キー、AUTO_INCREMENT
- name：VARCHAR(100)型、NOT NULL
- email：VARCHAR(255)型、NOT NULL、UNIQUE
- password：VARCHAR(255)型、NOT NULL
- created_at：TIMESTAMP型、DEFAULT CURRENT_TIMESTAMP

**2. postsテーブル**：

- id：INT型、主キー、AUTO_INCREMENT
- user_id：INT型、NOT NULL、外部キー（usersテーブルのidを参照）
- title：VARCHAR(200)型、NOT NULL
- content：TEXT型、NOT NULL
- created_at：TIMESTAMP型、DEFAULT CURRENT_TIMESTAMP
- 外部キー制約：親レコード削除時、子レコードも削除（ON DELETE CASCADE）
- インデックス：user_idとcreated_atにINDEXを付ける

**3. commentsテーブル**：

- id：INT型、主キー、AUTO_INCREMENT
- post_id：INT型、NOT NULL、外部キー（postsテーブルのidを参照）
- user_id：INT型、NOT NULL、外部キー（usersテーブルのidを参照）
- content：TEXT型、NOT NULL
- created_at：TIMESTAMP型、DEFAULT CURRENT_TIMESTAMP
- 外部キー制約：親レコード削除時、子レコードも削除（ON DELETE CASCADE）
- インデックス：post_idにINDEXを付ける

**3つのCREATE TABLE文を書いてください。**

---

### 演習 2-2: ECサイトのテーブル設計

ECサイトの以下のテーブルを設計してください。

**1. categoriesテーブル**：

- id：INT型、主キー、AUTO_INCREMENT
- name：VARCHAR(100)型、NOT NULL、UNIQUE
- created_at：TIMESTAMP型、DEFAULT CURRENT_TIMESTAMP

**2. productsテーブル**：

- id：INT型、主キー、AUTO_INCREMENT
- category_id：INT型、NOT NULL、外部キー（categoriesテーブルのidを参照）
- name：VARCHAR(200)型、NOT NULL
- price：DECIMAL(10, 2)型、NOT NULL
- stock：INT UNSIGNED型、NOT NULL、DEFAULT 0
- is_active：BOOLEAN型、DEFAULT TRUE
- created_at：TIMESTAMP型、DEFAULT CURRENT_TIMESTAMP
- インデックス：category_idとpriceにINDEXを付ける

**2つのCREATE TABLE文を書いてください。**

---

## 💡 チャレンジ問題

### チャレンジ 1: タスク管理システムのテーブル設計

タスク管理システムの以下のテーブルを設計してください。

**1. usersテーブル**：

- id、name、email、password、created_at

**2. projectsテーブル**：

- id、name、description、created_at

**3. tasksテーブル**：

- id、project_id（外部キー）、assigned_user_id（外部キー、usersテーブル）、title、status（ENUM: 'pending', 'in_progress', 'completed'）、due_date、created_at

**要件**：

- 外部キー制約を適切に設定
- 頻繁に検索するカラムにインデックスを付ける
- ON DELETE CASCADEを適切に設定

**3つのCREATE TABLE文を書いてください。**

---

### チャレンジ 2: AIにテーブル設計を依頼

以下の指示をAIに出して、テーブル設計をしてもらってください。

**指示例**：

```text
「MySQLで、SNSのようなシステムのテーブルを設計してください。以下の3つのテーブルを作成してください：

1. usersテーブル：
   - id：INT、主キー、AUTO_INCREMENT
   - username：VARCHAR(50)、NOT NULL、UNIQUE
   - email：VARCHAR(255)、NOT NULL、UNIQUE
   - bio：TEXT（自己紹介）
   - created_at：TIMESTAMP、DEFAULT CURRENT_TIMESTAMP

2. postsテーブル：
   - id：INT、主キー、AUTO_INCREMENT
   - user_id：INT、NOT NULL、外部キー（usersテーブルのidを参照）
   - content：TEXT、NOT NULL
   - created_at：TIMESTAMP、DEFAULT CURRENT_TIMESTAMP
   - 外部キー制約：親レコード削除時、子レコードも削除
   - インデックス：user_idとcreated_atに付ける

3. followsテーブル（フォロー関係）：
   - id：INT、主キー、AUTO_INCREMENT
   - follower_id：INT、NOT NULL、外部キー（usersテーブルのidを参照）
   - following_id：INT、NOT NULL、外部キー（usersテーブルのidを参照）
   - created_at：TIMESTAMP、DEFAULT CURRENT_TIMESTAMP
   - UNIQUE制約：follower_idとfollowing_idの組み合わせが一意
   - インデックス：follower_idとfollowing_idに付ける

文字コードはutf8mb4を使用してください。」
```

**生成されたSQLを確認し、制約とインデックスが適切かチェックしてください。**

---

## 🎓 演習を終えて

お疲れさま！これらの演習を通じて：

- ✅ PRIMARY KEY、FOREIGN KEY、UNIQUE、NOT NULLなどの制約を使えるようになった
- ✅ 1対多の関係をテーブル設計で表現できるようになった
- ✅ インデックスを適切に設定できるようになった
- ✅ 実践的なテーブル設計ができるようになった

**Phase 3（MySQL基礎）の全レッスンを修了しました！**

次は **Phase 4（MySQL発展）** または **Phase 5（PHP+MySQL統合）** に進もう！

👉 **[解答例を見る](solutions/README.md)**

---

**Let's vibe and code! 🎉**
