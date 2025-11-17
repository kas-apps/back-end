# Lesson 01: データベースとは - 解答例 ✅

演習問題の解答例だよ！自分の答えと比べてみてね。

---

## 📝 基礎編

### 問題1-1: Excelとデータベースの対応関係

**解答**：

1. **テーブル名**：`students`（生徒たち、複数形）

2. **カラム名**：
   - `id`
   - `name`
   - `age`
   - `class`

3. **主キー**：`id`
   - 理由：各生徒を一意に識別できる

4. **各カラムのデータ型**：
   - `id`：`INT`（整数）
   - `name`：`VARCHAR(100)`（文字列、可変長）
   - `age`：`INT`（整数）
   - `class`：`VARCHAR(10)`（文字列、可変長）

**テーブル定義の例**：

```sql
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    class VARCHAR(10) NOT NULL
);
```

**ポイント**：

- テーブル名は複数形（`students`）
- 主キーは`id`で、`AUTO_INCREMENT`を使うと自動で番号が振られる
- `NOT NULL`で必須項目を指定

---

### 問題1-2: 主キーの理解

**解答**：**D: `student_id`**

**理由**：

✅ **`student_id`が主キーとして最適な理由**：

- **ユニーク（一意）**：各生徒に1つだけ
- **変わらない**：入学時に割り当てられ、卒業まで変わらない
- **NULL にならない**：必ず値がある
- **シンプル**：整数で管理しやすい

❌ **他の選択肢がダメな理由**：

- **A: `name`** → 同姓同名がいる可能性（太郎さんが2人いたら？）
- **B: `email`** → 変更される可能性がある
- **C: `phone`** → 変更される可能性がある

**主キーの条件**：

1. ユニーク（重複しない）
2. NULL にならない
3. 変わらない
4. シンプル

---

### 問題1-3: 外部キーの理解

**解答**：

1. **外部キーはどのカラムですか？**
   - `booksテーブル`の`author_id`

2. **`author_id = 1`の書籍は何冊ありますか？**
   - 2冊（「吾輩は猫である」と「坊っちゃん」）

3. **「羅生門」の著者は誰ですか？**
   - 芥川龍之介
   - 理由：「羅生門」の`author_id`は`2`、`authorsテーブル`で`id = 2`は芥川龍之介

**外部キーの役割**：

```text
booksテーブルのauthor_id → authorsテーブルのidを参照
```

これで、「この本の著者は誰？」って簡単にわかる！

---

## 🎯 応用編

### 問題1-4: ブログシステムのテーブル設計

**解答**：

#### 1. `users`テーブル

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**カラム構成**：

| カラム名   | データ型      | 制約                                 | 説明         |
| ---------- | ------------- | ------------------------------------ | ------------ |
| id         | INT           | PRIMARY KEY, AUTO_INCREMENT          | ユーザーID   |
| name       | VARCHAR(100)  | NOT NULL                             | ユーザー名   |
| email      | VARCHAR(255)  | UNIQUE, NOT NULL                     | メールアドレス |
| created_at | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP            | 登録日時     |

#### 2. `posts`テーブル

```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**カラム構成**：

| カラム名   | データ型      | 制約                                 | 説明         |
| ---------- | ------------- | ------------------------------------ | ------------ |
| id         | INT           | PRIMARY KEY, AUTO_INCREMENT          | 投稿ID       |
| user_id    | INT           | NOT NULL, FOREIGN KEY                | ユーザーID（外部キー）|
| title      | VARCHAR(200)  | NOT NULL                             | タイトル     |
| content    | TEXT          | NOT NULL                             | 本文         |
| created_at | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP            | 投稿日時     |

#### 3. 主キーと外部キー

- **usersテーブルの主キー**：`id`
- **postsテーブルの主キー**：`id`
- **postsテーブルの外部キー**：`user_id`（usersテーブルのidを参照）

#### 4. 制約

- `NOT NULL`：必須項目（空欄不可）
- `UNIQUE`：一意（重複不可）、emailに設定
- `DEFAULT CURRENT_TIMESTAMP`：デフォルトで現在時刻を設定

**テーブル間の関係**：

```text
users（1人） → posts（複数の投稿）

