# Lesson 01: JOIN（結合）演習問題 - 解答例 ✅

このページでは、演習問題の解答例と解説を掲載しています。

**学習のヒント**：

- まず自分で考えて、AIと一緒に試してみよう
- その後、この解答例を見て確認しよう
- 解答は1つではないことも！別の書き方も試してみよう

---

## 🟢 基礎編：JOINの基本操作

### 問題1-1：INNER JOINの基本

**問題**：

`users`テーブルと`posts`テーブルをINNER JOINして、**ユーザー名**と**投稿タイトル**を取得してください。

**解答**：

```sql
SELECT
    users.name,
    posts.title
FROM
    users
INNER JOIN
    posts
ON
    users.id = posts.user_id;
```

**ポイント**：

- `INNER JOIN`で両方にマッチするデータのみ取得
- `ON users.id = posts.user_id`で結合条件を指定
- 投稿がないユーザー（美咲さん）は結果に含まれない

---

### 問題1-2：エイリアス（別名）を使う

**解答**：

```sql
SELECT
    u.name,
    p.title
FROM
    users AS u
INNER JOIN
    posts AS p
ON
    u.id = p.user_id;
```

**ポイント**：

- `AS`を使ってテーブルに別名を付ける（`AS`は省略可能）
- `users AS u`でusersテーブルにuという別名を付ける
- `u.name`のように短く書ける

---

### 問題1-3：カラムに別名を付ける

**解答**：

```sql
SELECT
    u.name AS user_name,
    p.title AS post_title
FROM
    users AS u
INNER JOIN
    posts AS p
ON
    u.id = p.user_id;
```

**ポイント**：

- カラムにも`AS`で別名を付けられる
- 結果がわかりやすくなる

---

## 🟡 応用編：複数テーブルのJOINや条件付きJOIN

### 問題2-1：3テーブルのJOIN

**解答**：

```sql
SELECT
    u.name AS user_name,
    p.title AS post_title,
    c.name AS category_name
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
INNER JOIN
    categories AS c ON p.category_id = c.id;
```

**ポイント**：

- 2つの`INNER JOIN`を使う
- 結合条件：
  - 1つ目：`users.id = posts.user_id`
  - 2つ目：`posts.category_id = categories.id`
- 結合の順序：users → posts → categories

---

### 問題2-2：WHERE句と組み合わせる

**解答**：

```sql
SELECT
    u.name AS user_name,
    p.title AS post_title,
    c.name AS category_name
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
INNER JOIN
    categories AS c ON p.category_id = c.id
WHERE
    c.name = '技術';  -- 「技術」カテゴリのみ
```

**ポイント**：

- `WHERE`句で条件を指定
- JOINした後にフィルタリングされる
- 結果は「技術」カテゴリの投稿のみ

---

### 問題2-3：LEFT JOINで投稿がないユーザーも表示

**解答**：

```sql
SELECT
    u.name AS user_name,
    p.title AS post_title
FROM
    users AS u
LEFT JOIN
    posts AS p ON u.id = p.user_id;
```

**ポイント**：

- `LEFT JOIN`で左のテーブル（users）の全データを取得
- 投稿がないユーザー（美咲さん）も結果に含まれる
- 投稿がない場合、`post_title`は`NULL`になる

---

## 🔴 実践編：実際のWebアプリで使うクエリ

### 問題3-1：コメントと投稿、ユーザーをJOIN

**解答**：

```sql
SELECT
    u.name AS commenter_name,
    p.title AS post_title,
    c.content AS comment_content
FROM
    comments AS c
INNER JOIN
    posts AS p ON c.post_id = p.id
INNER JOIN
    users AS u ON c.user_id = u.id;
```

**ポイント**：

- commentsテーブルから始める
- postsテーブルをJOIN（`comments.post_id = posts.id`）
- usersテーブルをJOIN（`comments.user_id = users.id`）
- カラムに別名を付けて分かりやすく

---

### 問題3-2：投稿者とコメント投稿者の両方を表示

**解答**：

```sql
SELECT
    post_author.name AS post_author,
    p.title AS post_title,
    commenter.name AS commenter,
    c.content AS comment_content
FROM
    comments AS c
INNER JOIN
    posts AS p ON c.post_id = p.id
INNER JOIN
    users AS post_author ON p.user_id = post_author.id  -- 投稿者
INNER JOIN
    users AS commenter ON c.user_id = commenter.id;     -- コメント投稿者
```

**ポイント**：

- **usersテーブルを2回JOINする**必要がある！
- 1回目：投稿者を取得（`posts.user_id = post_author.id`）
- 2回目：コメント投稿者を取得（`comments.user_id = commenter.id`）
- エイリアスで区別する（`users AS post_author`, `users AS commenter`）
- 同じテーブルを複数回JOINするテクニック！

---

### 問題3-3：ORDER BYで並び替え

**解答**：

```sql
SELECT
    u.name AS commenter_name,
    p.title AS post_title,
    c.content AS comment_content,
    p.created_at
FROM
    comments AS c
INNER JOIN
    posts AS p ON c.post_id = p.id
INNER JOIN
    users AS u ON c.user_id = u.id
ORDER BY
    p.created_at DESC;  -- 新しい順
```

**ポイント**：

- `ORDER BY posts.created_at DESC`で投稿日時が新しい順に並び替え
- `DESC`は降順（新しい→古い）
- `ASC`は昇順（古い→新しい）

---

## 🏆 チャレンジ編：サブクエリやLEFT JOINの応用

### 問題4-1：投稿がないユーザーのみ表示

**解答**：

```sql
SELECT
    u.name AS user_name
FROM
    users AS u
LEFT JOIN
    posts AS p ON u.id = p.user_id
WHERE
    p.id IS NULL;  -- 投稿がないユーザーのみ
```

