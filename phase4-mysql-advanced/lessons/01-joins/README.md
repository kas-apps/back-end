# Lesson 01: JOIN（結合） 🔗

**学習目標**：複数のテーブルからデータを取得する方法を理解し、AIと一緒に複雑なJOINクエリを書けるようになる！

---

## 📖 このレッスンで学ぶこと

- JOINの必要性（なぜ複数テーブルからデータを取得するのか）
- INNER JOIN（両方にマッチするデータのみ取得）
- LEFT JOIN / RIGHT JOIN（片方のテーブルの全データを取得）
- 複数テーブルのJOIN（3つ以上のテーブルを結合）
- サブクエリの基礎（クエリの中にクエリ）

---

## 🎯 なぜJOINを学ぶの？（Why）

### 実際のWebアプリでよくあるニーズ

「ユーザー名と投稿タイトルを一緒に表示したい！」
「商品名とカテゴリ名を一緒に取得したい！」

Phase 3で学んだテーブル設計では、データの重複を避けるために、情報を複数のテーブルに分けて保存したよね。

**Phase 3の復習**：

- `users`テーブル：ユーザー情報
- `posts`テーブル：投稿情報（`user_id`で`users`を参照）

でも、実際にWebページを表示するときは、**ユーザー名と投稿タイトルを一緒に**表示したい！

```text
欲しいデータ：
ユーザー名：太郎  →  投稿タイトル：初めての投稿
ユーザー名：花子  →  投稿タイトル：花子の日記
```

これを実現するのが**JOIN（結合）**！

### JOINがないと…

**悪い方法**（JOINなし）：

```text
ステップ1：postsテーブルから投稿を取得
→ user_id = 1, title = "初めての投稿"

ステップ2：usersテーブルからuser_id = 1のユーザーを取得
→ name = "太郎"

ステップ3：PHPで結合する
```

問題点：

- データベースへのクエリが2回必要（遅い！）
- PHPで結合する処理が複雑
- 10件の投稿を表示するなら、11回もクエリを実行！😱

**良い方法**（JOINあり）：

```text
1回のクエリで、ユーザー名と投稿タイトルを一緒に取得！
```

JOINを使えば、**1回のクエリで完結**！高速で効率的！✨

---

## 🧩 JOINの基本概念（What）

### アナロジー：パズルのピースを組み合わせる

JOINは「パズルのピースを組み合わせる」みたいなもの！

```text
ピース1：usersテーブル（ユーザー情報）
ピース2：postsテーブル（投稿情報）

接続部分：user_id（外部キー）

組み合わせると：ユーザー名 + 投稿タイトルの完全な情報！
```

### JOINの種類

JOINには主に3種類ある：

#### 1. INNER JOIN（内部結合）

**「両方のテーブルにマッチするデータのみ」取得**

```text
usersテーブル：太郎（id=1）、花子（id=2）、次郎（id=3）
postsテーブル：太郎の投稿（user_id=1）、花子の投稿（user_id=2）

INNER JOINの結果：
太郎 + 太郎の投稿
花子 + 花子の投稿

次郎は投稿がないので、結果に含まれない！
```

#### 2. LEFT JOIN（左外部結合）

**「左のテーブルの全データ + マッチする右のデータ」取得**

```text
usersテーブル（左）：太郎（id=1）、花子（id=2）、次郎（id=3）
postsテーブル（右）：太郎の投稿（user_id=1）、花子の投稿（user_id=2）

LEFT JOINの結果：
太郎 + 太郎の投稿
花子 + 花子の投稿
次郎 + NULL（投稿なし）

次郎も結果に含まれる！投稿はNULL
```

#### 3. RIGHT JOIN（右外部結合）

**「右のテーブルの全データ + マッチする左のデータ」取得**

LEFT JOINの逆バージョン。実務ではLEFT JOINの方がよく使われる。

---

## 💻 実例で理解しよう（How）

### 準備：サンプルデータを作成

まず、Phase 3で学んだテーブルを作成しよう！

**usersテーブルの作成**：

