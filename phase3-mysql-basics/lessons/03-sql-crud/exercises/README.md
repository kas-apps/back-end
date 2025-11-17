# Lesson 03: SQL基本操作（CRUD） - 演習問題 💪

SQL基本操作（CRUD）の演習問題だよ！実際にSQLを書いて、CRUD操作をマスターしよう！

---

## 📝 準備：データベースとテーブルの作成

演習を始める前に、以下のSQLファイルを実行して、データベースとテーブルを準備しよう！

### ステップ1：データベースとテーブルの作成

[resources/create-database.sql](../resources/create-database.sql) を開いて、phpMyAdminのSQLタブで実行してください。

### ステップ2：サンプルデータの挿入

[resources/insert-data.sql](../resources/insert-data.sql) を開いて、phpMyAdminのSQLタブで実行してください。

これで準備完了！演習を始めよう！

---

## 🎯 基礎編：SELECT文

### 演習 1-1: 全件取得

**課題**：

`books` テーブルの全データを取得するSELECT文を書いてください。

**ヒント**：

```sql
SELECT * FROM テーブル名;
```

---

### 演習 1-2: 特定カラムの取得

**課題**：

`books` テーブルから、`title`（タイトル）と `price`（価格）だけを取得するSELECT文を書いてください。

**ヒント**：

```sql
SELECT カラム1, カラム2 FROM テーブル名;
```

---

### 演習 1-3: WHERE句で条件指定

**課題**：

`books` テーブルから、価格が2000円以上の本を取得するSELECT文を書いてください。

**ヒント**：

```sql
SELECT * FROM テーブル名 WHERE カラム名 >= 値;
```

---

### 演習 1-4: LIKE（部分一致検索）

**課題**：

`books` テーブルから、タイトルに「PHP」が含まれる本を取得するSELECT文を書いてください。

**ヒント**：

```sql
SELECT * FROM テーブル名 WHERE カラム名 LIKE '%キーワード%';
```

---

### 演習 1-5: ORDER BY（並び替え）

**課題**：

`books` テーブルから、全ての本を価格の安い順（昇順）で取得するSELECT文を書いてください。

**ヒント**：

```sql
SELECT * FROM テーブル名 ORDER BY カラム名 ASC;
```

---

### 演習 1-6: LIMIT（件数制限）

**課題**：

`books` テーブルから、価格の高い順（降順）で上位3件を取得するSELECT文を書いてください。

**ヒント**：

```sql
SELECT * FROM テーブル名 ORDER BY カラム名 DESC LIMIT 件数;
```

---

## 🚀 応用編：INSERT、UPDATE、DELETE

### 演習 2-1: データの挿入（INSERT）

**課題**：

`books` テーブルに、以下の本を挿入するINSERT文を書いてください。

- title: `MySQL入門`
- author: `鈴木太郎`
- price: `2800`
- stock: `8`

**ヒント**：

```sql
INSERT INTO テーブル名 (カラム1, カラム2, ...) VALUES (値1, 値2, ...);
```

---

### 演習 2-2: 複数行の挿入

**課題**：

`books` テーブルに、以下の2冊の本を一度に挿入するINSERT文を書いてください。

1. title: `JavaScript基礎`, author: `山田花子`, price: `2500`, stock: `12`
2. title: `HTML&CSS入門`, author: `田中次郎`, price: `2200`, stock: `15`

**ヒント**：

```sql
INSERT INTO テーブル名 (カラム1, カラム2, ...) VALUES
(値1, 値2, ...),
(値1, 値2, ...);
```

---

### 演習 2-3: データの更新（UPDATE）

**課題**：

`books` テーブルで、`id=1` の本の価格を `3200` に更新するUPDATE文を書いてください。

**ヒント**：

```sql
UPDATE テーブル名 SET カラム名 = 値 WHERE 条件;
```

**⚠️ 重要**：WHERE句を忘れないで！

---

### 演習 2-4: 複数カラムの更新

**課題**：

`books` テーブルで、`id=2` の本の在庫（stock）を `8` に、価格（price）を `2600` に更新するUPDATE文を書いてください。

**ヒント**：

```sql
UPDATE テーブル名 SET カラム1 = 値1, カラム2 = 値2 WHERE 条件;
```

