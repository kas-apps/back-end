# Lesson 03: SQL基本操作（CRUD） 📝

**学習目標**：データベースの基本操作「CRUD」をマスターして、データの作成・取得・更新・削除ができるようになる！

---

## 📖 このレッスンで学ぶこと

- CRUDとは何か
- **CREATE**：テーブルの作成（CREATE TABLE）
- **INSERT**：データの挿入（INSERT INTO）
- **SELECT**：データの取得（SELECT）
  - 全件取得、条件指定（WHERE）、並び替え（ORDER BY）、件数制限（LIMIT）
- **UPDATE**：データの更新（UPDATE）
- **DELETE**：データの削除（DELETE）
- **WHERE句の重要性**（SQLインジェクション対策、誤操作防止）🔒
- **セキュリティ意識**：Phase 5でプリペアドステートメントを学ぶ準備

---

## 🎯 なぜCRUDを学ぶの？（Why）

### CRUDはデータベース操作の基本中の基本！

**CRUD**は、データベース操作の4つの基本動作の頭文字！

| 操作   | SQL      | 意味                       | 例                     |
| ------ | -------- | -------------------------- | ---------------------- |
| Create | INSERT   | データを作る（追加する）   | 新しいユーザーを登録   |
| Read   | SELECT   | データを読む（取得する）   | ユーザー一覧を表示     |
| Update | UPDATE   | データを更新する（変更する）| ユーザー情報を変更     |
| Delete | DELETE   | データを削除する（消す）   | ユーザーを削除         |

**Webアプリケーションのほぼ全て**がCRUDで成り立っている！

### 実際の使用例

**ブログシステム**：

- **Create**：新しい記事を投稿
- **Read**：記事一覧を表示、記事を読む
- **Update**：記事を編集
- **Delete**：記事を削除

**ECサイト**：

- **Create**：新しい商品を登録
- **Read**：商品一覧を表示、商品詳細を見る
- **Update**：商品情報を更新（価格変更、在庫更新）
- **Delete**：販売終了した商品を削除

**SNS**：

- **Create**：投稿する、コメントする
- **Read**：タイムラインを表示、コメントを読む
- **Update**：投稿を編集
- **Delete**：投稿を削除

### バックエンド開発における重要性

CRUDは、**バックエンド開発の最重要スキル**！

- 📊 **データの永続化**：ユーザーの情報、投稿、商品データを保存・管理
- 🔍 **データの検索**：条件に合ったデータを素早く取得
- 🔒 **セキュリティ**：WHERE句、プリペアドステートメント（Phase 5で学ぶ）
- 🚀 **パフォーマンス**：適切なクエリで高速化

---

## 🏗️ CRUDの基礎知識（What）

### SQLとは？

**SQL（Structured Query Language）**は、データベースを操作するための言語！

**アナロジー：図書館司書への指示**

- **SQL = 司書への指示書**
- **データベース = 図書館**
- **テーブル = 本棚**
- **レコード = 本**

```text
「歴史コーナー（テーブル）から、
著者が『山田太郎』（WHERE条件）の本（レコード）を
3冊（LIMIT）持ってきてください」

→ これがSELECT文！
```

### SQLの基本ルール

1. **セミコロン（;）で終わる**：SQL文の終わりに `;` を付ける
2. **大文字・小文字**：SQLキーワードは大文字が慣習（SELECT、INSERT など）、テーブル名やカラム名は小文字
3. **文字列はシングルクォート**：`'山田太郎'` のように囲む
4. **コメント**：`-- コメント` または `/* コメント */`

### セキュリティ意識

**⚠️ 超重要**：

このレッスンでは、SQLの基本構文を学ぶよ。でも、**Phase 5ではプリペアドステートメント**を学んで、**SQLインジェクション対策**をする必要がある！

今は基本構文を理解することに集中しよう。Phase 5でPHPと組み合わせる時に、セキュアな書き方を学ぶからね！

---

## 💻 CREATE：テーブルの作成（How）

### CREATE TABLEの基本構文