```sql
-- usersテーブル作成
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- サンプルデータ挿入
INSERT INTO users (name, email) VALUES
('太郎', 'taro@example.com'),
('花子', 'hanako@example.com'),
('次郎', 'jiro@example.com');
```

**postsテーブルの作成**：

```sql
-- postsテーブル作成
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- サンプルデータ挿入
INSERT INTO posts (user_id, title, content) VALUES
(1, '初めての投稿', 'こんにちは！'),
(1, 'PHP学習中', '楽しいです！'),
(2, '花子の日記', 'よろしくお願いします');
```

**データの確認**：

usersテーブル：

| id  | name | email               |
| --- | ---- | ------------------- |
| id  | name | email              |
| --- | ---- | ------------------ |
| 1   | 太郎 | `taro@example.com` |
| 2   | 花子 | `hanako@example.com` |
| 3   | 次郎 | `jiro@example.com` |

postsテーブル：

| id  | user_id | title        | content                |
| --- | ------- | ------------ | ---------------------- |
| 1   | 1       | 初めての投稿 | こんにちは！           |
| 2   | 1       | PHP学習中    | 楽しいです！           |
| 3   | 2       | 花子の日記   | よろしくお願いします   |

---

### 実例1：INNER JOIN（内部結合）

**クエリ**：

```sql
-- INNER JOIN：ユーザー名と投稿タイトルを一緒に取得
SELECT
    users.name,        -- usersテーブルのnameカラム
    posts.title,       -- postsテーブルのtitleカラム
    posts.content      -- postsテーブルのcontentカラム
FROM
    users              -- 左のテーブル
INNER JOIN
    posts              -- 右のテーブル
ON
    users.id = posts.user_id;  -- 結合条件（どのカラムで紐づけるか）
```

**実行結果**：

| name | title        | content                |
| ---- | ------------ | ---------------------- |
| 太郎 | 初めての投稿 | こんにちは！           |
| 太郎 | PHP学習中    | 楽しいです！           |
| 花子 | 花子の日記   | よろしくお願いします   |

**ポイント**：

- 太郎さんの投稿が2件あるので、太郎さんが2行表示される
- 次郎さんは投稿がないので、結果に含まれない
- `ON users.id = posts.user_id` で「どのカラムで紐づけるか」を指定

### 実例2：LEFT JOIN（左外部結合）

**クエリ**：

```sql
-- LEFT JOIN：投稿がないユーザーも表示
SELECT
    users.name,
    posts.title,
    posts.content
FROM
    users              -- 左のテーブル（全データを取得）
LEFT JOIN
    posts              -- 右のテーブル（マッチするデータのみ）
ON
    users.id = posts.user_id;
```

**実行結果**：

| name | title        | content                |
| ---- | ------------ | ---------------------- |
| 太郎 | 初めての投稿 | こんにちは！           |
| 太郎 | PHP学習中    | 楽しいです！           |
| 花子 | 花子の日記   | よろしくお願いします   |
| 次郎 | NULL         | NULL                   |

**ポイント**：

- 次郎さんは投稿がないけど、結果に含まれる！
- 投稿がない場合は`NULL`が表示される
- **「投稿がないユーザーも表示したい」**ときに便利！

### 実例3：テーブル名にエイリアス（別名）を付ける

長いテーブル名を毎回書くのは大変！エイリアス（別名）を使おう！

**クエリ**：

```sql
-- エイリアスを使ってスッキリ！
SELECT
    u.name,        -- u = usersテーブル
    p.title,       -- p = postsテーブル
    p.content
FROM
    users AS u     -- usersテーブルに別名「u」を付ける
INNER JOIN
    posts AS p     -- postsテーブルに別名「p」を付ける
ON
    u.id = p.user_id;
```

**ポイント**：

- `AS`を使って別名を付ける（`AS`は省略可能）
- `users.name` → `u.name` でスッキリ！
- 複雑なクエリでは必須のテクニック

---

### 実例4：3つのテーブルをJOIN

実際のWebアプリでは、3つ以上のテーブルを結合することもあるよ！

**categoriesテーブルの作成**：