---

### 演習 2-5: データの削除（DELETE）

**課題**：

`books` テーブルで、`id=3` の本を削除するDELETE文を書いてください。

**ヒント**：

```sql
DELETE FROM テーブル名 WHERE 条件;
```

**⚠️ 重要**：WHERE句を忘れないで！

---

## 🔒 セキュリティチャレンジ：WHERE句の重要性

### 演習 3-1: WHERE句なしのUPDATEの危険性を体験

**課題**：

以下の手順で、WHERE句の重要性を体験してください。

**ステップ1：バックアップを取る**

```sql
-- booksテーブルのデータを確認
SELECT * FROM books;
```

結果をメモまたはスクリーンショットで保存してください。

**ステップ2：危険なUPDATE文を実行**

```sql
-- ⚠️ 危険：WHERE句がないので全てのレコードが更新される
UPDATE books SET stock = 0;
```

**ステップ3：結果を確認**

```sql
SELECT * FROM books;
```

全ての本の在庫が `0` になっているはず！

**ステップ4：元に戻す**

```sql
-- 元のデータを再挿入（resources/insert-data.sql を再実行）
```

**学んだこと**：

- WHERE句がないUPDATE文は超危険！
- 本番環境では絶対にやらない
- 必ずWHERE句を付ける習慣をつける

---

### 演習 3-2: 実行前にSELECTで確認

**課題**：

以下の手順で、安全なUPDATE/DELETEの習慣を身につけてください。

**タスク**：価格が3000円以上の本の在庫を10に更新する

**ステップ1：まずSELECTで対象を確認**

```sql
SELECT * FROM books WHERE price >= 3000;
```

対象の本が何件あるか確認してください。

**ステップ2：WHERE句を同じにしてUPDATE**

```sql
UPDATE books SET stock = 10 WHERE price >= 3000;
```

**ステップ3：結果を確認**

```sql
SELECT * FROM books WHERE price >= 3000;
```

在庫が `10` に更新されていることを確認してください。

---

## 💡 チャレンジ問題

### チャレンジ 1: 複雑な検索

**課題**：

`books` テーブルから、以下の条件に合う本を取得するSELECT文を書いてください。

- 価格が2000円以上3000円以下
- 在庫が10個以上
- 価格の安い順で並び替え

**ヒント**：

```sql
SELECT * FROM books
WHERE price BETWEEN 2000 AND 3000
AND stock >= 10
ORDER BY price ASC;
```

---

### チャレンジ 2: AIにSQL生成を依頼

**課題**：

AIに以下の指示を出して、SQL文を生成してもらってください。

**指示例**：

```text
「MySQLのbooksテーブルから、
- タイトルに『入門』が含まれる本
- 価格が3000円以下
の条件で検索するSELECT文を書いてください。
結果は価格の安い順で並び替えてください。」
```

**生成されたSQLを実行**：

phpMyAdminのSQLタブで実行して、正しく動作することを確認してください。

---

### チャレンジ 3: テーブルの作成

**課題**：

以下の構造の `authors`（著者）テーブルを作成するCREATE TABLE文を書いてください。

**テーブル構造**：

| カラム名   | データ型    | 制約                         |
| ---------- | ----------- | ---------------------------- |
| id         | INT         | 主キー、AUTO_INCREMENT       |
| name       | VARCHAR(100)| NOT NULL                     |
| email      | VARCHAR(255)| NOT NULL、UNIQUE             |
| created_at | TIMESTAMP   | DEFAULT CURRENT_TIMESTAMP    |

**ヒント**：

```sql
CREATE TABLE テーブル名 (
    カラム名 データ型 制約,
    ...
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 🎓 演習を終えて

お疲れさま！これらの演習を通じて：

- ✅ SELECT文で様々な条件でデータを取得できるようになった
- ✅ INSERT文でデータを挿入できるようになった
- ✅ UPDATE文でデータを更新できるようになった
- ✅ DELETE文でデータを削除できるようになった
- ✅ WHERE句の重要性を理解した
- ✅ 実行前にSELECTで確認する習慣が身についた

次のステップでは、データ型とテーブル設計を学んでいくよ！

👉 **[解答例を見る](solutions/README.md)**

---

**Let's vibe and code! 🎉**
