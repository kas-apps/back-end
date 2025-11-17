# Lesson 02: 集計関数とグループ化 📈

**学習目標**：データを集計・分析する方法を理解し、AIと一緒にビジネスレポートを作成できるようになる！

---

## 📖 このレッスンで学ぶこと

- 集計関数の基礎（COUNT, SUM, AVG, MAX, MIN）
- GROUP BY（データをグループ化して集計）
- HAVING句（グループ化後の条件指定）
- ORDER BYと集計関数（集計結果の並び替え）
- 実用的なレポート作成（売上集計、ユーザーアクティビティ）

---

## 🎯 なぜ集計関数を学ぶの？（Why）

### 実際のWebアプリでよくあるニーズ

「カテゴリ別の投稿数を知りたい！」
「ユーザー別の売上合計を計算したい！」
「平均評価が高い商品を表示したい！」

Phase 3で学んだSELECTは、個々のデータを取得するのに便利だったよね。でも、**データ分析**や**レポート作成**には、集計関数が必要！

### 集計関数がないと…

**悪い方法**（集計関数なし）：

```text
ステップ1：全投稿を取得
ステップ2：PHPで投稿をカテゴリごとに分類
ステップ3：PHPで各カテゴリの投稿数をカウント
```

問題点：

- すべてのデータを取得する必要がある（遅い！）
- PHPで処理が複雑
- データが多いとメモリ不足に！

**良い方法**（集計関数あり）：

```sql
-- 1回のクエリで、カテゴリ別の投稿数を取得！
SELECT category_id, COUNT(*) AS post_count
FROM posts
GROUP BY category_id;
```

集計関数を使えば、**データベース側で集計**！高速で効率的！✨

---

## 🧩 集計関数の基本概念（What）

### アナロジー：仕分け箱に分けて数える

集計関数とGROUP BYは「仕分け箱に分けて数える」みたいなもの！

```text
イメージ：

投稿データ（全部ごちゃ混ぜ）
↓
GROUP BY category_id（カテゴリごとに仕分け）
↓
箱1：技術カテゴリの投稿（2件）
箱2：日記カテゴリの投稿（1件）
箱3：趣味カテゴリの投稿（1件）
↓
COUNT(*)で各箱の件数を数える！
```

### 主な集計関数

| 関数       | 説明                           | 使用例                          |
| ---------- | ------------------------------ | ------------------------------- |
| **COUNT()** | 件数をカウント                 | `COUNT(*)`：全件数              |
| **SUM()**   | 合計を計算                     | `SUM(price)`：価格の合計        |
| **AVG()**   | 平均を計算                     | `AVG(rating)`：平均評価         |
| **MAX()**   | 最大値を取得                   | `MAX(price)`：最高価格          |
| **MIN()**   | 最小値を取得                   | `MIN(price)`：最低価格          |

---

## 💻 実例で理解しよう（How）

### 準備：サンプルデータを作成

```sql
-- データベース作成
CREATE DATABASE IF NOT EXISTS shop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shop_db;

-- productsテーブル
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO products (name, category_id, price, stock) VALUES
('ノートパソコン', 1, 80000.00, 5),
('スマートフォン', 1, 60000.00, 10),
('タブレット', 1, 40000.00, 7),
('PHP入門書', 2, 3000.00, 20),
('MySQL入門書', 2, 3500.00, 15),
('コーヒー豆', 3, 1500.00, 50),
('紅茶', 3, 1200.00, 30);

-- categoriesテーブル
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

INSERT INTO categories (name) VALUES
('家電'),
('書籍'),
('食品');
```

---

### 実例1：COUNT() - 件数をカウント

**クエリ**：

```sql
-- 商品の総数をカウント
SELECT COUNT(*) AS total_products
FROM products;
```

**実行結果**：

| total_products |
| -------------- |
| 7              |

**ポイント**：

- `COUNT(*)`は全行数をカウント
- `AS total_products`で結果に別名を付ける

---

### 実例2：SUM() - 合計を計算

**クエリ**：

```sql
-- 全商品の在庫数の合計
SELECT SUM(stock) AS total_stock
FROM products;
```

**実行結果**：

| total_stock |
| ----------- |
| 137         |

**ポイント**：

- `SUM(stock)`で在庫数を合計
- 数値カラムのみ使用可能

---

### 実例3：AVG() - 平均を計算

**クエリ**：

```sql
-- 商品の平均価格
SELECT AVG(price) AS average_price
FROM products;
```