```sql
-- categoriesテーブル作成
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- サンプルデータ挿入
INSERT INTO categories (name) VALUES
('技術'),
('日記'),
('趣味');

-- postsテーブルにcategory_idカラムを追加（既存テーブルへの変更）
ALTER TABLE posts ADD COLUMN category_id INT;

-- 外部キー制約を追加
ALTER TABLE posts ADD FOREIGN KEY (category_id) REFERENCES categories(id);

-- 既存の投稿にカテゴリを設定
UPDATE posts SET category_id = 1 WHERE id = 1;  -- 初めての投稿 → 技術
UPDATE posts SET category_id = 1 WHERE id = 2;  -- PHP学習中 → 技術
UPDATE posts SET category_id = 2 WHERE id = 3;  -- 花子の日記 → 日記
```

**3テーブルのJOINクエリ**：

```sql
-- ユーザー名、投稿タイトル、カテゴリ名を一緒に取得
SELECT
    u.name AS user_name,           -- ユーザー名
    p.title AS post_title,         -- 投稿タイトル
    c.name AS category_name        -- カテゴリ名
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
INNER JOIN
    categories AS c ON p.category_id = c.id;
```

**実行結果**：

| user_name | post_title   | category_name |
| --------- | ------------ | ------------- |
| 太郎      | 初めての投稿 | 技術          |
| 太郎      | PHP学習中    | 技術          |
| 花子      | 花子の日記   | 日記          |

**ポイント**：

- 2つのINNER JOINを使って、3つのテーブルを結合
- 結合の順序：users → posts → categories
- `AS`を使ってカラムに別名を付けると、結果がわかりやすい！

---

### 実例5：WHERE句と組み合わせる

JOINとWHERE句を組み合わせて、条件を指定できる！

**クエリ**：

```sql
-- 「技術」カテゴリの投稿のみ取得
SELECT
    u.name,
    p.title,
    c.name AS category_name
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
INNER JOIN
    categories AS c ON p.category_id = c.id
WHERE
    c.name = '技術';  -- カテゴリが「技術」のみ
```

**実行結果**：

| name | title        | category_name |
| ---- | ------------ | ------------- |
| 太郎 | 初めての投稿 | 技術          |
| 太郎 | PHP学習中    | 技術          |

**ポイント**：

- WHERE句で条件を指定
- JOINした後にフィルタリングされる

---

### 実例6：ORDER BYで並び替え

**クエリ**：

```sql
-- 投稿日時が新しい順に並び替え
SELECT
    u.name,
    p.title,
    p.created_at
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
ORDER BY
    p.created_at DESC;  -- 新しい順（降順）
```

**ポイント**：

- JOINした結果を`ORDER BY`で並び替えられる
- `DESC`は降順（新しい→古い）、`ASC`は昇順（古い→新しい）

---

### 実例7：サブクエリの基礎

**サブクエリ = クエリの中にクエリ**

**クエリ**：

```sql
-- 投稿が2件以上あるユーザーの投稿を取得
SELECT
    u.name,
    p.title
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
WHERE
    u.id IN (
        -- サブクエリ：投稿が2件以上あるユーザーのIDを取得
        SELECT user_id
        FROM posts
        GROUP BY user_id
        HAVING COUNT(*) >= 2
    );
```

**実行結果**：

| name | title        |
| ---- | ------------ |
| 太郎 | 初めての投稿 |
| 太郎 | PHP学習中    |

**ポイント**：

- サブクエリ（`IN (SELECT ...)`）でユーザーIDを取得
- メインクエリでそのユーザーの投稿を取得
- サブクエリは高度なテクニック！Phase 5でもっと詳しく学ぶ

---

## 🤖 バイブコーディング実践

### AIへの指示例

#### 良い指示の例1：基本的なINNER JOIN

```text
「usersテーブルとpostsテーブルをINNER JOINして、
ユーザー名（users.name）と投稿タイトル（posts.title）を取得するSQLを書いてください。
結合条件は、users.idとposts.user_idです。」
```

**なぜ良い？**