**ポイント**：

- `LEFT JOIN`で全ユーザーを取得
- `WHERE posts.id IS NULL`で投稿がないユーザーをフィルタ
- LEFT JOINでマッチしなかった場合、右のテーブルの値は`NULL`になる

---

### 問題4-2：各ユーザーの投稿数を表示

**解答**：

```sql
SELECT
    u.name AS user_name,
    COUNT(p.id) AS post_count
FROM
    users AS u
LEFT JOIN
    posts AS p ON u.id = p.user_id
GROUP BY
    u.id, u.name;
```

**ポイント**：

- `LEFT JOIN`を使う（投稿がないユーザーも表示するため）
- `COUNT(posts.id)`で投稿数をカウント
- `GROUP BY users.id`でユーザーごとにグループ化
- Lesson 02で集計関数を詳しく学ぶ！

---

### 問題4-3：サブクエリで投稿が2件以上のユーザーを取得

**解答**：

```sql
SELECT
    u.name AS user_name,
    p.title AS post_title
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
WHERE
    u.id IN (
        -- サブクエリ：投稿が2件以上あるユーザーIDを取得
        SELECT user_id
        FROM posts
        GROUP BY user_id
        HAVING COUNT(*) >= 2
    );
```

**ポイント**：

- サブクエリ（`IN (SELECT ...)`）で投稿が2件以上あるユーザーIDを取得
- `GROUP BY user_id`でユーザーごとにグループ化
- `HAVING COUNT(*) >= 2`で投稿が2件以上の条件を指定
- メインクエリでそのユーザーの投稿を取得
- サブクエリは高度なテクニック！

---

## 🤖 AIと一緒に復習しよう

### 解答例をAIに説明してもらう

解答を見ても理解できない場合は、AIに質問しよう！

**質問例**：

```text
「このSQLクエリを説明してください。
特に、LEFT JOINとWHERE posts.id IS NULLの組み合わせで、
なぜ投稿がないユーザーのみが取得できるのか教えてください。

SELECT u.name
FROM users AS u
LEFT JOIN posts AS p ON u.id = p.user_id
WHERE p.id IS NULL;
」
```

### 別の書き方を試してみる

AIに、別の書き方を提案してもらおう！

**質問例**：

```text
「問題4-2（各ユーザーの投稿数を表示）を、
サブクエリを使って書き直すことはできますか？」
```

---

## 📊 補足：よくある間違いと対策

### 間違い1：WHERE句とHAVING句の混同

**間違った例**：

```sql
-- 間違い：集計前にフィルタすべきところでHAVINGを使っている
SELECT u.name, COUNT(p.id) AS post_count
FROM users AS u
LEFT JOIN posts AS p ON u.id = p.user_id
GROUP BY u.id
HAVING u.name = '太郎';  -- 間違い
```

**正しい例**：

```sql
-- 正しい：集計前にフィルタする場合はWHERE句を使う
SELECT u.name, COUNT(p.id) AS post_count
FROM users AS u
LEFT JOIN posts AS p ON u.id = p.user_id
WHERE u.name = '太郎'
GROUP BY u.id;
```

**ポイント**：

- `WHERE`：集計前にフィルタ
- `HAVING`：集計後にフィルタ
- Lesson 02で詳しく学ぶ！

---

### 間違い2：GROUP BYに必要なカラムが不足

**間違った例**：

```sql
-- 間違い：u.nameがGROUP BYに含まれていない
SELECT u.name, COUNT(p.id) AS post_count
FROM users AS u
LEFT JOIN posts AS p ON u.id = p.user_id
GROUP BY u.id;  -- u.nameがない
```

**正しい例**：

```sql
-- 正しい：SELECT句のカラムはGROUP BYに含める
SELECT u.name, COUNT(p.id) AS post_count
FROM users AS u
LEFT JOIN posts AS p ON u.id = p.user_id
GROUP BY u.id, u.name;  -- u.nameを追加
```

**ポイント**：

- `SELECT`句で指定した非集計カラムは、`GROUP BY`に含める必要がある
- MySQLのバージョンによってはエラーになる

---

### 間違い3：結合条件の間違い

**間違った例**：

```sql
-- 間違い：結合条件が間違っている
SELECT u.name, p.title
FROM users AS u
INNER JOIN posts AS p
ON u.name = p.title;  -- 間違い！nameとtitleを比較している
```

**正しい例**：

```sql
-- 正しい：idとuser_idを比較
SELECT u.name, p.title
FROM users AS u
INNER JOIN posts AS p
ON u.id = p.user_id;  -- 正しい
```

**ポイント**：

- 結合条件は外部キーを使う（`users.id = posts.user_id`）
- 関係のないカラムを比較しないように注意

---

## ✅ まとめ

演習問題、お疲れさま！✨

### 学んだこと

- ✅ INNER JOINで両方にマッチするデータのみ取得
- ✅ LEFT JOINで片方のテーブルの全データを取得
- ✅ 3つ以上のテーブルもJOINできる
- ✅ WHERE句やORDER BYと組み合わせられる
- ✅ サブクエリで複雑な条件を指定できる
- ✅ 同じテーブルを複数回JOINする方法

### 次のステップ

JOINをマスターしたら、次は**集計関数とグループ化**を学ぼう！

- COUNT、SUM、AVGでデータを集計
- GROUP BYでグループ化
- HAVINGで集計後にフィルタ

JOINと集計関数を組み合わせると、超強力なデータ分析ができるようになる！

👉 **[Lesson 02: 集計関数とグループ化へ進む](../../../02-aggregation/README.md)**

---

**Let's vibe and code! 🎉**

JOINの演習、完璧！AIと一緒に、どんどん複雑なクエリにチャレンジしていこう！