```sql
CREATE TABLE テーブル名 (
    カラム名1 データ型 制約,
    カラム名2 データ型 制約,
    ...
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 実例1：usersテーブルの作成

```sql
-- usersテーブルを作成
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- ユーザーID（主キー、自動増分）
    name VARCHAR(100) NOT NULL,  -- ユーザー名（必須）
    email VARCHAR(255) NOT NULL UNIQUE,  -- メールアドレス（必須、一意）
    age INT,  -- 年齢（任意）
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- 登録日時（自動設定）
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**ポイント**：

- `AUTO_INCREMENT`：idが自動で1, 2, 3...と増える
- `PRIMARY KEY`：主キー（一意に識別）
- `NOT NULL`：必須項目（NULLを許可しない）
- `UNIQUE`：重複を許可しない（emailは1つだけ）
- `DEFAULT CURRENT_TIMESTAMP`：現在時刻を自動設定

### 実例2：postsテーブルの作成

```sql
-- postsテーブルを作成
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- 投稿ID
    user_id INT NOT NULL,  -- ユーザーID（外部キー）
    title VARCHAR(200) NOT NULL,  -- タイトル
    content TEXT NOT NULL,  -- 本文
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- 投稿日時
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  -- 更新日時
    FOREIGN KEY (user_id) REFERENCES users(id)  -- 外部キー制約
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**ポイント**：

- `FOREIGN KEY`：外部キー制約（usersテーブルのidを参照）
- `ON UPDATE CURRENT_TIMESTAMP`：更新時に自動で現在時刻を設定

---

## ✍️ INSERT：データの挿入

### INSERT INTOの基本構文

```sql
INSERT INTO テーブル名 (カラム1, カラム2, ...) VALUES (値1, 値2, ...);
```

### 実例1：1件のデータを挿入

```sql
-- usersテーブルにデータを挿入
INSERT INTO users (name, email, age) VALUES ('山田太郎', 'taro@example.com', 25);
```

**ポイント**：

- `id` と `created_at` は自動で設定されるので指定不要
- 文字列はシングルクォート `'...'` で囲む
- 数値はクォートなし

### 実例2：複数のデータを一度に挿入

```sql
-- 複数のユーザーを一度に挿入
INSERT INTO users (name, email, age) VALUES
('佐藤花子', 'hanako@example.com', 30),
('鈴木一郎', 'ichiro@example.com', 28),
('田中次郎', 'jiro@example.com', 35),
('高橋美咲', 'misaki@example.com', 22);
```

**ポイント**：

- カンマで区切って複数行を指定
- 一度に挿入する方が効率的

### 実例3：投稿データの挿入

```sql
-- postsテーブルにデータを挿入
INSERT INTO posts (user_id, title, content) VALUES
(1, '初めての投稿', 'こんにちは！ブログを始めました。'),
(1, 'MySQL学習中', 'SQLって楽しいですね！'),
(2, '花子の日記', '今日はいい天気でした。'),
(3, 'プログラミング入門', 'PHPを勉強中です。');
```

**ポイント**：

- `user_id` で、どのユーザーの投稿かを紐づけ
- 外部キー制約により、存在しないuser_idは挿入できない

---

## 🔍 SELECT：データの取得

### SELECTの基本構文

```sql
SELECT カラム1, カラム2, ... FROM テーブル名;
```

### 実例1：全件取得

```sql
-- usersテーブルの全データを取得
SELECT * FROM users;
```

**実行結果**：

| id  | name     | email                | age | created_at          |
| --- | -------- | -------------------- | --- | ------------------- |
| 1   | 山田太郎 | taro@example.com     | 25  | 2024-01-01 10:00:00 |
| 2   | 佐藤花子 | hanako@example.com   | 30  | 2024-01-01 10:01:00 |
| 3   | 鈴木一郎 | ichiro@example.com   | 28  | 2024-01-01 10:02:00 |
| 4   | 田中次郎 | jiro@example.com     | 35  | 2024-01-01 10:03:00 |
| 5   | 高橋美咲 | misaki@example.com   | 22  | 2024-01-01 10:04:00 |

**ポイント**：

- `*` は全カラムを意味する
- 全データが表示される

### 実例2：特定のカラムだけ取得

```sql
-- 名前とメールアドレスだけを取得
SELECT name, email FROM users;
```

**実行結果**：

| name     | email                |
| -------- | -------------------- |
| 山田太郎 | taro@example.com     |
| 佐藤花子 | hanako@example.com   |
| 鈴木一郎 | ichiro@example.com   |
| 田中次郎 | jiro@example.com     |
| 高橋美咲 | misaki@example.com   |

**ポイント**：

- 必要なカラムだけを指定すると、パフォーマンスが向上
- `*` よりも具体的なカラム名を指定する方が良い

### 実例3：WHERE句で条件指定

```sql
-- 年齢が30歳以上のユーザーを取得
SELECT * FROM users WHERE age >= 30;
```

**実行結果**：

| id  | name     | email              | age | created_at          |
| --- | -------- | ------------------ | --- | ------------------- |
| 2   | 佐藤花子 | hanako@example.com | 30  | 2024-01-01 10:01:00 |
| 4   | 田中次郎 | jiro@example.com   | 35  | 2024-01-01 10:03:00 |

**WHERE句の条件演算子**：

| 演算子 | 意味               | 例                      |
| ------ | ------------------ | ----------------------- |
| =      | 等しい             | age = 30                |
| !=     | 等しくない         | age != 30               |
| >      | より大きい         | age > 30                |
| <      | より小さい         | age < 30                |
| >=     | 以上               | age >= 30               |
| <=     | 以下               | age <= 30               |
| LIKE   | パターンマッチ     | name LIKE '田中%'       |
| IN     | リスト内に含まれる | age IN (25, 30, 35)     |
| BETWEEN| 範囲内             | age BETWEEN 20 AND 30   |

### 実例4：複数の条件（AND、OR）

```sql
-- 年齢が25歳以上30歳以下のユーザーを取得
SELECT * FROM users WHERE age >= 25 AND age <= 30;

