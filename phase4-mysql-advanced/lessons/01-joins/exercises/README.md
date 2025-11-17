# Lesson 01: JOIN（結合）演習問題 💪

このページでは、JOINの理解を深めるための演習問題を用意しています。

**難易度**：

- 🟢 **基礎編**：JOINの基本操作を練習
- 🟡 **応用編**：複数テーブルのJOINや条件付きJOIN
- 🔴 **実践編**：実際のWebアプリで使うクエリ
- 🏆 **チャレンジ編**：サブクエリやLEFT JOINの応用

---

## 📋 準備：サンプルデータベース

演習問題を始める前に、以下のSQLを実行して、サンプルデータベースを作成してください。

```sql
-- データベース作成
CREATE DATABASE IF NOT EXISTS blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_db;

-- usersテーブル
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email) VALUES
('太郎', 'taro@example.com'),
('花子', 'hanako@example.com'),
('次郎', 'jiro@example.com'),
('美咲', 'misaki@example.com');

-- categoriesテーブル
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

INSERT INTO categories (name) VALUES
('技術'),
('日記'),
('趣味'),
('旅行');

-- postsテーブル
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

INSERT INTO posts (user_id, category_id, title, content) VALUES
(1, 1, '初めての投稿', 'こんにちは！'),
(1, 1, 'PHP学習中', '楽しいです！'),
(2, 2, '花子の日記', 'よろしくお願いします'),
(2, 3, '趣味について', '読書が好きです'),
(3, 4, '旅行記', '京都に行ってきました');

-- commentsテーブル
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO comments (post_id, user_id, content) VALUES
(1, 2, '素晴らしい投稿ですね！'),
(1, 3, 'ありがとうございます'),
(2, 2, 'PHP楽しいですよね'),
(3, 1, 'よろしく！');
```

---

## 🟢 基礎編：JOINの基本操作

### 問題1-1：INNER JOINの基本

**問題**：

`users`テーブルと`posts`テーブルをINNER JOINして、**ユーザー名**と**投稿タイトル**を取得してください。

**期待する結果**（一部）：

| name | title        |
| ---- | ------------ |
| 太郎 | 初めての投稿 |
| 太郎 | PHP学習中    |
| 花子 | 花子の日記   |
| ...  | ...          |

<details>
<summary>💡 ヒント</summary>

- `INNER JOIN`を使う
- 結合条件は`users.id = posts.user_id`
- `SELECT`でユーザー名と投稿タイトルを指定

</details>

---

### 問題1-2：エイリアス（別名）を使う

**問題**：

問題1-1のクエリを、テーブル名に**エイリアス（別名）**を使って書き直してください。

- `users`テーブルに別名`u`
- `posts`テーブルに別名`p`

<details>
<summary>💡 ヒント</summary>

- `FROM users AS u`のように別名を付ける
- `u.name`, `p.title`のように別名を使う

</details>

---

### 問題1-3：カラムに別名を付ける

**問題**：

問題1-2のクエリを、カラムにもわかりやすい別名を付けて書き直してください。

- `name`を`user_name`に
- `title`を`post_title`に

**期待する結果**：

| user_name | post_title   |
| --------- | ------------ |
| 太郎      | 初めての投稿 |
| ...       | ...          |

<details>
<summary>💡 ヒント</summary>

- `SELECT u.name AS user_name`のように`AS`を使う

</details>

---

## 🟡 応用編：複数テーブルのJOINや条件付きJOIN

### 問題2-1：3テーブルのJOIN

**問題**：

`users`、`posts`、`categories`の3つのテーブルをJOINして、以下を取得してください：

- ユーザー名
- 投稿タイトル
- カテゴリ名

**期待する結果**（一部）：

| user_name | post_title   | category_name |
| --------- | ------------ | ------------- |
| 太郎      | 初めての投稿 | 技術          |
| 太郎      | PHP学習中    | 技術          |
| ...       | ...          | ...           |

