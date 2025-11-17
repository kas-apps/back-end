# Lesson 02: phpMyAdminの使い方 - 解答例 ✅

演習お疲れさま！ここでは各演習の解答例と詳しい解説を紹介するよ。

---

## 🎯 基礎編

### 演習 1-1: データベースの作成 ✅

**GUI操作**：

1. phpMyAdminにアクセス（`http://localhost:8888/phpMyAdmin/`）
2. 左側サイドバーの「**新規作成**」をクリック
3. データベース名に `practice_db` を入力
4. 照合順序で `utf8mb4_general_ci` を選択
5. 「**作成**」ボタンをクリック

**成功メッセージ**：

```text
データベース practice_db を作成しました。
```

**SQL版（参考）**：

```sql
CREATE DATABASE practice_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
```

---

### 演習 1-2: テーブルの作成（GUI） ✅

**GUI操作**：

1. 左側サイドバーから `practice_db` をクリック
2. 「**新しいテーブルを作成する**」セクションで：
   - 名前：`products`
   - カラム数：`5`
   - 「**実行**」をクリック

3. 各カラムを以下のように設定：

**id カラム**：
- 名前：`id`
- タイプ：`INT`
- インデックス：`PRIMARY`
- A_I（Auto Increment）：✅ チェック

**name カラム**：
- 名前：`name`
- タイプ：`VARCHAR`
- 長さ/値：`200`
- Null：❌ チェックを外す

**price カラム**：
- 名前：`price`
- タイプ：`INT`
- Null：❌ チェックを外す

**stock カラム**：
- 名前：`stock`
- タイプ：`INT`
- デフォルト：`0`（「定義済み」を選択）
- Null：❌ チェックを外す

**created_at カラム**：
- 名前：`created_at`
- タイプ：`TIMESTAMP`
- デフォルト：`CURRENT_TIMESTAMP`
- Null：❌ チェックを外す

4. 「**保存**」ボタンをクリック

**SQL版（参考）**：

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price INT NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**解説**：

- **AUTO_INCREMENT**：idが自動で1, 2, 3...と増えていく
- **NOT NULL**：必須項目（空欄を許可しない）
- **DEFAULT 0**：stockのデフォルト値は0
- **CURRENT_TIMESTAMP**：現在時刻が自動で入る

---

### 演習 1-3: データの挿入（GUI） ✅

**データ1の挿入**：

1. `products` テーブルを選択
2. 「**挿入**」タブをクリック
3. フォームに値を入力：
   - id：空欄（自動で番号が振られる）
   - name：`ノートパソコン`
   - price：`80000`
   - stock：`5`
   - created_at：空欄（自動で現在時刻）
4. 「**実行**」ボタンをクリック

**データ2の挿入**：

同様に：
- name：`マウス`
- price：`1500`
- stock：`20`

**データ3の挿入**：

同様に：
- name：`キーボード`
- price：`3000`
- stock：`10`

**SQL版（参考）**：

```sql
-- 3件を一度に挿入
INSERT INTO products (name, price, stock) VALUES
('ノートパソコン', 80000, 5),
('マウス', 1500, 20),
('キーボード', 3000, 10);
```

**解説**：

- `id` と `created_at` は指定しなくても自動で入る
- SQLで一度に複数行を挿入する方が効率的

---

### 演習 1-4: データの検索（表示） ✅

**GUI操作**：

1. `products` テーブルを選択
2. 「**表示**」タブをクリック

**表示される内容**：

| id  | name           | price | stock | created_at          |
| --- | -------------- | ----- | ----- | ------------------- |
| 1   | ノートパソコン | 80000 | 5     | 2024-01-01 10:00:00 |
| 2   | マウス         | 1500  | 20    | 2024-01-01 10:01:00 |
| 3   | キーボード     | 3000  | 10    | 2024-01-01 10:02:00 |

**確認ポイント**：

- ✅ `id` が自動で 1, 2, 3 と採番されている
- ✅ `created_at` に現在時刻が自動で入っている
- ✅ 3件全てのデータが正しく表示されている

**SQL版（参考）**：

```sql
SELECT * FROM products;
```

---

### 演習 1-5: データの編集（GUI） ✅