-- または、BETWEENを使う
SELECT * FROM users WHERE age BETWEEN 25 AND 30;
```

**実行結果**：

| id  | name     | email              | age | created_at          |
| --- | -------- | ------------------ | --- | ------------------- |
| 1   | 山田太郎 | taro@example.com   | 25  | 2024-01-01 10:00:00 |
| 2   | 佐藤花子 | hanako@example.com | 30  | 2024-01-01 10:01:00 |
| 3   | 鈴木一郎 | ichiro@example.com | 28  | 2024-01-01 10:02:00 |

```sql
-- 年齢が25歳、または30歳のユーザーを取得
SELECT * FROM users WHERE age = 25 OR age = 30;

-- または、INを使う
SELECT * FROM users WHERE age IN (25, 30);
```

### 実例5：LIKE（部分一致検索）

```sql
-- 名前に「田」が含まれるユーザーを取得
SELECT * FROM users WHERE name LIKE '%田%';
```

**実行結果**：

| id  | name     | email            | age | created_at          |
| --- | -------- | ---------------- | --- | ------------------- |
| 1   | 山田太郎 | taro@example.com | 25  | 2024-01-01 10:00:00 |
| 4   | 田中次郎 | jiro@example.com | 35  | 2024-01-01 10:03:00 |

**LIKEのパターン**：

| パターン   | 意味                               | 例                  |
| ---------- | ---------------------------------- | ------------------- |
| `'田%'`    | 「田」で始まる                     | 田中、田村          |
| `'%田'`    | 「田」で終わる                     | 山田、本田          |
| `'%田%'`   | 「田」が含まれる                   | 山田、田中、本田    |
| `'田_'`    | 「田」+ 任意の1文字                | 田中（2文字）       |

### 実例6：ORDER BY（並び替え）

```sql
-- 年齢の昇順（若い順）で取得
SELECT * FROM users ORDER BY age ASC;

-- 年齢の降順（年上順）で取得
SELECT * FROM users ORDER BY age DESC;
```

**ORDER BY のオプション**：

| オプション | 意味     |
| ---------- | -------- |
| ASC        | 昇順     |
| DESC       | 降順     |

### 実例7：LIMIT（件数制限）

```sql
-- 最初の3件だけ取得
SELECT * FROM users LIMIT 3;

