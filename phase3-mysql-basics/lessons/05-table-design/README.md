# Lesson 05: 基本的なテーブル設計 📊

**学習目標**：正規化の基礎、テーブル間の関係、制約、インデックスを理解して、効率的で保守性の高いテーブルを設計できるようになる！

---

## 📖 このレッスンで学ぶこと

- **正規化の基礎**（第1正規形）
- **1対多の関係**（1:N）
- **制約（Constraints）**
  - PRIMARY KEY（主キー）
  - FOREIGN KEY（外部キー）
  - UNIQUE（一意制約）
  - NOT NULL（非NULL制約）
  - DEFAULT（デフォルト値）
- **インデックスの基礎**
- **命名規則**
- **実用例**：ブログシステム、ECサイト

---

## 🎯 なぜテーブル設計を学ぶの？（Why）

### 良い設計と悪い設計の違い

**アナロジー：家の設計**

**悪い設計**：

```text
- 部屋の配置がバラバラ
- 収納スペースがない
- 配線がぐちゃぐちゃ
- 修理するのが大変
```

**良い設計**：

```text
- 部屋が機能的に配置されている
- 収納スペースが十分
- 配線が整理されている
- 修理・拡張が簡単
```

**データベースも同じ！**

- ✅ **良い設計**：データの重複がない、検索が速い、メンテナンスしやすい
- ❌ **悪い設計**：データが重複、検索が遅い、エラーが発生しやすい

### バックエンド開発における重要性

テーブル設計は、**アプリケーション全体の品質**を左右する！

- 📊 **データ整合性**：矛盾のないデータを保持
- 🚀 **パフォーマンス**：高速な検索とデータ取得
- 🔒 **セキュリティ**：制約でデータの不正挿入を防ぐ
- 💪 **保守性**：変更や拡張がしやすい
- 🐛 **バグ削減**：データの不整合によるバグを防ぐ

---

## 🏗️ 正規化の基礎（Normalization）

### 正規化とは？

**正規化**：データの重複を排除して、効率的に管理する手法

**アナロジー：本棚の整理**

**正規化前（散らかった本棚）**：

```text
- 同じ本が複数の棚にある（重複）
- どこに何があるか分からない
- 本を探すのに時間がかかる
```

**正規化後（整理された本棚）**：

```text
- 同じ本は1冊だけ（重複なし）
- ジャンル別に整理
- すぐに見つかる
```

### 第1正規形（1NF）

**第1正規形の条件**：

1. **各カラムに1つの値だけ**（繰り返しグループの排除）
2. **主キーで各レコードを一意に識別できる**

**悪い例（第1正規形ではない）**：

```text
usersテーブル
id | name       | hobbies
---+------------+---------------------
1  | 山田太郎   | 読書, 映画, 料理
2  | 佐藤花子   | 旅行, 写真
```

**問題点**：

- `hobbies` カラムに複数の値が含まれている
- 「読書が趣味の人」を検索するのが難しい
- 新しい趣味を追加するのが大変

**良い例（第1正規形）**：

**usersテーブル**：

```text
id | name
---+----------
1  | 山田太郎
2  | 佐藤花子
```

**hobbiesテーブル**：

```text
id | user_id | hobby
---+---------+------
1  | 1       | 読書
2  | 1       | 映画
3  | 1       | 料理
4  | 2       | 旅行
5  | 2       | 写真
```

**メリット**：

- データの重複がない
- 検索が簡単
- 追加・削除が簡単

---

## 🔗 1対多の関係（1:N）

### 1対多の関係とは？

**1対多**：1つのレコードが、複数のレコードと関連している

**例**：

- **1人のユーザー** → **複数の投稿**（1:N）
- **1つのカテゴリ** → **複数の商品**（1:N）
- **1人の著者** → **複数の本**（1:N）

### 実例：ブログシステム

**usersテーブル**（1）：

| id  | name     | email               |
| --- | -------- | ------------------- |
| 1   | 山田太郎 | taro@example.com    |
| 2   | 佐藤花子 | hanako@example.com  |