**実行結果**：

| average_price |
| ------------- |
| 27028.57      |

**ポイント**：

- `AVG(price)`で価格の平均を計算
- 小数点以下も表示される

---

### 実例4：MAX() と MIN() - 最大値と最小値

**クエリ**：

```sql
-- 最高価格と最低価格
SELECT
    MAX(price) AS max_price,
    MIN(price) AS min_price
FROM products;
```

**実行結果**：

| max_price | min_price |
| --------- | --------- |
| 80000.00  | 1200.00   |

---

### 実例5：GROUP BY - カテゴリ別に集計

**クエリ**：

```sql
-- カテゴリ別の商品数
SELECT
    category_id,
    COUNT(*) AS product_count
FROM
    products
GROUP BY
    category_id;
```

**実行結果**：

| category_id | product_count |
| ----------- | ------------- |
| 1           | 3             |
| 2           | 2             |
| 3           | 2             |

**ポイント**：

- `GROUP BY category_id`でカテゴリごとにグループ化
- `COUNT(*)`で各グループの件数をカウント

---

### 実例6：JOINと集計関数の組み合わせ

**クエリ**：

```sql
-- カテゴリ名と商品数を一緒に表示
SELECT
    c.name AS category_name,
    COUNT(p.id) AS product_count,
    SUM(p.stock) AS total_stock
FROM
    categories AS c
LEFT JOIN
    products AS p ON c.id = p.category_id
GROUP BY
    c.id, c.name;
```

**実行結果**：

| category_name | product_count | total_stock |
| ------------- | ------------- | ----------- |
| 家電          | 3             | 22          |
| 書籍          | 2             | 35          |
| 食品          | 2             | 80          |

**ポイント**：

- LEFT JOINで全カテゴリを表示
- GROUP BYでカテゴリごとにグループ化
- 複数の集計関数を同時に使える

---

### 実例7：HAVING句 - グループ化後の条件指定

**クエリ**：

```sql
-- 商品数が2件以上のカテゴリのみ表示
SELECT
    c.name AS category_name,
    COUNT(p.id) AS product_count
FROM
    categories AS c
LEFT JOIN
    products AS p ON c.id = p.category_id
GROUP BY
    c.id, c.name
HAVING
    COUNT(p.id) >= 2;
```

**実行結果**：

| category_name | product_count |
| ------------- | ------------- |
| 家電          | 3             |
| 書籍          | 2             |
| 食品          | 2             |

**ポイント**：

- `HAVING`は集計後にフィルタリング
- `WHERE`は集計前にフィルタリング
- この違いが超重要！

---

### WHERE句とHAVING句の違い

**WHERE句**：集計前にフィルタ

```sql
-- 価格が3000円以上の商品のみカウント
SELECT
    category_id,
    COUNT(*) AS product_count
FROM
    products
WHERE
    price >= 3000  -- 集計前にフィルタ
GROUP BY
    category_id;
```

**HAVING句**：集計後にフィルタ

```sql
-- 商品数が2件以上のカテゴリのみ表示
SELECT
    category_id,
    COUNT(*) AS product_count
FROM
    products
GROUP BY
    category_id
HAVING
    COUNT(*) >= 2;  -- 集計後にフィルタ
```

**違いのまとめ**：

| 項目             | WHERE句        | HAVING句       |
| ---------------- | -------------- | -------------- |
| フィルタのタイミング | 集計前         | 集計後         |
| 使える場所       | SELECT全般     | GROUP BYの後のみ |
| 集計関数を使える？ | ❌ 使えない    | ✅ 使える      |

---

### 実例8：ORDER BYで集計結果を並び替え

**クエリ**：

```sql
-- カテゴリ別の商品数を多い順に表示
SELECT
    c.name AS category_name,
    COUNT(p.id) AS product_count
FROM
    categories AS c
LEFT JOIN
    products AS p ON c.id = p.category_id
GROUP BY
    c.id, c.name
ORDER BY
    product_count DESC;  -- 商品数が多い順
```

**実行結果**：

| category_name | product_count |
| ------------- | ------------- |
| 家電          | 3             |
| 書籍          | 2             |
| 食品          | 2             |

---

### 実例9：実用的なレポート - カテゴリ別売上集計

**ordersテーブルを追加**：

```sql
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO orders (product_id, quantity) VALUES
(1, 2),  -- ノートパソコン × 2
(2, 1),  -- スマートフォン × 1
(4, 5),  -- PHP入門書 × 5
(6, 10); -- コーヒー豆 × 10
```

