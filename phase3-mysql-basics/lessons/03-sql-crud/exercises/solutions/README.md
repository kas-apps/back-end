# Lesson 03: SQL基本操作（CRUD） - 解答例 ✅

演習お疲れさま！ここでは各演習の解答例と詳しい解説を紹介するよ。

---

## 🎯 基礎編：SELECT文

### 演習 1-1: 全件取得 ✅

**解答**：

```sql
SELECT * FROM books;
```

**実行結果**：

booksテーブルの全データが表示される。

**解説**：

- `*` は全カラムを意味する
- WHERE句がないので、全レコードが取得される

---

### 演習 1-2: 特定カラムの取得 ✅

**解答**：

```sql
SELECT title, price FROM books;
```

**実行結果**：

| title             | price |
| ----------------- | ----- |
| PHP入門           | 3000  |
| Python基礎        | 2800  |
| データベース設計  | 3500  |
| ...               | ...   |

**解説**：

- `title` と `price` カラムだけが表示される
- 必要なカラムだけを指定することで、パフォーマンスが向上

---

### 演習 1-3: WHERE句で条件指定 ✅

**解答**：

```sql
SELECT * FROM books WHERE price >= 2000;
```

**実行結果**：

価格が2000円以上の本だけが表示される。

**解説**：

- `WHERE price >= 2000` で条件指定
- `>=` は「以上」を意味する

---

### 演習 1-4: LIKE（部分一致検索） ✅

**解答**：

```sql
SELECT * FROM books WHERE title LIKE '%PHP%';
```

**実行結果**：

タイトルに「PHP」が含まれる本だけが表示される。

**解説**：

- `LIKE '%PHP%'` で部分一致検索
- `%` は任意の文字列を意味する

---

### 演習 1-5: ORDER BY（並び替え） ✅

**解答**：

```sql
SELECT * FROM books ORDER BY price ASC;
```

**実行結果**：

価格の安い順（昇順）で本が表示される。

**解説**：

- `ORDER BY price ASC` で昇順に並び替え
- `ASC` は昇順（Ascending）、`DESC` は降順（Descending）

---

### 演習 1-6: LIMIT（件数制限） ✅

**解答**：

```sql
SELECT * FROM books ORDER BY price DESC LIMIT 3;
```

**実行結果**：

価格の高い順で上位3件が表示される。

**解説**：

- `ORDER BY price DESC` で降順に並び替え
- `LIMIT 3` で上位3件だけを取得

---

## 🚀 応用編：INSERT、UPDATE、DELETE

### 演習 2-1: データの挿入（INSERT） ✅

**解答**：

```sql
INSERT INTO books (title, author, price, stock) VALUES ('MySQL入門', '鈴木太郎', 2800, 8);
```

**実行後の確認**：

```sql
SELECT * FROM books WHERE title = 'MySQL入門';
```

**実行結果**：

| id  | title      | author     | price | stock | created_at          |
| --- | ---------- | ---------- | ----- | ----- | ------------------- |
| ?   | MySQL入門  | 鈴木太郎   | 2800  | 8     | 2024-01-01 10:00:00 |

**解説**：

- `id` と `created_at` は自動で設定される
- 文字列はシングルクォート `'...'` で囲む

---

### 演習 2-2: 複数行の挿入 ✅

**解答**：

```sql
INSERT INTO books (title, author, price, stock) VALUES
('JavaScript基礎', '山田花子', 2500, 12),
('HTML&CSS入門', '田中次郎', 2200, 15);
```

**実行後の確認**：

```sql
SELECT * FROM books WHERE title IN ('JavaScript基礎', 'HTML&CSS入門');
```

**実行結果**：

2件の本が挿入されている。

**解説**：

- 複数行をカンマで区切って一度に挿入
- 一度に挿入する方が効率的

---

### 演習 2-3: データの更新（UPDATE） ✅

**解答**：

```sql
UPDATE books SET price = 3200 WHERE id = 1;
```

**実行後の確認**：

```sql
SELECT * FROM books WHERE id = 1;
```

**実行結果**：

id=1の本の価格が `3200` に更新されている。

**解説**：

- `WHERE id = 1` で、id=1のレコードだけを更新
- WHERE句がないと、全てのレコードが更新されてしまう！

---

### 演習 2-4: 複数カラムの更新 ✅

**解答**：

```sql
UPDATE books SET stock = 8, price = 2600 WHERE id = 2;
```

**実行後の確認**：

```sql
SELECT * FROM books WHERE id = 2;
```

**実行結果**：

id=2の本の在庫が `8`、価格が `2600` に更新されている。

**解説**：

- カンマで区切って複数カラムを更新
- WHERE句は必須！

---