- 使用するテーブル名を明示
- 取得するカラム名を明示
- 結合条件（ON句）を明示
- JOINの種類（INNER JOIN）を明示

#### 良い指示の例2：LEFT JOINで投稿がないユーザーも表示

```text
「usersテーブルとpostsテーブルをLEFT JOINして、
全ユーザーと投稿を取得してください。投稿がないユーザーも含めてください。
結合条件は、users.idとposts.user_idです。」
```

**ポイント**：

- LEFT JOINを明示
- 「投稿がないユーザーも含める」と明示

#### 良い指示の例3：3テーブルのJOIN

```text
「usersテーブル、postsテーブル、categoriesテーブルを結合して、
ユーザー名、投稿タイトル、カテゴリ名を取得してください。
結合条件：
- users.id = posts.user_id
- posts.category_id = categories.id
すべて INNER JOIN で結合してください。」
```

**ポイント**：

- 複数の結合条件を明示
- 各テーブルの関係を説明

#### 曖昧な指示の例（悪い例）

```text
「ユーザーと投稿を結合して」
```

**なぜ悪い？**

- 結合条件が不明
- JOINの種類が不明
- 取得するカラムが不明

AIは頑張って生成するけど、期待と違うかも...😢

---

### 生成されたJOINクエリのチェックポイント

AIが生成したJOINクエリを見るときは、以下をチェック！

#### 結合条件チェック

- [ ] **ON句が正しいか**
  - `ON users.id = posts.user_id` のように、外部キーで結合しているか
  - 結合条件が複数ある場合、すべて記載されているか

- [ ] **JOINの種類が適切か**
  - INNER JOIN：両方にマッチするデータのみ
  - LEFT JOIN：左のテーブルの全データ
  - 目的に合ったJOINが選ばれているか

#### カラム指定チェック

- [ ] **取得するカラムが明確か**
  - `SELECT *` ではなく、必要なカラムのみ指定されているか
  - テーブル名（または別名）付きでカラムが指定されているか（`u.name`など）

- [ ] **エイリアス（別名）が使われているか**
  - 長いテーブル名の場合、別名が付けられているか
  - カラムにわかりやすい別名が付けられているか（`AS user_name`など）

#### パフォーマンスチェック

- [ ] **必要なインデックスがあるか**
  - 結合条件のカラム（`user_id`など）にインデックスがあるか
  - Lesson 03でインデックスを詳しく学ぶ

- [ ] **不要なJOINがないか**
  - 使わないテーブルまでJOINしていないか

---

### よくある問題と修正方法

#### 問題1：結合条件の間違い

**悪い例（AIが生成しがち）**：

```sql
-- 結合条件が間違っている
SELECT u.name, p.title
FROM users AS u
INNER JOIN posts AS p
ON u.name = p.title;  -- 間違い！nameとtitleを比較している
```

**問題点**：結合条件が間違っている！`users.id`と`posts.user_id`を比較すべき。

**修正**：

```sql
-- 正しい結合条件
SELECT u.name, p.title
FROM users AS u
INNER JOIN posts AS p
ON u.id = p.user_id;  -- 正しい！idとuser_idを比較
```

**AIへの修正指示**：

```text
「ON句の結合条件が間違っています。
users.idとposts.user_idを比較するように修正してください。」
```

---

#### 問題2：SELECT * を使っている

**悪い例（AIが生成しがち）**：

```sql
-- SELECT * は避けるべき
SELECT *
FROM users AS u
INNER JOIN posts AS p
ON u.id = p.user_id;
```

**問題点**：

- 不要なカラムも取得される（パフォーマンス低下）
- どのカラムが取得されるかわかりにくい
- カラム名が重複する（`id`が2つ！）

**修正**：

```sql
-- 必要なカラムのみ指定
SELECT
    u.id AS user_id,
    u.name AS user_name,
    p.id AS post_id,
    p.title AS post_title
FROM users AS u
INNER JOIN posts AS p
ON u.id = p.user_id;
```

**AIへの修正指示**：