-- 2件目から3件を取得（OFFSET）
SELECT * FROM users LIMIT 3 OFFSET 1;
```

**実行結果（LIMIT 3）**：

| id  | name     | email              | age | created_at          |
| --- | -------- | ------------------ | --- | ------------------- |
| 1   | 山田太郎 | taro@example.com   | 25  | 2024-01-01 10:00:00 |
| 2   | 佐藤花子 | hanako@example.com | 30  | 2024-01-01 10:01:00 |
| 3   | 鈴木一郎 | ichiro@example.com | 28  | 2024-01-01 10:02:00 |

**ポイント**：

- ページネーション（ページ分け）に使う
- `OFFSET`：スキップする件数

### 実例8：組み合わせ（WHERE + ORDER BY + LIMIT）

```sql
-- 年齢が25歳以上のユーザーを、年齢の昇順で3件取得
SELECT * FROM users WHERE age >= 25 ORDER BY age ASC LIMIT 3;
```

**実行結果**：

| id  | name     | email              | age | created_at          |
| --- | -------- | ------------------ | --- | ------------------- |
| 1   | 山田太郎 | taro@example.com   | 25  | 2024-01-01 10:00:00 |
| 3   | 鈴木一郎 | ichiro@example.com | 28  | 2024-01-01 10:02:00 |
| 2   | 佐藤花子 | hanako@example.com | 30  | 2024-01-01 10:01:00 |

---

## 🔄 UPDATE：データの更新

### UPDATEの基本構文

```sql
UPDATE テーブル名 SET カラム1 = 値1, カラム2 = 値2 WHERE 条件;
```

**⚠️ 超重要**：`WHERE` 句を忘れると、全てのレコードが更新される！

### 実例1：1件のデータを更新

```sql
-- id=1のユーザーの年齢を26に更新
UPDATE users SET age = 26 WHERE id = 1;
```

**実行後の状態**：

| id  | name     | email            | age | created_at          |
| --- | -------- | ---------------- | --- | ------------------- |
| 1   | 山田太郎 | taro@example.com | 26  | 2024-01-01 10:00:00 |

**ポイント**：

- `WHERE id = 1` で、id=1のレコードだけを更新
- WHERE句を忘れると全員の年齢が26になる！

### 実例2：複数のカラムを更新

```sql
-- id=2のユーザーの名前とメールアドレスを更新
UPDATE users SET name = '佐藤桜', email = 'sakura@example.com' WHERE id = 2;
```

**実行後の状態**：

| id  | name   | email                | age | created_at          |
| --- | ------ | -------------------- | --- | ------------------- |
| 2   | 佐藤桜 | sakura@example.com   | 30  | 2024-01-01 10:01:00 |

### 実例3：条件に合う複数のレコードを更新

```sql
-- 年齢が30歳以上のユーザー全員の年齢を+1
UPDATE users SET age = age + 1 WHERE age >= 30;
```

**実行後の状態**：

| id  | name     | email              | age | created_at          |
| --- | -------- | ------------------ | --- | ------------------- |
| 2   | 佐藤桜   | sakura@example.com | 31  | 2024-01-01 10:01:00 |
| 4   | 田中次郎 | jiro@example.com   | 36  | 2024-01-01 10:03:00 |

**ポイント**：

- `age = age + 1` で、現在の年齢に1を足す
- WHERE句で条件指定すれば、複数レコードを一度に更新できる

### ⚠️ 危険な例：WHERE句なし

```sql
-- 🚨 危険：全てのユーザーの年齢が0になる
UPDATE users SET age = 0;
```

**実行すると、全員の年齢が0になる！**

**教訓**：

- **必ずWHERE句を付ける**
- 本番環境では取り返しがつかない！
- Phase 5で学ぶプリペアドステートメントでも、WHERE句は必須

---

## 🗑️ DELETE：データの削除

### DELETEの基本構文

```sql
DELETE FROM テーブル名 WHERE 条件;
```

**⚠️ 超重要**：`WHERE` 句を忘れると、全てのレコードが削除される！

### 実例1：1件のデータを削除

```sql
-- id=5のユーザーを削除
DELETE FROM users WHERE id = 5;
```

**実行後の状態**：

| id  | name     | email              | age | created_at          |
| --- | -------- | ------------------ | --- | ------------------- |
| 1   | 山田太郎 | taro@example.com   | 26  | 2024-01-01 10:00:00 |
| 2   | 佐藤桜   | sakura@example.com | 31  | 2024-01-01 10:01:00 |
| 3   | 鈴木一郎 | ichiro@example.com | 28  | 2024-01-01 10:02:00 |
| 4   | 田中次郎 | jiro@example.com   | 36  | 2024-01-01 10:03:00 |

**ポイント**：

- id=5のレコードが削除された
- **削除は取り消せない**

### 実例2：条件に合う複数のレコードを削除

```sql
-- 年齢が35歳以上のユーザーを削除
DELETE FROM users WHERE age >= 35;
```

**実行後の状態**：

| id  | name     | email              | age | created_at          |
| --- | -------- | ------------------ | --- | ------------------- |
| 1   | 山田太郎 | taro@example.com   | 26  | 2024-01-01 10:00:00 |
| 2   | 佐藤桜   | sakura@example.com | 31  | 2024-01-01 10:01:00 |
| 3   | 鈴木一郎 | ichiro@example.com | 28  | 2024-01-01 10:02:00 |

**ポイント**：

- WHERE句で条件指定すれば、複数レコードを一度に削除できる

### ⚠️ 危険な例：WHERE句なし

```sql
-- 🚨 超危険：全てのユーザーが削除される
DELETE FROM users;
```

**実行すると、全てのデータが消える！**

**教訓**：

- **必ずWHERE句を付ける**
- 削除前にSELECTで確認する習慣をつける
- 本番環境では絶対にWHERE句なしで実行しない

---

## 🔒 WHERE句の重要性とセキュリティ意識

### WHERE句は「防弾チョッキ」

**WHERE句**は、データベースを守る「防弾チョッキ」みたいなもの！

**WHERE句なしのUPDATE/DELETE**：

```sql
-- 🚨 全てのユーザーが削除される
DELETE FROM users;