**GUI操作**：

1. 「**表示**」タブでデータを確認
2. マウス（id=2）の行の「**編集**」アイコン（鉛筆マーク）をクリック
3. `stock` の値を `20` から `15` に変更
4. 「**実行**」ボタンをクリック

**確認**：

「表示」タブで確認すると：

| id  | name   | price | stock | created_at          |
| --- | ------ | ----- | ----- | ------------------- |
| 2   | マウス | 1500  | 15    | 2024-01-01 10:01:00 |

stockが `15` に変更されている！

**SQL版（参考）**：

```sql
UPDATE products SET stock = 15 WHERE id = 2;
```

**重要なポイント**：

- `WHERE id = 2` で、id=2のレコードだけを更新
- WHERE句を忘れると、全てのレコードが更新されてしまう！

---

## 🚀 応用編

### 演習 2-1: SQLタブでデータ挿入 ✅

**SQLクエリ**：

```sql
INSERT INTO products (name, price, stock) VALUES
('モニター', 25000, 8),
('Webカメラ', 5000, 12);
```

**手順**：

1. `practice_db` を選択
2. 「**SQL**」タブをクリック
3. 上記のSQLクエリを入力
4. 「**実行**」ボタンをクリック

**成功メッセージ**：

```text
2 行を挿入しました。
```

**確認**：

「表示」タブで5件のデータが表示される：

| id  | name           | price | stock | created_at          |
| --- | -------------- | ----- | ----- | ------------------- |
| 1   | ノートパソコン | 80000 | 5     | 2024-01-01 10:00:00 |
| 2   | マウス         | 1500  | 15    | 2024-01-01 10:01:00 |
| 3   | キーボード     | 3000  | 10    | 2024-01-01 10:02:00 |
| 4   | モニター       | 25000 | 8     | 2024-01-01 10:05:00 |
| 5   | Webカメラ      | 5000  | 12    | 2024-01-01 10:05:00 |

**解説**：

- `VALUES` の後にカンマで複数行を指定
- 一度に複数行を挿入できる（効率的！）

---

### 演習 2-2: SQLタブでデータ検索 ✅

**条件1：価格が10000円以上の商品を検索**

**SQLクエリ**：

```sql
SELECT * FROM products WHERE price >= 10000;
```

**実行結果**：

| id  | name           | price | stock | created_at          |
| --- | -------------- | ----- | ----- | ------------------- |
| 1   | ノートパソコン | 80000 | 5     | 2024-01-01 10:00:00 |
| 4   | モニター       | 25000 | 8     | 2024-01-01 10:05:00 |

**条件2：在庫が10個以下の商品を検索**

**SQLクエリ**：

```sql
SELECT * FROM products WHERE stock <= 10;
```

**実行結果**：

| id  | name           | price | stock | created_at          |
| --- | -------------- | ----- | ----- | ------------------- |
| 1   | ノートパソコン | 80000 | 5     | 2024-01-01 10:00:00 |
| 3   | キーボード     | 3000  | 10    | 2024-01-01 10:02:00 |
| 4   | モニター       | 25000 | 8     | 2024-01-01 10:05:00 |

**解説**：

- `WHERE` 句で条件を指定
- `>=`：以上、`<=`：以下、`=`：等しい、`>`：より大きい、`<`：より小さい

---

### 演習 2-3: SQLタブでデータ更新 ✅

**SQLクエリ**：

```sql
UPDATE products SET stock = 3 WHERE name = 'ノートパソコン';
```

**手順**：

1. 「**SQL**」タブをクリック
2. 上記のSQLクエリを入力
3. 「**実行**」ボタンをクリック

**成功メッセージ**：

```text
1 行を更新しました。
```

**確認**：

「表示」タブで確認すると：

| id  | name           | price | stock | created_at          |
| --- | -------------- | ----- | ----- | ------------------- |
| 1   | ノートパソコン | 80000 | 3     | 2024-01-01 10:00:00 |

stockが `3` に更新されている！

**⚠️ 超重要なポイント**：

