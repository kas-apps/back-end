-- ============================================
-- Phase 3 Lesson 03: SQL基本操作（CRUD）
-- 基本的なクエリ集（サンプル）
-- ============================================

-- データベースを選択
USE sql_basics_db;

-- ============================================
-- SELECT（データ取得）
-- ============================================

-- 1. 全件取得
SELECT * FROM books;

-- 2. 特定カラムだけ取得
SELECT title, price FROM books;

-- 3. WHERE句で条件指定（価格が3000円以上）
SELECT * FROM books WHERE price >= 3000;

-- 4. WHERE句で条件指定（在庫が10個以下）
SELECT * FROM books WHERE stock <= 10;

-- 5. LIKE（部分一致検索）- タイトルに「PHP」が含まれる
SELECT * FROM books WHERE title LIKE '%PHP%';

-- 6. 複数条件（AND）- 価格が2500円以上、かつ在庫が15個以上
SELECT * FROM books WHERE price >= 2500 AND stock >= 15;

-- 7. 複数条件（OR）- 価格が3000円以下、または在庫が10個以下
SELECT * FROM books WHERE price <= 3000 OR stock <= 10;

-- 8. BETWEEN（範囲指定）- 価格が2500円以上3000円以下
SELECT * FROM books WHERE price BETWEEN 2500 AND 3000;

-- 9. IN（リスト指定）- 価格が2500円、3000円、3500円のいずれか
SELECT * FROM books WHERE price IN (2500, 3000, 3500);

-- 10. ORDER BY（昇順）- 価格の安い順
SELECT * FROM books ORDER BY price ASC;

-- 11. ORDER BY（降順）- 価格の高い順
SELECT * FROM books ORDER BY price DESC;

-- 12. LIMIT（件数制限）- 最初の3件
SELECT * FROM books LIMIT 3;

-- 13. LIMIT + OFFSET - 2件目から3件取得
SELECT * FROM books LIMIT 3 OFFSET 1;

-- 14. 組み合わせ - 価格が2500円以上、価格の安い順で上位3件
SELECT * FROM books WHERE price >= 2500 ORDER BY price ASC LIMIT 3;

-- ============================================
-- INSERT（データ挿入）
-- ============================================

-- 15. 1件挿入
INSERT INTO books (title, author, price, stock) VALUES
('新しい本', '新しい著者', 2000, 5);

-- 16. 複数行挿入
INSERT INTO books (title, author, price, stock) VALUES
('本1', '著者1', 2100, 10),
('本2', '著者2', 2200, 12),
('本3', '著者3', 2300, 8);

-- ============================================
-- UPDATE（データ更新）
-- ============================================

-- 17. 1件更新（id=1の価格を3200に）
UPDATE books SET price = 3200 WHERE id = 1;

-- 18. 複数カラム更新（id=2の価格と在庫を更新）
UPDATE books SET price = 2900, stock = 15 WHERE id = 2;

-- 19. 条件に合う複数レコードを更新（価格が3000円以上の本の在庫を+5）
UPDATE books SET stock = stock + 5 WHERE price >= 3000;

-- ⚠️ 危険な例（コメントアウト）- WHERE句がないと全レコードが更新される
-- UPDATE books SET stock = 0;

-- ============================================
-- DELETE（データ削除）
-- ============================================

-- 20. 1件削除（id=10のレコードを削除）
-- DELETE FROM books WHERE id = 10;

-- 21. 条件に合う複数レコードを削除（在庫が0の本を削除）
-- DELETE FROM books WHERE stock = 0;

-- ⚠️ 危険な例（コメントアウト）- WHERE句がないと全レコードが削除される
-- DELETE FROM books;

-- ============================================
-- 実用的なクエリの例
-- ============================================

-- 22. 在庫が少ない本（10個以下）を価格の安い順で表示
SELECT * FROM books WHERE stock <= 10 ORDER BY price ASC;

-- 23. 高額な本（3000円以上）を在庫の多い順で表示
SELECT * FROM books WHERE price >= 3000 ORDER BY stock DESC;

-- 24. タイトルに「入門」または「基礎」が含まれる本を表示
SELECT * FROM books WHERE title LIKE '%入門%' OR title LIKE '%基礎%';

-- 25. 著者ごとの本の数をカウント（GROUP BY - Phase 4で学ぶ）
SELECT author, COUNT(*) AS book_count FROM books GROUP BY author;

-- 26. 在庫の合計を計算（SUM - Phase 4で学ぶ）
SELECT SUM(stock) AS total_stock FROM books;

-- 27. 平均価格を計算（AVG - Phase 4で学ぶ）
SELECT AVG(price) AS average_price FROM books;

-- 28. 最高価格と最低価格を取得（MAX、MIN - Phase 4で学ぶ）
SELECT MAX(price) AS max_price, MIN(price) AS min_price FROM books;

-- ============================================
-- 終了
-- ============================================

SELECT 'クエリ集の実行が完了しました！' AS message;