-- 🚨 全てのユーザーの年齢が0になる
UPDATE users SET age = 0;
```

**WHERE句ありのUPDATE/DELETE**：

```sql
-- ✅ id=1のユーザーだけ削除
DELETE FROM users WHERE id = 1;

-- ✅ id=1のユーザーの年齢だけ更新
UPDATE users SET age = 26 WHERE id = 1;
```

### 実行前にSELECTで確認する

**ベストプラクティス**：

1. **まずSELECTで確認**：どのレコードが対象か確認
2. **WHERE句を同じにしてUPDATE/DELETE**：安全に実行

**例**：

```sql
-- ステップ1：まず対象を確認
SELECT * FROM users WHERE age >= 35;

-- ステップ2：問題なければ削除
DELETE FROM users WHERE age >= 35;
```

### Phase 5で学ぶセキュリティ対策

このレッスンでは、SQLの基本構文を学んだよ。

**Phase 5（PHP+MySQL統合）**では：

- **プリペアドステートメント**：SQLインジェクション対策
- **パラメータバインディング**：ユーザー入力を安全に扱う
- **エスケープ処理**：特殊文字の処理

を学んで、**セキュアなバックエンド開発**をマスターする！

**今は基本を理解することに集中して、Phase 5でセキュリティを強化しよう！**

---

## 🤖 バイブコーディング実践（最重要セクション！）

### AIへの指示例

#### 良い指示の例1：テーブル作成のSQL生成

```text
「MySQLで、ブログシステムのusersテーブルを作成するCREATE TABLE文を書いてください。
以下のカラムを含めてください：
- id：INT型、主キー、AUTO_INCREMENT
- name：VARCHAR(100)型、NOT NULL
- email：VARCHAR(255)型、NOT NULL、UNIQUE
- password：VARCHAR(255)型、NOT NULL（ハッシュ化されたパスワードを保存）
- created_at：TIMESTAMP型、デフォルトはCURRENT_TIMESTAMP