```text
「SELECT * ではなく、必要なカラムのみ指定してください。
users.id, users.name, posts.id, posts.title を取得してください。
カラム名が重複しないよう、エイリアス（AS）を使ってください。」
```

---

#### 問題3：INNER JOINとLEFT JOINの選択ミス

**悪い例（目的と合わない）**：

```text
目的：「投稿がないユーザーも含めて、全ユーザーを表示したい」

AIが生成したSQL：
SELECT u.name, p.title
FROM users AS u
INNER JOIN posts AS p  -- INNER JOINだと投稿がないユーザーは表示されない！
ON u.id = p.user_id;
```

**問題点**：INNER JOINだと、投稿がないユーザー（次郎さん）が表示されない！

**修正**：

```sql
-- LEFT JOINに変更
SELECT u.name, p.title
FROM users AS u
LEFT JOIN posts AS p  -- LEFT JOINで全ユーザーを表示
ON u.id = p.user_id;
```

**AIへの修正指示**：

```text
「投稿がないユーザーも表示したいので、INNER JOINではなくLEFT JOINに変更してください。」
```

---

#### 問題4：3テーブルのJOINで結合順序が複雑

**悪い例（AIが生成しがち）**：

```sql
-- 結合順序が複雑で読みにくい
SELECT u.name, p.title, c.name
FROM categories AS c
INNER JOIN posts AS p ON c.id = p.category_id
INNER JOIN users AS u ON p.user_id = u.id;
```

**問題点**：結合順序が直感的でない。`users`から始める方がわかりやすい。

**修正**：

```sql
-- usersから始める方がわかりやすい
SELECT u.name, p.title, c.name
FROM users AS u
INNER JOIN posts AS p ON u.id = p.user_id
INNER JOIN categories AS c ON p.category_id = c.id;
```

**AIへの修正指示**：

```text
「結合順序をusers → posts → categoriesの順に変更してください。
より読みやすいクエリにしてください。」
```

---

## 💪 演習問題

演習問題は別ファイルにまとめています。実際に手を動かして、JOIN操作を練習しよう！

👉 **[演習問題を見る](exercises/README.md)**

---

## ✅ まとめ

このレッスンで学んだことを振り返ろう！

### JOINの必要性

- ✅ 複数のテーブルからデータを一緒に取得できる
- ✅ 1回のクエリで完結！高速で効率的
- ✅ 実際のWebアプリケーションで超頻出

### JOINの種類

- ✅ **INNER JOIN**：両方にマッチするデータのみ取得
- ✅ **LEFT JOIN**：左のテーブルの全データ + マッチする右のデータ
- ✅ **RIGHT JOIN**：右のテーブルの全データ + マッチする左のデータ（実務では少ない）

### JOIN構文のポイント

- ✅ `ON`句で結合条件を指定（`users.id = posts.user_id`）
- ✅ エイリアス（別名）を使ってスッキリ書く（`users AS u`）
- ✅ 必要なカラムのみ`SELECT`で指定（`SELECT *`は避ける）
- ✅ 3つ以上のテーブルも結合できる

### バイブコーディングのポイント

- ✅ AIに具体的な指示を出す（テーブル名、カラム名、結合条件を明示）
- ✅ 結合条件（ON句）が正しいか確認する
- ✅ JOINの種類（INNER/LEFT）が目的に合っているか確認する
- ✅ 必要なカラムのみ取得しているか確認する

---

## 🚀 次のステップ

おめでとう！JOINの基礎をマスターできたね！✨

次のLesson 02では、**集計関数とグループ化**を学ぶよ！

- COUNT、SUM、AVG、MAX、MINの使い方
- GROUP BYでデータをグループ化
- HAVING句でグループ化後の条件指定
- 実用的なレポート作成（カテゴリ別の投稿数、売上集計など）

JOINと集計関数を組み合わせると、さらに強力なデータ分析ができるようになる！

👉 **[Lesson 02: 集計関数とグループ化へ進む](../02-aggregation/README.md)**

---

**Let's vibe and code! 🎉**

JOINは実務で超重要！AIと一緒に、パズルのピースを組み合わせるように、データを結合していこう！