**postsテーブル**（多）：

| id  | user_id | title        | content         |
| --- | ------- | ------------ | --------------- |
| 1   | 1       | 初めての投稿 | こんにちは！    |
| 2   | 1       | 2つ目の投稿  | 元気ですか？    |
| 3   | 2       | 花子の日記   | よろしくね！    |

**関係性**：

```text
users（1） ← postsテーブル（多）
user_id で紐づけ
```

**SQL**：

```sql
-- usersテーブル
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- postsテーブル
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,  -- 外部キー
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)  -- 外部キー制約
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 🔒 制約（Constraints）

### PRIMARY KEY（主キー）

**役割**：各レコードを一意に識別する

**特徴**：

- **一意**：重複した値は許可されない
- **NOT NULL**：NULLは許可されない
- **1テーブルに1つだけ**：複数の主キーは設定できない

**例**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- 主キー
    name VARCHAR(100) NOT NULL
);
```

### FOREIGN KEY（外部キー）

**役割**：他のテーブルのレコードを参照する

**特徴**：

- **参照整合性**：存在しない値は挿入できない
- **親レコードの削除**：子レコードがある場合、削除できない（デフォルト）

**例**：

```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)  -- 外部キー制約
);
```

**外部キー制約の効果**：

```sql
-- ❌ エラー：user_id = 999 は存在しない
INSERT INTO posts (user_id, title) VALUES (999, 'タイトル');
-- Error: Cannot add or update a child row: a foreign key constraint fails

-- ✅ OK：user_id = 1 は存在する
INSERT INTO posts (user_id, title) VALUES (1, 'タイトル');
```

**削除時の挙動**：

```sql
-- ❌ エラー：user_id = 1 を参照するpostsレコードが存在する
DELETE FROM users WHERE id = 1;
-- Error: Cannot delete or update a parent row: a foreign key constraint fails
```

**CASCADE（連鎖削除）**：

```sql
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE  -- 親レコード削除時、子レコードも削除
);
```

### UNIQUE（一意制約）

**役割**：重複した値を許可しない

**特徴**：

- **一意**：同じ値は1つだけ
- **NULL許可**：NULLは複数OK

**例**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE  -- 一意制約
);
```

**効果**：

```sql
-- ✅ OK
INSERT INTO users (name, email) VALUES ('山田太郎', 'taro@example.com');

-- ❌ エラー：emailが重複
INSERT INTO users (name, email) VALUES ('田中次郎', 'taro@example.com');
-- Error: Duplicate entry 'taro@example.com' for key 'email'
```

### NOT NULL（非NULL制約）

**役割**：NULLを許可しない

**特徴**：

- **必須項目**：値が必ず必要

**例**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,  -- 必須
    email VARCHAR(255) NOT NULL,  -- 必須
    age INT  -- NULLを許可（任意）
);
```

**効果**：

```sql
-- ❌ エラー：nameがNULL
INSERT INTO users (email) VALUES ('taro@example.com');
-- Error: Field 'name' doesn't have a default value

-- ✅ OK：全ての必須項目に値がある
INSERT INTO users (name, email) VALUES ('山田太郎', 'taro@example.com');
```

### DEFAULT（デフォルト値）

**役割**：値が指定されなかった場合のデフォルト値

**例**：

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0,  -- デフォルト値: 0
    is_active BOOLEAN DEFAULT TRUE,  -- デフォルト値: TRUE
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- デフォルト値: 現在時刻
);
```

**効果**：

```sql
-- stockとis_activeを指定しない
INSERT INTO products (name, price) VALUES ('商品A', 1000);