```sql
-- ❌ 危険：WHERE句がない
UPDATE products SET stock = 3;
-- → 全ての商品のstockが3になる！

-- ✅ 安全：WHERE句で条件指定
UPDATE products SET stock = 3 WHERE name = 'ノートパソコン';
-- → ノートパソコンのstockだけが3になる
```

**WHERE句は絶対に忘れないで！**

---

## 🔒 実践編：セキュリティ意識を高める

### 演習 3-1: WHERE句の重要性を体験 ✅

**ステップ1：バックアップを取る**

1. `products` テーブルを選択
2. 「**エクスポート**」タブをクリック
3. エクスポート方法：「簡易」を選択
4. フォーマット：「SQL」を選択
5. 「**実行**」ボタンをクリック
6. `products.sql` ファイルがダウンロードされる

**ステップ2：危険なUPDATE文を実行**

**SQLクエリ**：

```sql
-- ⚠️ 危険：WHERE句がないので全てのレコードが更新される
UPDATE products SET stock = 0;
```

**実行後の状態**：

| id  | name           | price | stock | created_at          |
| --- | -------------- | ----- | ----- | ------------------- |
| 1   | ノートパソコン | 80000 | 0     | 2024-01-01 10:00:00 |
| 2   | マウス         | 1500  | 0     | 2024-01-01 10:01:00 |
| 3   | キーボード     | 3000  | 0     | 2024-01-01 10:02:00 |
| 4   | モニター       | 25000 | 0     | 2024-01-01 10:05:00 |
| 5   | Webカメラ      | 5000  | 0     | 2024-01-01 10:05:00 |

**全ての商品の在庫が0になってしまった！😱**

**ステップ3：バックアップから復元**

1. 「**インポート**」タブをクリック
2. 「**ファイルを選択**」ボタンをクリック
3. ダウンロードした `products.sql` を選択
4. 「**実行**」ボタンをクリック

**成功メッセージ**：

```text
インポートが正常に終了しました。
```

**確認**：

「表示」タブで確認すると、元のデータに戻っている！

**学んだこと**：

- ✅ WHERE句がないUPDATE/DELETE文は超危険！
- ✅ 本番環境でこれをやると、全てのデータが壊れる
- ✅ バックアップがあれば復元できる
- ✅ 必ずバックアップを取る習慣をつけよう

---

### 演習 3-2: データのエクスポート ✅

**手順**：

1. phpMyAdminで `practice_db` を選択
2. 「**エクスポート**」タブをクリック
3. エクスポート方法：「簡易」を選択
4. フォーマット：「SQL」を選択
5. 「**実行**」ボタンをクリック

**ダウンロードされるファイル**：

`practice_db.sql`

**ファイルの中身（一部）**：

```sql
-- データベースの作成
CREATE DATABASE IF NOT EXISTS `practice_db`;

-- テーブルの作成
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `price` int(11) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- データの挿入
INSERT INTO `products` (`id`, `name`, `price`, `stock`, `created_at`) VALUES
(1, 'ノートパソコン', 80000, 3, '2024-01-01 10:00:00'),
(2, 'マウス', 1500, 15, '2024-01-01 10:01:00'),
(3, 'キーボード', 3000, 10, '2024-01-01 10:02:00'),
(4, 'モニター', 25000, 8, '2024-01-01 10:05:00'),
(5, 'Webカメラ', 5000, 12, '2024-01-01 10:05:00');
```

**解説**：

- データベースの構造とデータの両方が含まれている
- このファイルがあれば、いつでも復元できる

---

### 演習 3-3: データのインポート（新しいデータベースに復元） ✅

**手順**：

**ステップ1：新しいデータベースを作成**

1. 「**新規作成**」をクリック
2. データベース名に `practice_db_backup` を入力
3. 照合順序は `utf8mb4_general_ci` を選択
4. 「**作成**」ボタンをクリック

**ステップ2：インポート**

1. `practice_db_backup` を選択
2. 「**インポート**」タブをクリック
3. 「**ファイルを選択**」ボタンをクリック
4. ダウンロードした `practice_db.sql` を選択
5. 「**実行**」ボタンをクリック

**成功メッセージ**：

```text
インポートが正常に終了しました。5 個のクエリを実行しました。
```

**確認**：