文字コードはutf8mb4を使用してください。」
```

**AIが生成するSQL**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**なぜ良い？**

- ✅ カラムの詳細（型、制約）を明示
- ✅ セキュリティ意識（passwordはハッシュ化を前提）
- ✅ 文字コードも指定

#### 良い指示の例2：データ挿入のSQL生成

```text
「usersテーブルに、以下のテストデータを挿入するINSERT文を書いてください：
- name: '山田太郎', email: 'taro@example.com', password: 'hashed_password_1'
- name: '佐藤花子', email: 'hanako@example.com', password: 'hashed_password_2'
- name: '鈴木一郎', email: 'ichiro@example.com', password: 'hashed_password_3'

複数行を一度に挿入する形式でお願いします。
※ passwordは実際にはPHPのpassword_hash()でハッシュ化しますが、今回はテストデータなので仮の値で構いません。」
```

**AIが生成するSQL**：

```sql
INSERT INTO users (name, email, password) VALUES
('山田太郎', 'taro@example.com', 'hashed_password_1'),
('佐藤花子', 'hanako@example.com', 'hashed_password_2'),
('鈴木一郎', 'ichiro@example.com', 'hashed_password_3');
```

#### 良い指示の例3：検索クエリの生成

```text
「usersテーブルから、以下の条件でユーザーを検索するSELECT文を書いてください：
- メールアドレスが 'taro@example.com' のユーザー
- 取得するカラム：id, name, email, created_at

※ Phase 5でPHPと組み合わせる際は、プリペアドステートメントを使う前提です。」
```

**AIが生成するSQL**：

```sql
SELECT id, name, email, created_at FROM users WHERE email = 'taro@example.com';
```

**なぜ良い？**

- ✅ 取得するカラムを明示（`*` を使わない）
- ✅ WHERE句で条件を明確に指定
- ✅ 将来のセキュリティ対策（プリペアドステートメント）にも言及

#### 曖昧な指示の例（避けるべき）

```text
「ユーザーを取得するSQL書いて」
```

**なぜダメ？**

- ❌ どのカラムを取得するか不明
- ❌ 条件が不明（全件？特定の条件？）
- ❌ テーブル名が不明

### 生成されたSQLのチェックポイント

AIが生成したSQLをチェックする時のポイント：

#### テーブル作成（CREATE TABLE）のチェック

- [ ] **主キーが設定されているか**
  - `PRIMARY KEY` が設定されているか
  - `AUTO_INCREMENT` が設定されているか

- [ ] **データ型が適切か**
  - INT：ID、数値
  - VARCHAR：短い文字列（名前、メールアドレス）
  - TEXT：長い文字列（本文）
  - TIMESTAMP：日時

- [ ] **制約が適切か**
  - `NOT NULL`：必須項目に設定されているか
  - `UNIQUE`：重複を許可しないカラム（email等）に設定されているか
  - `DEFAULT`：デフォルト値が適切か

- [ ] **文字コードが設定されているか**
  - `utf8mb4` が設定されているか（絵文字対応）

#### データ挿入（INSERT）のチェック

- [ ] **カラム名が正しいか**
  - テーブルに存在するカラム名か

- [ ] **データ型が一致しているか**
  - INT型のカラムに数値が入っているか
  - VARCHAR型のカラムに文字列が入っているか

- [ ] **必須カラムに値が入っているか**
  - NOT NULL制約があるカラムに値があるか

#### データ取得（SELECT）のチェック

- [ ] **必要なカラムだけ取得しているか**
  - `*` ではなく、具体的なカラム名を指定しているか

- [ ] **WHERE句が適切か**
  - 条件が正しいか
  - セキュリティリスクがないか（Phase 5で対策）

#### データ更新（UPDATE）のチェック

- [ ] **WHERE句があるか**
  - WHERE句がないと、全てのレコードが更新される！

- [ ] **更新するカラムが正しいか**
  - 意図したカラムを更新しているか

#### データ削除（DELETE）のチェック

- [ ] **WHERE句があるか**
  - WHERE句がないと、全てのレコードが削除される！

- [ ] **削除条件が正しいか**
  - 本当に削除していいレコードか

### よくある問題と修正方法

#### 問題1：WHERE句の忘れ

**AIが生成しがちな危険なSQL**：

```sql
-- 🚨 危険：WHERE句がない
UPDATE users SET age = 30;
```

**原因**：AIへの指示が曖昧

**修正**：

```sql
-- ✅ 安全：WHERE句で条件指定
UPDATE users SET age = 30 WHERE id = 1;
```

**AIへの修正指示**：

```text
「UPDATE文にWHERE句を追加してください。id=1のユーザーだけを更新するようにしてください。」
```

#### 問題2：文字コードの設定忘れ

**AIが生成しがちなSQL**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);
-- 文字コードの指定がない
```

