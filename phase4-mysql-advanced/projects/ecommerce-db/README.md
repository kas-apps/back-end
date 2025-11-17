# 総合プロジェクト1: ECサイトのデータベース設計 🛒

**プロジェクト概要**：本格的なECサイトのデータベースを設計・実装し、Phase 4で学んだすべての技術を実践する！

---

## 🎯 プロジェクトの目標

Phase 4で学んだ技術を全部使って、実践的なECサイトのデータベースを構築しよう！

- JOIN（複数テーブルからデータ取得）
- 集計関数（売上レポート作成）
- インデックス（パフォーマンス最適化）
- トランザクション（注文処理）
- 多対多の関係（商品とタグ）

---

## 📊 データベース設計

### テーブル一覧

1. **users**：ユーザー情報
2. **categories**：商品カテゴリ
3. **products**：商品情報
4. **tags**：商品タグ
5. **product_tags**：商品とタグの多対多（中間テーブル）
6. **orders**：注文情報
7. **order_items**：注文詳細
8. **reviews**：商品レビュー

---

## 💻 実装内容

### 1. テーブル作成

`database.sql`ファイルに、すべてのテーブル定義とサンプルデータを含めています。

### 2. 実践クエリ例

**商品一覧を取得（カテゴリ名付き）**：

```sql
SELECT
    p.name AS product_name,
    p.price,
    c.name AS category_name
FROM
    products AS p
INNER JOIN
    categories AS c ON p.category_id = c.id;
```

**カテゴリ別の売上集計**：

```sql
SELECT
    c.name AS category_name,
    SUM(oi.quantity * oi.price) AS total_sales
FROM
    categories AS c
LEFT JOIN
    products AS p ON c.id = p.category_id
LEFT JOIN
    order_items AS oi ON p.id = oi.product_id
GROUP BY
    c.id, c.name
ORDER BY
    total_sales DESC;
```

**注文処理（トランザクション）**：

```sql
START TRANSACTION;

-- 在庫を減らす
UPDATE products SET stock = stock - 1 WHERE id = 1;

-- 注文を追加
INSERT INTO orders (user_id, total_amount) VALUES (1, 80000.00);

-- 注文詳細を追加
INSERT INTO order_items (order_id, product_id, quantity, price)
VALUES (LAST_INSERT_ID(), 1, 1, 80000.00);

COMMIT;
```

---

## 🤖 AIと一緒に実践しよう

### AIへの指示例

```text
「ECサイトの商品一覧ページを想定して、以下を取得するSQLを書いてください：
- 商品名
- 商品価格
- カテゴリ名
- 平均評価（reviewsテーブルのratingの平均）
商品ごとにグループ化し、平均評価が高い順に並び替えてください。」
```

---

## ✅ 学べること

- ✅ 複雑なテーブル設計（1対多、多対多）
- ✅ JOINを使った複雑なクエリ
- ✅ 集計関数で売上レポート
- ✅ トランザクションで注文処理
- ✅ インデックスでパフォーマンス最適化

---

**Let's vibe and code! 🎉**

実践的なECサイトデータベースを構築しよう！