1対多の関係
```

---

### 問題1-5: ECサイトの商品管理

**解答**：

#### 1. `categories`テーブル

```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);
```

**カラム構成**：

| カラム名 | データ型     | 制約                        | 説明           |
| -------- | ------------ | --------------------------- | -------------- |
| id       | INT          | PRIMARY KEY, AUTO_INCREMENT | カテゴリID     |
| name     | VARCHAR(100) | UNIQUE, NOT NULL            | カテゴリ名     |

#### 2. `products`テーブル

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

**カラム構成**：

| カラム名    | データ型       | 制約                        | 説明               |
| ----------- | -------------- | --------------------------- | ------------------ |
| id          | INT            | PRIMARY KEY, AUTO_INCREMENT | 商品ID             |
| category_id | INT            | NOT NULL, FOREIGN KEY       | カテゴリID（外部キー）|
| name        | VARCHAR(200)   | NOT NULL                    | 商品名             |
| price       | DECIMAL(10, 2) | NOT NULL                    | 価格               |
| stock       | INT            | DEFAULT 0                   | 在庫数             |

#### 3. 主キーと外部キー

- **categoriesテーブルの主キー**：`id`
- **productsテーブルの主キー**：`id`
- **productsテーブルの外部キー**：`category_id`（categoriesテーブルのidを参照）

#### 4. テーブル間の関係性

```text
categories（1つのカテゴリ） → products（複数の商品）

1対多の関係

例：
「家電」カテゴリ → ノートPC、スマホ、タブレット（複数商品）
「書籍」カテゴリ → PHP入門書、MySQL入門書（複数商品）
```

**ポイント**：

- 価格は`DECIMAL(10, 2)`を使う（正確な小数が必要）
- 在庫数のデフォルト値は0

---

## 🔍 セキュリティチャレンジ

### 問題1-6: データの重複を発見せよ

**解答**：

#### 1. 重複しているデータ

- **顧客情報**（`customer_name`, `customer_email`, `customer_phone`）が重複
  - 太郎さんの情報が2回出ている

#### 2. この設計の問題点

**問題点1：データの重複**

- 太郎さんが10回注文したら、太郎さんの情報が10回保存される
- 無駄にデータ量が増える

**問題点2：更新の手間**

- 太郎さんのメールアドレスが変わったら？
- 全ての注文レコードを更新しないといけない（漏れが出る可能性）

**問題点3：データの不整合**

- 1件だけ更新漏れがあったら、「太郎さんのメールアドレスが2つ存在する」状態になる
- どっちが正しいの？😱

#### 3. テーブル分割の提案

**良い設計**：3つのテーブルに分割

**customersテーブル**（顧客情報）：

```sql
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20)
);
```

**productsテーブル**（商品情報）：

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10, 2) NOT NULL
);
```

**ordersテーブル**（注文情報）：

```sql
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    product_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

**メリット**：

- ✅ データの重複なし
- ✅ 顧客情報の変更は1箇所だけ
- ✅ データの整合性が保たれる
- ✅ 効率的なデータ管理

**テーブル間の関係**：

```text
customers（1人） → orders（複数の注文）
products（1つの商品） → orders（複数の注文）
```

---

## 🌟 総合チャレンジ

### 問題1-7: タスク管理システムの設計

**解答**：

#### 1. 必要なテーブル

- `users`テーブル：ユーザー情報
- `projects`テーブル：プロジェクト情報
- `tasks`テーブル：タスク情報

#### 2. 各テーブルのカラム構成

**usersテーブル**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**projectsテーブル**：

```sql
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**tasksテーブル**：

```sql
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    is_completed BOOLEAN DEFAULT FALSE,
    due_date DATE,
    FOREIGN KEY (project_id) REFERENCES projects(id)
);
```

#### 3. 主キーと外部キー

**主キー**：

- usersテーブル：`id`
- projectsテーブル：`id`
- tasksテーブル：`id`

**外部キー**：

- projectsテーブル：`user_id`（usersテーブルのidを参照）
- tasksテーブル：`project_id`（projectsテーブルのidを参照）

#### 4. テーブル間の関係性

```text
users（1人のユーザー）
   ↓ 1対多
projects（複数のプロジェクト）
   ↓ 1対多
tasks（複数のタスク）

具体例：
太郎さん（users）
 ├── Webサイト制作（projects）
 │    ├── デザイン作成（tasks）
 │    ├── コーディング（tasks）
 │    └── テスト（tasks）
 └── ブログ執筆（projects）
      ├── 記事1執筆（tasks）
      └── 記事2執筆（tasks）
```

**サンプルデータ**：

**users**：

| id  | name | email            |
| --- | ---- | ---------------- |
| 1   | 太郎 | <taro@example.com> |

**projects**：

| id  | user_id | name           |
| --- | ------- | -------------- |
| 1   | 1       | Webサイト制作  |
| 2   | 1       | ブログ執筆     |

**tasks**：

| id  | project_id | name         | is_completed | due_date   |
| --- | ---------- | ------------ | ------------ | ---------- |
| 1   | 1          | デザイン作成 | TRUE         | 2024-01-10 |
| 2   | 1          | コーディング | FALSE        | 2024-01-20 |
| 3   | 2          | 記事1執筆    | FALSE        | 2024-01-15 |