`practice_db_backup` 内に `products` テーブルとデータが復元されていることを確認

**解説**：

- エクスポートとインポートで、データベースを簡単にコピーできる
- 本番環境からローカルにデータを持ってくる時に便利
- バックアップと復元の基本操作

---

## 💡 チャレンジ問題

### チャレンジ 1: 複数テーブルの作成 ✅

**categoriesテーブルの作成**

**SQL**：

```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**カテゴリデータの挿入**：

```sql
INSERT INTO categories (name) VALUES
('PC周辺機器'),
('家電'),
('文房具');
```

**productsテーブルにcategory_idカラムを追加**：

```sql
ALTER TABLE products ADD COLUMN category_id INT;
```

**productsテーブルの各商品にcategory_idを設定**：

```sql
-- ノートパソコン、マウス、キーボード、モニター、Webカメラを「PC周辺機器」に設定
UPDATE products SET category_id = 1 WHERE id IN (1, 2, 3, 4, 5);
```

**確認**：

```sql
SELECT p.name AS 商品名, p.price AS 価格, c.name AS カテゴリ
FROM products p
LEFT JOIN categories c ON p.category_id = c.id;
```

**実行結果**：

| 商品名         | 価格  | カテゴリ       |
| -------------- | ----- | -------------- |
| ノートパソコン | 80000 | PC周辺機器     |
| マウス         | 1500  | PC周辺機器     |
| キーボード     | 3000  | PC周辺機器     |
| モニター       | 25000 | PC周辺機器     |
| Webカメラ      | 5000  | PC周辺機器     |

**解説**：

- `ALTER TABLE` でテーブルにカラムを追加
- `LEFT JOIN` で複数のテーブルを結合して表示（Phase 4で詳しく学ぶ）

---

### チャレンジ 2: AIへのSQL生成指示 ✅

**AIへの指示例**：

```text
「MySQLで、以下の構造のproductsテーブルを作成するCREATE TABLE文を書いてください：
- id：INT型、主キー、AUTO_INCREMENT
- name：VARCHAR(200)型、NOT NULL
- price：INT型、NOT NULL
- stock：INT型、NOT NULL、デフォルト値は0
- created_at：TIMESTAMP型、デフォルトはCURRENT_TIMESTAMP

テーブル名はproductsで、文字コードはutf8mb4を使用してください。」
```

**AIが生成するSQL**：

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price INT NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**確認方法**：

1. 新しいデータベース `test_db` を作成
2. 「SQL」タブで上記のSQLを実行
3. 「構造」タブで、テーブル構造が正しいか確認

**解説**：

- phpMyAdminで作ったテーブルと同じ構造のSQLを、AIに生成してもらえた！
- AIへの指示は具体的で明確にすることが重要
- phpMyAdminで学んだ知識が、AIへの指示に活かせる

---

## 🎓 まとめ

演習を通じて、以下のスキルが身についたね！

### phpMyAdminのGUI操作

- ✅ データベースの作成・削除
- ✅ テーブルの作成・編集
- ✅ データの挿入・更新・削除

### SQLタブでのクエリ実行

- ✅ INSERT文：データの挿入
- ✅ SELECT文：データの検索
- ✅ UPDATE文：データの更新
- ✅ WHERE句の重要性

### バックアップと復元

- ✅ エクスポート：データベースをファイルに保存
- ✅ インポート：ファイルからデータベースを復元

### セキュリティ意識

- ✅ WHERE句がないUPDATE/DELETEの危険性
- ✅ バックアップの重要性

### バイブコーディング

- ✅ phpMyAdminで学んだことを、AIへの指示に活かす
- ✅ AIが生成したSQLを、phpMyAdminで検証する

---

## 🚀 次のステップ

次のLesson 03では、SQL基本操作（CRUD）を詳しく学んでいくよ！

- CREATE（テーブル作成）
- INSERT（データ挿入）
- SELECT（データ取得）
- UPDATE（データ更新）
- DELETE（データ削除）

phpMyAdminで体験したことを、SQLで深く理解していこう！

👉 **[Lesson 03: SQL基本操作（CRUD）へ進む](../../03-sql-crud/README.md)**

---

**Let's vibe and code! 🎉**
