-- ================================================================================
-- Product Management Database
-- ================================================================================
-- Phase 3: MySQL基礎編 - 総合プロジェクト2
--
-- このSQLファイルは、商品管理システムのデータベースを作成します。
-- phpMyAdminのSQLタブで実行してください。
-- ================================================================================

-- ================================================================================
-- データベースの作成
-- ================================================================================

-- 既存のデータベースを削除（注意：既存データが削除されます）
DROP DATABASE IF EXISTS product_db;

-- データベースを作成（文字コード：utf8mb4）
CREATE DATABASE product_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 作成したデータベースを使用
USE product_db;

-- ================================================================================
-- テーブルの作成
-- ================================================================================

-- カテゴリテーブル
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'カテゴリID',
    name VARCHAR(100) UNIQUE NOT NULL COMMENT 'カテゴリ名（一意）',
    description TEXT COMMENT 'カテゴリの説明'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品カテゴリ';

-- 仕入先テーブル
CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '仕入先ID',
    name VARCHAR(200) UNIQUE NOT NULL COMMENT '仕入先名（一意）',
    email VARCHAR(255) COMMENT '連絡先メールアドレス',
    phone VARCHAR(20) COMMENT '連絡先電話番号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='仕入先情報';

-- 商品テーブル
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '商品ID',
    category_id INT NOT NULL COMMENT 'カテゴリID（外部キー）',
    supplier_id INT NOT NULL COMMENT '仕入先ID（外部キー）',
    name VARCHAR(200) NOT NULL COMMENT '商品名',
    sku VARCHAR(50) UNIQUE NOT NULL COMMENT '商品コード（SKU）',
    price DECIMAL(10, 2) NOT NULL COMMENT '価格（小数点以下2桁）',
    stock INT DEFAULT 0 COMMENT '在庫数',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '登録日時',

    -- 外部キー制約
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE RESTRICT,

    -- 価格と在庫数のチェック制約（MySQL 8.0.16以降）
    CHECK (price >= 0),
    CHECK (stock >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品情報';

-- ================================================================================
-- サンプルデータの挿入
-- ================================================================================

-- カテゴリデータの挿入
INSERT INTO categories (name, description) VALUES
('家電', '家電製品全般'),
('食品', '食品・飲料'),
('書籍', '書籍・雑誌'),
('文房具', '文房具・オフィス用品'),
('スポーツ', 'スポーツ・アウトドア用品');

-- 仕入先データの挿入
INSERT INTO suppliers (name, email, phone) VALUES
('テクノサプライ', 'info@techno-supply.com', '03-1234-5678'),
('フードマート', 'contact@foodmart.com', '03-2345-6789'),
('ユニバーサル商事', 'sales@universal-trade.com', '03-3456-7890');

-- 商品データの挿入
INSERT INTO products (category_id, supplier_id, name, sku, price, stock) VALUES
-- 家電（カテゴリ1）、テクノサプライ（仕入先1）
(1, 1, 'ノートパソコン', 'LAPTOP001', 85000.00, 15),
(1, 1, 'スマートフォン', 'PHONE001', 65000.00, 20),
(1, 1, 'タブレット', 'TABLET001', 45000.00, 10),

-- 食品（カテゴリ2）、フードマート（仕入先2）
(2, 2, 'コーヒー豆（500g）', 'COFFEE001', 1500.00, 50),
(2, 2, '緑茶（100g）', 'TEA001', 800.00, 100),

-- 書籍（カテゴリ3）、ユニバーサル商事（仕入先3）
(3, 3, 'PHP入門書', 'BOOK001', 3000.00, 30),
(3, 3, 'MySQL完全ガイド', 'BOOK002', 3500.00, 25),

-- 文房具（カテゴリ4）、ユニバーサル商事（仕入先3）
(4, 3, 'ボールペン（12本セット）', 'PEN001', 600.00, 200),
(4, 3, 'ノート（5冊セット）', 'NOTE001', 800.00, 150),

-- スポーツ（カテゴリ5）、テクノサプライ（仕入先1）
(5, 1, 'ランニングシューズ', 'SHOES001', 12000.00, 40);

-- ================================================================================
-- データの確認
-- ================================================================================

-- 全カテゴリを表示
SELECT * FROM categories;

-- 全仕入先を表示
SELECT * FROM suppliers;

-- 全商品を表示
SELECT * FROM products;

-- ================================================================================
-- 実践的なクエリ例
-- ================================================================================

-- 例1: 特定のカテゴリ（家電）の商品を取得
SELECT * FROM products WHERE category_id = 1;

-- 例2: 特定の仕入先（テクノサプライ）の商品を取得
SELECT * FROM products WHERE supplier_id = 1;

-- 例3: 価格が10000円以上の商品を取得
SELECT * FROM products WHERE price >= 10000;

-- 例4: 在庫が50個以下の商品を取得（在庫不足の警告）
SELECT * FROM products WHERE stock <= 50;

-- 例5: 価格が高い順に商品を表示
SELECT * FROM products ORDER BY price DESC;

-- 例6: 在庫が少ない順に商品を表示（在庫管理に便利）
SELECT * FROM products ORDER BY stock ASC;

-- 例7: 最新の5件の商品を取得
SELECT * FROM products ORDER BY created_at DESC LIMIT 5;

-- 例8: 商品名に「パソコン」を含む商品を検索
SELECT * FROM products WHERE name LIKE '%パソコン%';

-- 例9: 在庫数の合計を計算
SELECT SUM(stock) AS total_stock FROM products;

-- 例10: 商品数をカウント
SELECT COUNT(*) AS total_products FROM products;

-- 例11: 平均価格を計算
SELECT AVG(price) AS average_price FROM products;

-- 例12: 最も高い商品と最も安い商品の価格を取得
SELECT MAX(price) AS max_price, MIN(price) AS min_price FROM products;

-- ================================================================================
-- ビジネスロジック的なクエリ
-- ================================================================================

-- 在庫が10個以下の商品を「在庫不足」として警告表示
SELECT
    id,
    name,
    stock,
    CASE
        WHEN stock <= 10 THEN '在庫不足'
        WHEN stock <= 50 THEN '在庫少'
        ELSE '在庫充分'
    END AS stock_status
FROM products
ORDER BY stock ASC;

-- 商品の合計金額（価格×在庫数）を計算
SELECT
    id,
    name,
    price,
    stock,
    price * stock AS total_value
FROM products
ORDER BY total_value DESC;

-- カテゴリ別の商品数をカウント（Phase 4で詳しく学ぶGROUP BY）
-- ヒント：これはLesson 03では難しいかも。Phase 4の内容だよ！
-- SELECT category_id, COUNT(*) AS product_count
-- FROM products
-- GROUP BY category_id;

-- ================================================================================
-- データの更新例
-- ================================================================================

-- 例1: 商品の価格を更新
-- UPDATE products SET price = 80000.00 WHERE sku = 'LAPTOP001';

-- 例2: 商品の在庫を更新
-- UPDATE products SET stock = 30 WHERE sku = 'COFFEE001';

-- 例3: 仕入先のメールアドレスを更新
-- UPDATE suppliers SET email = 'info@foodmart.com' WHERE name = 'フードマート';

-- ================================================================================
-- データの削除例
-- ================================================================================

-- 例1: 特定の商品を削除
-- DELETE FROM products WHERE sku = 'TABLET001';

-- 例2: 特定のカテゴリの全商品を削除
-- DELETE FROM products WHERE category_id = 5;

-- 注意: 外部キー制約（ON DELETE RESTRICT）があるため、
-- 商品が存在するカテゴリや仕入先は削除できません
-- 先に商品を削除してから、カテゴリや仕入先を削除する必要があります

-- ================================================================================
-- テーブル構造の確認
-- ================================================================================

-- categoriesテーブルの構造を確認
DESCRIBE categories;

-- suppliersテーブルの構造を確認
DESCRIBE suppliers;

-- productsテーブルの構造を確認
DESCRIBE products;

-- ================================================================================
-- 外部キー制約の確認
-- ================================================================================

-- productsテーブルの外部キー制約を確認
SHOW CREATE TABLE products;

-- ================================================================================
-- 注意事項とベストプラクティス
-- ================================================================================

-- 1. データ型の選択
--    - 価格: DECIMAL(10, 2) を使用（FLOATは誤差が出るため不適切）
--    - 在庫数: INT を使用（整数）
--    - 商品コード: VARCHAR で固定フォーマット
--
-- 2. 外部キー制約について
--    - ON DELETE RESTRICT: 親レコードが削除される際、子レコードが存在すると削除不可
--      → データの整合性を保つために重要
--
-- 3. CHECK制約（MySQL 8.0.16以降）
--    - 価格と在庫数は負の値にならないようにチェック
--
-- 4. 文字コード設定
--    - utf8mb4: 絵文字や特殊文字にも対応した文字コード
--    - utf8mb4_unicode_ci: 大文字小文字を区別しない照合順序
--
-- 5. データベースエンジン
--    - InnoDB: トランザクションと外部キー制約をサポート
--
-- 6. 命名規則
--    - テーブル名: 複数形、スネークケース（products、categories）
--    - カラム名: 単数形、スネークケース（category_id、supplier_id）

-- ================================================================================
-- 実践課題のヒント
-- ================================================================================

-- 課題2: データの挿入
-- ヒント: INSERT INTO テーブル名 (カラム1, カラム2, ...) VALUES (値1, 値2, ...);

-- 課題3: データの更新
-- ヒント: UPDATE テーブル名 SET カラム名 = 新しい値 WHERE 条件;
-- 注意: WHERE句を忘れると全レコードが更新されます！

-- 課題4: データの削除
-- ヒント: DELETE FROM テーブル名 WHERE 条件;
-- 注意: WHERE句を忘れると全レコードが削除されます！

-- 課題5: 高度なクエリ
-- ヒント:
-- - ORDER BY で並び替え: ORDER BY price DESC（降順）、ORDER BY stock ASC（昇順）
-- - LIMIT で件数制限: LIMIT 5
-- - 集計関数: COUNT(*), SUM(stock), AVG(price), MAX(price), MIN(price)

-- 課題6: ビジネスロジック
-- ヒント:
-- - 条件分岐: CASE WHEN ... THEN ... ELSE ... END
-- - 計算: price * stock
-- - WHERE句で条件指定: WHERE stock <= 10

-- ================================================================================
-- DECIMALとFLOATの違いを実験
-- ================================================================================

-- DECIMALは正確な小数を保存
-- FLOATは浮動小数点で誤差が出る可能性

-- 実験用テーブル（参考）
-- CREATE TABLE price_test (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     price_decimal DECIMAL(10, 2),
--     price_float FLOAT
-- );

-- INSERT INTO price_test (price_decimal, price_float) VALUES (100.10, 100.10);
-- SELECT * FROM price_test;
-- → DECIMALは100.10、FLOATは100.099998...となる可能性

-- 結論: 価格など正確な小数が必要な場合は、必ずDECIMALを使う！