<details>
<summary>💡 ヒント</summary>

- 2つの`INNER JOIN`を使う
- 結合条件：
  - `users.id = posts.user_id`
  - `posts.category_id = categories.id`

</details>

---

### 問題2-2：WHERE句と組み合わせる

**問題**：

問題2-1のクエリに、**WHERE句**を追加して、**「技術」カテゴリの投稿のみ**を取得してください。

**期待する結果**：

| user_name | post_title   | category_name |
| --------- | ------------ | ------------- |
| 太郎      | 初めての投稿 | 技術          |
| 太郎      | PHP学習中    | 技術          |

<details>
<summary>💡 ヒント</summary>

- `WHERE categories.name = '技術'`を追加

</details>

---

### 問題2-3：LEFT JOINで投稿がないユーザーも表示

**問題**：

`users`テーブルと`posts`テーブルを**LEFT JOIN**して、**投稿がないユーザーも含めて**全ユーザーと投稿を取得してください。

**期待する結果**（一部）：

| user_name | post_title   |
| --------- | ------------ |
| 太郎      | 初めての投稿 |
| 太郎      | PHP学習中    |
| 花子      | 花子の日記   |
| 美咲      | NULL         |

（美咲さんは投稿がないので、NULLが表示される）

<details>
<summary>💡 ヒント</summary>

- `INNER JOIN`を`LEFT JOIN`に変更

</details>

---

## 🔴 実践編：実際のWebアプリで使うクエリ

### 問題3-1：コメントと投稿、ユーザーをJOIN

**問題**：

`comments`、`posts`、`users`の3つのテーブルをJOINして、以下を取得してください：

- コメントした人の名前（コメント投稿者）
- 投稿タイトル（コメントが付いている投稿）
- コメント内容

**期待する結果**（一部）：

| commenter_name | post_title   | comment_content          |
| -------------- | ------------ | ------------------------ |
| 花子           | 初めての投稿 | 素晴らしい投稿ですね！   |
| 次郎           | 初めての投稿 | ありがとうございます     |
| ...            | ...          | ...                      |

<details>
<summary>💡 ヒント</summary>

- `comments`テーブルから始める
- `posts`テーブルをJOIN（`comments.post_id = posts.id`）
- `users`テーブルをJOIN（`comments.user_id = users.id`）
- カラムに別名を付けると分かりやすい

</details>

---

### 問題3-2：投稿者とコメント投稿者の両方を表示

**問題**：

`comments`、`posts`、`users`をJOINして、以下を取得してください：

- **投稿者の名前**（投稿を書いた人）
- **投稿タイトル**
- **コメント投稿者の名前**（コメントを書いた人）
- **コメント内容**

**期待する結果**（一部）：

| post_author | post_title   | commenter   | comment_content          |
| ----------- | ------------ | ----------- | ------------------------ |
| 太郎        | 初めての投稿 | 花子        | 素晴らしい投稿ですね！   |
| 太郎        | 初めての投稿 | 次郎        | ありがとうございます     |
| ...         | ...          | ...         | ...                      |

<details>
<summary>💡 ヒント</summary>

- `users`テーブルを2回JOINする必要がある！
- 1回目：投稿者を取得（`posts.user_id = users.id`）
- 2回目：コメント投稿者を取得（`comments.user_id = users.id`）
- エイリアスを使って区別する（`users AS post_author`, `users AS commenter`）

</details>

---

### 問題3-3：ORDER BYで並び替え

**問題**：

問題3-1のクエリに、**投稿日時が新しい順**に並び替える`ORDER BY`を追加してください。

<details>
<summary>💡 ヒント</summary>

- `ORDER BY posts.created_at DESC`を追加
- `DESC`は降順（新しい→古い）

</details>

---

## 🏆 チャレンジ編：サブクエリやLEFT JOINの応用

### 問題4-1：投稿がないユーザーのみ表示