**クエリ**：

```sql
-- カテゴリ別の売上集計
SELECT
    c.name AS category_name,
    SUM(o.quantity * p.price) AS total_sales,
    SUM(o.quantity) AS total_quantity
FROM
    categories AS c
LEFT JOIN
    products AS p ON c.id = p.category_id
LEFT JOIN
    orders AS o ON p.id = o.product_id
GROUP BY
    c.id, c.name
ORDER BY
    total_sales DESC;
```

**実行結果**：

| category_name | total_sales | total_quantity |
| ------------- | ----------- | -------------- |
| 家電          | 220000.00   | 3              |
| 書籍          | 15000.00    | 5              |
| 食品          | 15000.00    | 10             |

**ポイント**：

- `SUM(o.quantity * p.price)`で売上合計を計算
- JOINと集計関数を組み合わせた実用的なクエリ
- ビジネスレポートで超よく使うパターン！

---

## 🤖 バイブコーディング実践

### AIへの指示例

#### 良い指示の例1：カテゴリ別の集計

```text
「productsテーブルとcategoriesテーブルをJOINして、
カテゴリ別の商品数（COUNT）と在庫合計（SUM）を取得してください。
GROUP BYでカテゴリごとにグループ化し、
ORDER BYで商品数が多い順に並び替えてください。」
```

#### 良い指示の例2：HAVING句を使った条件付き集計

```text
「カテゴリ別の商品数を集計してください。
ただし、商品数が2件以上のカテゴリのみを表示してください。
HAVING句を使って、集計後にフィルタしてください。」
```

#### 良い指示の例3：売上レポート

```text
「カテゴリ別の売上合計を計算するSQLを書いてください。
orders、products、categoriesの3つのテーブルをJOINし、
SUM(quantity * price)で売上を計算してください。
売上が多い順に並び替えてください。」
```

### 生成された集計クエリのチェックポイント

- [ ] **GROUP BYが適切か**
  - SELECT句の非集計カラムがGROUP BYに含まれているか
  - グループ化するカラムが正しいか

- [ ] **集計関数が適切か**
  - COUNT、SUM、AVGなどが正しく使われているか
  - カラムの型に合った集計関数か（SUMは数値のみ）

- [ ] **HAVINGとWHEREの使い分け**
  - 集計前のフィルタはWHERE句
  - 集計後のフィルタはHAVING句

- [ ] **JOINと集計の組み合わせ**
  - 必要なテーブルがすべてJOINされているか
  - LEFT JOINが適切に使われているか

### よくある問題と修正方法

#### 問題1：GROUP BYに必要なカラムが不足

**悪い例**：

```sql
-- エラーになる可能性がある
SELECT category_id, name, COUNT(*) AS product_count
FROM products
GROUP BY category_id;  -- nameがGROUP BYにない
```

**修正**：

```sql
-- SELECT句のカラムはGROUP BYに含める
SELECT category_id, MIN(name) AS name, COUNT(*) AS product_count
FROM products
GROUP BY category_id;
```

**AIへの修正指示**：

```text
「SELECT句のカラムをGROUP BYに含めてください。
category_idとnameの両方をGROUP BYに追加してください。」
```

---

## 💪 演習問題

演習問題は別ファイルにまとめています。

👉 **[演習問題を見る](exercises/README.md)**

---

## ✅ まとめ

### 集計関数

- ✅ **COUNT()**：件数をカウント
- ✅ **SUM()**：合計を計算
- ✅ **AVG()**：平均を計算
- ✅ **MAX()** / **MIN()**：最大値・最小値

### GROUP BYとHAVING

- ✅ **GROUP BY**：データをグループ化
- ✅ **HAVING**：集計後にフィルタ
- ✅ **WHERE**：集計前にフィルタ

### 実用パターン

- ✅ JOINと集計関数の組み合わせ
- ✅ 売上レポート作成
- ✅ ORDER BYで集計結果を並び替え

---

## 🚀 次のステップ

次のLesson 03では、**インデックス**を学ぶよ！

集計クエリのパフォーマンスを向上させる方法を学ぼう！

👉 **[Lesson 03: インデックスへ進む](../03-indexes/README.md)**

---

**Let's vibe and code! 🎉**

集計関数は、データ分析の魔法！AIと一緒に、ビジネスに役立つレポートを作っていこう！