---

## 🤖 AI活用チャレンジ

### 問題1-8: AIにテーブル設計を依頼しよう

**良い指示の例**：

```text
「図書館の本の貸し出し管理システムのデータベースを設計してください。

以下の3つのテーブルが必要です：

1. membersテーブル（会員情報）
   - id（主キー、自動増分）
   - name（会員名、必須）
   - email（メールアドレス、必須、一意）
   - phone（電話番号）
   - registered_at（登録日時、デフォルト：現在時刻）

2. booksテーブル（本の情報）
   - id（主キー、自動増分）
   - title（タイトル、必須）
   - author（著者、必須）
   - isbn（ISBN、一意）
   - is_available（貸出可能状態、デフォルト：TRUE）

3. lendingsテーブル（貸し出し履歴）
   - id（主キー、自動増分）
   - member_id（外部キー、membersテーブルのidを参照、必須）
   - book_id（外部キー、booksテーブルのidを参照、必須）
   - lent_at（貸出日、デフォルト：現在時刻）
   - returned_at（返却日、NULL可）

テーブル間の関係：
- 1人の会員 → 複数の貸し出し記録（1対多）
- 1冊の本 → 複数の貸し出し記録（1対多）

SQLのCREATE TABLE文で出力してください。」
```

**解答例（AIが生成すべきSQL）**：

```sql
-- membersテーブル
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- booksテーブル
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    is_available BOOLEAN DEFAULT TRUE
);

-- lendingsテーブル
CREATE TABLE lendings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    book_id INT NOT NULL,
    lent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    returned_at TIMESTAMP NULL,
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);
```

**チェックリストで評価**：

- [x] 主キーが全てのテーブルに設定されているか
  - ✅ 全テーブルに`id`（PRIMARY KEY、AUTO_INCREMENT）がある

- [x] 外部キーで適切に紐づいているか
  - ✅ `lendings.member_id` → `members.id`
  - ✅ `lendings.book_id` → `books.id`

- [x] データの重複がないか
  - ✅ 会員情報、本情報は各テーブルに1回だけ保存
  - ✅ lendingsテーブルは外部キーで参照

- [x] 命名規則が統一されているか
  - ✅ テーブル名：複数形、スネークケース（members、books、lendings）
  - ✅ カラム名：単数形、スネークケース（member_id、book_id、lent_at）

- [x] 必要な制約が設定されているか
  - ✅ NOT NULL：必須項目に設定
  - ✅ UNIQUE：email、isbnに設定
  - ✅ DEFAULT：registered_at、lent_at、is_availableに設定

**評価**：完璧！✨

**もし問題があった場合の修正指示例**：

```text
「lendingsテーブルのmember_idとbook_idに外部キー制約を追加してください。
それぞれ、membersテーブルのid、booksテーブルのidを参照するようにしてください。」
```

---

## 💡 ポイント解説

### データベース設計のコツ

1. **データの種類ごとにテーブルを分ける**
   - ユーザー情報 → usersテーブル
   - 投稿情報 → postsテーブル

2. **データの重複を避ける**
   - 同じ情報を何度も保存しない
   - 外部キーで参照する

3. **主キーは必ず設定**
   - 各レコードを一意に識別できるようにする
   - 通常は`id`カラム（INT、AUTO_INCREMENT）

4. **外部キーで関連付ける**
   - テーブル間の関係を明示する
   - データの整合性を保つ

5. **適切な制約を設定**
   - NOT NULL：必須項目
   - UNIQUE：一意（重複不可）
   - DEFAULT：デフォルト値

### AIに指示を出すコツ

1. **具体的なカラム名を指定**
   - 曖昧：「ユーザー情報を格納」
   - 具体的：「id、name、emailカラムを含む」

2. **主キー・外部キーを明示**
   - 「idは主キー、AUTO_INCREMENTで設定」
   - 「user_idは外部キー、usersテーブルのidを参照」

3. **制約を指定**
   - 「emailはUNIQUE、NOT NULL」
   - 「created_atはデフォルトで現在時刻」

4. **関係性を説明**
   - 「1人のユーザー → 複数の投稿（1対多）」

---

## ✅ まとめ

演習お疲れさま！データベース設計の感覚がつかめてきたかな？

**重要ポイント**：

- ✅ データの重複を避ける
- ✅ 主キーで各レコードを一意に識別
- ✅ 外部キーで関連付ける
- ✅ 適切な制約を設定
- ✅ 命名規則を統一（スネークケース、複数形/単数形）

次のLesson 02では、実際にphpMyAdminを使ってテーブルを作成していくよ！

---

**Let's vibe and code! 🎉**

練習を重ねて、データベース設計のプロになろう！