**原因**：文字コードの指示がない

**修正**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**AIへの修正指示**：

```text
「テーブル作成時に、文字コードをutf8mb4に設定してください。日本語と絵文字に対応させたいです。」
```

#### 問題3：外部キー制約の設定忘れ

**AIが生成しがちなSQL**：

```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  -- 外部キー制約がない
    title VARCHAR(200) NOT NULL
);
```

**原因**：外部キー制約の指示がない

**修正**：

```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)  -- 外部キー制約を追加
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**AIへの修正指示**：

```text
「postsテーブルのuser_idに外部キー制約を追加してください。usersテーブルのidを参照するようにしてください。」
```

---

## 💪 演習問題

演習問題は別ファイルにまとめています。実際にSQLを書いて、CRUD操作をマスターしよう！

👉 **[演習問題を見る](exercises/README.md)**

**演習で使うリソースファイル**：

- [create-database.sql](resources/create-database.sql)：データベースとテーブルの作成SQL
- [insert-data.sql](resources/insert-data.sql)：サンプルデータの挿入SQL
- [sample-queries.sql](resources/sample-queries.sql)：基本的なクエリ集

---

## ✅ まとめ

このレッスンで学んだことを振り返ろう！

### CRUDの基本操作

- ✅ **CREATE（CREATE TABLE）**：テーブルの作成
- ✅ **INSERT（INSERT INTO）**：データの挿入
- ✅ **SELECT（SELECT）**：データの取得
- ✅ **UPDATE（UPDATE）**：データの更新
- ✅ **DELETE（DELETE）**：データの削除

### SELECT文の詳細

- ✅ **全件取得**：`SELECT * FROM テーブル名;`
- ✅ **特定カラム**：`SELECT カラム1, カラム2 FROM テーブル名;`
- ✅ **WHERE句**：条件指定
- ✅ **ORDER BY**：並び替え
- ✅ **LIMIT**：件数制限

### WHERE句の重要性

- ✅ **UPDATE/DELETE文では必須**：WHERE句がないと全レコードが対象
- ✅ **実行前にSELECTで確認**：安全な操作の習慣
- ✅ **セキュリティ意識**：Phase 5でプリペアドステートメントを学ぶ

### バイブコーディングのポイント

- ✅ 具体的な指示を出す（カラム名、制約を明示）
- ✅ WHERE句のチェックを忘れずに
- ✅ 文字コード（utf8mb4）を必ず指定
- ✅ 外部キー制約の設定を確認

---

## 🚀 次のステップ

SQL基本操作（CRUD）をマスターしたね！すごい！✨

次のLesson 04では、**データ型とテーブル設計**を学ぶよ！

- 数値型（INT、DECIMAL、FLOAT）
- 文字列型（VARCHAR、TEXT、CHAR）
- 日付・時刻型（DATE、DATETIME、TIMESTAMP）
- 真偽値型（BOOLEAN）
- 適切なデータ型の選択

データ型を正しく選ぶことで、パフォーマンスとデータ整合性が向上する！

👉 **[Lesson 04: データ型とテーブル設計へ進む](../04-data-types/README.md)**

---

**Let's vibe and code! 🎉**

CRUD操作は、データベースの基本中の基本！ここをマスターすれば、どんなWebアプリケーションも作れるようになるよ！