-- 自動でデフォルト値が設定される
SELECT * FROM products;
-- stock = 0, is_active = 1, created_at = 現在時刻
```

---

## 🚀 インデックスの基礎

### インデックスとは？

**インデックス**：データの検索を高速化するための「索引」

**アナロジー：本の索引**

**索引がない本**：

```text
「PHPという単語が出てくるページは？」
→ 全ページを読んで探す（遅い）
```

**索引がある本**：

```text
「PHPという単語が出てくるページは？」
→ 索引を見る → 即座に見つかる（速い）
```

### インデックスの種類

**PRIMARY KEY**：

- 自動的にインデックスが作成される
- 最も高速な検索

**UNIQUE**：

- 一意制約を持つカラムには自動的にインデックスが作成される

**INDEX（通常のインデックス）**：

- 頻繁に検索するカラムに設定

**例**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- 自動でインデックス
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,  -- 自動でインデックス
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at)  -- 手動でインデックスを作成
);
```

**インデックスのメリット**：

- ✅ 検索が高速化
- ✅ WHERE句、JOIN、ORDER BYが速くなる

**インデックスのデメリット**：

- ❌ データの挿入・更新が少し遅くなる
- ❌ ディスクスペースを使う

**使い分け**：

- 頻繁に検索するカラム → インデックスを付ける
- めったに検索しないカラム → インデックス不要

---

## 📝 命名規則

### テーブル名

- **複数形**：`users`、`posts`、`products`
- **スネークケース**：`blog_posts`、`product_categories`
- **小文字**：`users`（`Users` は避ける）

### カラム名

- **単数形**：`name`、`email`、`created_at`
- **スネークケース**：`user_id`、`created_at`、`is_active`
- **小文字**：`name`（`Name` は避ける）

### 外部キー名

- **参照先テーブル名_id**：`user_id`、`category_id`、`author_id`

### 良い例・悪い例

**悪い例**：

```sql
CREATE TABLE User (  -- ❌ 大文字、単数形
    ID INT,  -- ❌ 大文字
    UserName VARCHAR(100),  -- ❌ キャメルケース
    Email VARCHAR(255)
);
```

**良い例**：

```sql
CREATE TABLE users (  -- ✅ 小文字、複数形
    id INT AUTO_INCREMENT PRIMARY KEY,  -- ✅ 小文字
    name VARCHAR(100) NOT NULL,  -- ✅ スネークケース
    email VARCHAR(255) NOT NULL UNIQUE
);
```

---

## 💻 実用例

### 実用例1：ブログシステム

**テーブル構成**：

1. **users**：ユーザー情報
2. **posts**：投稿情報
3. **comments**：コメント情報

**SQL**：