**問題**：

**LEFT JOIN**を使って、**投稿がないユーザー**（美咲さん）のみを取得してください。

**期待する結果**：

| user_name |
| --------- |
| 美咲      |

<details>
<summary>💡 ヒント</summary>

- `LEFT JOIN`で全ユーザーを取得
- `WHERE posts.id IS NULL`で投稿がないユーザーをフィルタ

</details>

---

### 問題4-2：各ユーザーの投稿数を表示

**問題**：

`users`と`posts`をJOINして、**各ユーザーの投稿数**を表示してください。

**期待する結果**：

| user_name | post_count |
| --------- | ---------- |
| 太郎      | 2          |
| 花子      | 2          |
| 次郎      | 1          |
| 美咲      | 0          |

<details>
<summary>💡 ヒント</summary>

- `LEFT JOIN`を使う（投稿がないユーザーも表示するため）
- `COUNT(posts.id)`で投稿数をカウント
- `GROUP BY users.id`でユーザーごとにグループ化
- Lesson 02で集計関数を詳しく学ぶ！

</details>

---

### 問題4-3：サブクエリで投稿が2件以上のユーザーを取得

**問題**：

**サブクエリ**を使って、**投稿が2件以上あるユーザー**の投稿を取得してください。

**期待する結果**（一部）：

| user_name | post_title   |
| --------- | ------------ |
| 太郎      | 初めての投稿 |
| 太郎      | PHP学習中    |
| 花子      | 花子の日記   |
| 花子      | 趣味について |

<details>
<summary>💡 ヒント</summary>

- サブクエリで投稿が2件以上あるユーザーIDを取得
- メインクエリでそのユーザーの投稿を取得
- `WHERE user_id IN (SELECT ...)`を使う

</details>

---

## 🎯 AIと一緒に演習しよう

### AIへの指示例

**基礎編の問題をAIに依頼する場合**：

```text
「blog_dbデータベースのusersテーブルとpostsテーブルをINNER JOINして、
ユーザー名（users.name）と投稿タイトル（posts.title）を取得するSQLを書いてください。
結合条件は、users.idとposts.user_idです。」
```

**応用編の問題をAIに依頼する場合**：

```text
「users、posts、categoriesの3つのテーブルをINNER JOINして、
ユーザー名、投稿タイトル、カテゴリ名を取得するSQLを書いてください。
結合条件：
- users.id = posts.user_id
- posts.category_id = categories.id
さらに、WHERE句で「技術」カテゴリの投稿のみを取得してください。」
```

### 生成されたSQLのチェックポイント

- [ ] 結合条件（ON句）が正しいか
- [ ] JOINの種類（INNER/LEFT）が適切か
- [ ] 必要なカラムのみSELECTされているか
- [ ] エイリアス（別名）が使われているか

---

## 📝 解答例

解答例は別ファイルにまとめています。

まずは自分で考えて、AIと一緒に試してみよう！その後、解答例を見て確認しよう！

👉 **[解答例を見る](solutions/README.md)**

---

## ✅ 演習完了チェックリスト

すべての演習を終えたら、以下をチェックしよう！

- [ ] INNER JOINの基本を理解した
- [ ] エイリアス（別名）を使ってスッキリしたクエリが書ける
- [ ] 3つ以上のテーブルをJOINできる
- [ ] LEFT JOINで「データがないレコード」も表示できる
- [ ] WHERE句やORDER BYと組み合わせられる
- [ ] サブクエリの基礎を理解した
- [ ] AIに具体的な指示を出してJOINクエリを生成できる
- [ ] 生成されたJOINクエリの結合条件を確認できる

---

**Let's vibe and code! 🎉**

JOINの演習、お疲れさま！実践的なクエリが書けるようになったね！

次は、Lesson 02で集計関数とグループ化を学ぼう！

👉 **[Lesson 02: 集計関数とグループ化へ進む](../../02-aggregation/README.md)**