### 演習 2-5: データの削除（DELETE） ✅

**解答**：

```sql
DELETE FROM books WHERE id = 3;
```

**実行後の確認**：

```sql
SELECT * FROM books WHERE id = 3;
```

**実行結果**：

id=3のレコードが削除されているので、0件。

**解説**：

- `WHERE id = 3` で、id=3のレコードだけを削除
- WHERE句がないと、全てのレコードが削除されてしまう！
- 削除は取り消せないので注意！

---

## 🔒 セキュリティチャレンジ：WHERE句の重要性

### 演習 3-1: WHERE句なしのUPDATEの危険性を体験 ✅

**ステップ2：危険なUPDATE文を実行**

```sql
UPDATE books SET stock = 0;
```

**実行後の確認**：

```sql
SELECT * FROM books;
```

**実行結果**：

全ての本の在庫が `0` になっている！

**ステップ4：元に戻す**

resources/insert-data.sql を再実行して、データを復元。

**学んだこと**：

- WHERE句がないUPDATE文は超危険！
- 本番環境でこれをやると、全てのデータが壊れる
- 必ずWHERE句を付ける習慣をつける

---

### 演習 3-2: 実行前にSELECTで確認 ✅

**ステップ1：まずSELECTで対象を確認**

```sql
SELECT * FROM books WHERE price >= 3000;
```

**実行結果**：

価格が3000円以上の本が何件か表示される。

**ステップ2：WHERE句を同じにしてUPDATE**

```sql
UPDATE books SET stock = 10 WHERE price >= 3000;
```

**ステップ3：結果を確認**

```sql
SELECT * FROM books WHERE price >= 3000;
```

**実行結果**：

対象の本の在庫が `10` に更新されている。

**学んだこと**：

- 実行前にSELECTで対象を確認する習慣をつける
- WHERE句を同じにすれば、安全に実行できる

---

## 💡 チャレンジ問題

### チャレンジ 1: 複雑な検索 ✅

**解答**：

```sql
SELECT * FROM books
WHERE price BETWEEN 2000 AND 3000
AND stock >= 10
ORDER BY price ASC;
```

**実行結果**：

- 価格が2000円以上3000円以下
- 在庫が10個以上
- 価格の安い順

の条件に合う本が表示される。

**解説**：

- `BETWEEN 2000 AND 3000` で範囲指定
- `AND` で複数条件を結合
- `ORDER BY price ASC` で昇順に並び替え

---

### チャレンジ 2: AIにSQL生成を依頼 ✅

**AIが生成するSQL**：

```sql
SELECT * FROM books
WHERE title LIKE '%入門%'
AND price <= 3000
ORDER BY price ASC;
```

**実行結果**：

タイトルに「入門」が含まれ、価格が3000円以下の本が、価格の安い順で表示される。

**解説**：

- AIに具体的な条件を指示することで、正確なSQLが生成される
- 生成されたSQLを実行して、正しく動作することを確認する

---

### チャレンジ 3: テーブルの作成 ✅

**解答**：

```sql
CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**確認**：

```sql
SHOW CREATE TABLE authors;
```

または、phpMyAdminの「構造」タブでテーブル構造を確認。

**解説**：

- `AUTO_INCREMENT`：idが自動で増える
- `PRIMARY KEY`：主キー
- `NOT NULL`：必須項目
- `UNIQUE`：重複を許可しない
- `DEFAULT CURRENT_TIMESTAMP`：現在時刻を自動設定
- `ENGINE=InnoDB`：トランザクション対応のストレージエンジン
- `DEFAULT CHARSET=utf8mb4`：日本語・絵文字対応

---

## 🎓 まとめ

演習を通じて、SQL基本操作（CRUD）をマスターしたね！

### 重要なポイント

- ✅ **SELECT文**：データの取得（WHERE、ORDER BY、LIMIT）
- ✅ **INSERT文**：データの挿入（複数行の挿入）
- ✅ **UPDATE文**：データの更新（WHERE句必須！）
- ✅ **DELETE文**：データの削除（WHERE句必須！）
- ✅ **WHERE句の重要性**：必ず条件を指定する
- ✅ **実行前の確認**：SELECTで対象を確認する習慣

### 次のステップ

次のLesson 04では、データ型とテーブル設計を学んでいくよ！

- 数値型（INT、DECIMAL、FLOAT）
- 文字列型（VARCHAR、TEXT、CHAR）
- 日付・時刻型（DATE、DATETIME、TIMESTAMP）

適切なデータ型を選ぶことで、パフォーマンスとデータ整合性が向上する！

👉 **[Lesson 04: データ型とテーブル設計へ進む](../../04-data-types/README.md)**

---

**Let's vibe and code! 🎉**