```sql
-- usersテーブル
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- ハッシュ化されたパスワード
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- postsテーブル
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),  -- 検索高速化
    INDEX idx_created_at (created_at)  -- 検索高速化
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- commentsテーブル
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_post_id (post_id)  -- 検索高速化
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**関係性**：

```text
users（1） → posts（多）：1人のユーザーが複数の投稿を作成
users（1） → comments（多）：1人のユーザーが複数のコメントを投稿
posts（1） → comments（多）：1つの投稿に複数のコメント
```

### 実用例2：ECサイト

**テーブル構成**：

1. **categories**：カテゴリ情報
2. **products**：商品情報
3. **orders**：注文情報
4. **order_items**：注文明細

**SQL**：

```sql
-- categoriesテーブル
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- productsテーブル
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    INDEX idx_category_id (category_id),
    INDEX idx_price (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ordersテーブル
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'paid', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- order_itemsテーブル
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    price DECIMAL(10, 2) NOT NULL,  -- 注文時の価格を保存
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 🤖 バイブコーディング実践（最重要セクション！）

### AIへの指示例

#### 良い指示の例：ブログシステムのテーブル設計

```text
「MySQLで、ブログシステムのテーブルを設計してください。以下の要件を満たすCREATE TABLE文を書いてください：

1. usersテーブル：
   - id：INT、主キー、AUTO_INCREMENT
   - name：VARCHAR(100)、NOT NULL
   - email：VARCHAR(255)、NOT NULL、UNIQUE
   - password：VARCHAR(255)、NOT NULL
   - created_at：TIMESTAMP、デフォルトCURRENT_TIMESTAMP

2. postsテーブル：
   - id：INT、主キー、AUTO_INCREMENT
   - user_id：INT、NOT NULL、外部キー（usersテーブルのidを参照）
   - title：VARCHAR(200)、NOT NULL
   - content：TEXT、NOT NULL
   - created_at：TIMESTAMP、デフォルトCURRENT_TIMESTAMP
   - updated_at：TIMESTAMP、デフォルトCURRENT_TIMESTAMP、更新時に自動更新
   - user_idとcreated_atにインデックスを付ける

外部キー制約は、親レコード削除時に子レコードも削除する（ON DELETE CASCADE）ようにしてください。
文字コードはutf8mb4を使用してください。」
```

### 生成されたテーブル設計のチェックポイント

#### 正規化チェック

- [ ] **データの重複がないか**
  - 同じ情報が複数の場所に保存されていないか

- [ ] **第1正規形か**
  - 各カラムに1つの値だけが入っているか

#### 関係性チェック

- [ ] **外部キーが適切か**
  - 1対多の関係が正しく表現されているか
  - FOREIGN KEY制約が設定されているか

#### 制約チェック

- [ ] **主キーが設定されているか**
  - PRIMARY KEY、AUTO_INCREMENT

- [ ] **外部キー制約が設定されているか**
  - FOREIGN KEY (user_id) REFERENCES users(id)

- [ ] **一意制約が設定されているか**
  - emailなど、重複を許可しないカラムに UNIQUE

- [ ] **NOT NULL制約が設定されているか**
  - 必須項目に NOT NULL

- [ ] **デフォルト値が設定されているか**
  - created_at に DEFAULT CURRENT_TIMESTAMP

#### インデックスチェック

- [ ] **頻繁に検索するカラムにインデックスがあるか**
  - user_id、created_at など

---

## 💪 演習問題

演習問題は別ファイルにまとめています。実際にテーブルを設計して、正規化と制約を学ぼう！

👉 **[演習問題を見る](exercises/README.md)**

---

## ✅ まとめ

このレッスンで学んだことを振り返ろう！

### 正規化の基礎

- ✅ **第1正規形**：各カラムに1つの値だけ
- ✅ **データの重複を排除**：効率的な管理

### 1対多の関係

- ✅ **1対多**：1つのレコードが複数のレコードと関連
- ✅ **外部キーで紐づけ**

### 制約

- ✅ **PRIMARY KEY**：各レコードを一意に識別
- ✅ **FOREIGN KEY**：他のテーブルを参照、データ整合性を保つ
- ✅ **UNIQUE**：重複を許可しない
- ✅ **NOT NULL**：必須項目
- ✅ **DEFAULT**：デフォルト値

### インデックス

- ✅ **検索高速化**：頻繁に検索するカラムに設定
- ✅ **PRIMARY KEY、UNIQUE**：自動でインデックス

### 命名規則

- ✅ **テーブル名**：複数形、スネークケース、小文字
- ✅ **カラム名**：単数形、スネークケース、小文字
- ✅ **外部キー名**：参照先テーブル名_id

---

## 🚀 次のステップ

基本的なテーブル設計をマスターしたね！すごい！✨

**Phase 3（MySQL基礎）の全レッスンを修了しました！**

次は **Phase 4（MySQL発展）** で：

- JOIN（複数テーブルの結合）
- サブクエリ
- 集計関数（COUNT、SUM、AVG など）
- GROUP BY、HAVING
- 正規化の詳細（第2正規形、第3正規形）

を学んでいくよ！

または、**Phase 5（PHP+MySQL統合）** で：

- PHPからMySQLに接続
- プリペアドステートメント（SQLインジェクション対策）
- CRUD操作の実装
- ログイン機能の実装

を学んで、実際にWebアプリケーションを作ってみよう！

---

**Let's vibe and code! 🎉**

テーブル設計は、データベースの基盤！正規化と制約を理解して、効率的で保守性の高いデータベースを設計しよう！
