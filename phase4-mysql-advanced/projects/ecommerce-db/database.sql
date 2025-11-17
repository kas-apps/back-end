-- ========================================
-- ECサイトのデータベース設計
-- Phase 4: MySQL発展編 - 総合プロジェクト
-- ========================================

-- データベース作成
CREATE DATABASE IF NOT EXISTS ecommerce_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce_db;

-- テーブル削除（既存データをリセット）
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS product_tags;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- ========================================
-- 1. usersテーブル（ユーザー情報）
-- ========================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,  -- パスワードはハッシュ化
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)  -- emailにインデックス
);

INSERT INTO users (name, email, password_hash) VALUES
('太郎', 'taro@example.com', '$2y$10$...'),  -- 実際はハッシュ化されたパスワード
('花子', 'hanako@example.com', '$2y$10$...'),
('次郎', 'jiro@example.com', '$2y$10$...');

-- ========================================
-- 2. categoriesテーブル（商品カテゴリ）
-- ========================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO categories (name) VALUES
('家電'),
('書籍'),
('食品'),
('ファッション');

-- ========================================
-- 3. productsテーブル（商品情報）
-- ========================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    INDEX idx_category_id (category_id),  -- カテゴリIDにインデックス
    INDEX idx_price (price)  -- 価格にインデックス
);

INSERT INTO products (category_id, name, description, price, stock) VALUES
(1, 'ノートパソコン', '高性能ノートPC', 80000.00, 5),
(1, 'スマートフォン', '最新スマホ', 60000.00, 10),
(1, 'タブレット', '10インチタブレット', 40000.00, 7),
(2, 'PHP入門書', 'PHP学習の決定版', 3000.00, 20),
(2, 'MySQL入門書', 'MySQL学習の決定版', 3500.00, 15),
(3, 'コーヒー豆', '高級コーヒー豆', 1500.00, 50),
(3, '紅茶', 'イングリッシュティー', 1200.00, 30),
(4, 'Tシャツ', 'コットンTシャツ', 2500.00, 100);

-- ========================================
-- 4. tagsテーブル（商品タグ）
-- ========================================
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO tags (name) VALUES
('新商品'),
('人気'),
('セール中'),
('おすすめ');

-- ========================================
-- 5. product_tagsテーブル（商品とタグの多対多）
-- ========================================
CREATE TABLE product_tags (
    product_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (product_id, tag_id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (tag_id) REFERENCES tags(id)
);

INSERT INTO product_tags (product_id, tag_id) VALUES
(1, 1),  -- ノートパソコン: 新商品
(1, 2),  -- ノートパソコン: 人気
(2, 2),  -- スマートフォン: 人気
(4, 3),  -- PHP入門書: セール中
(4, 4),  -- PHP入門書: おすすめ
(5, 4);  -- MySQL入門書: おすすめ

-- ========================================
-- 6. ordersテーブル（注文情報）
-- ========================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
);

INSERT INTO orders (user_id, total_amount, status) VALUES
(1, 140000.00, 'completed'),  -- 太郎の注文
(2, 3000.00, 'completed'),    -- 花子の注文
(3, 1500.00, 'pending');      -- 次郎の注文

-- ========================================
-- 7. order_itemsテーブル（注文詳細）
-- ========================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,  -- 注文時の価格を保存
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
);

INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 80000.00),  -- 注文1: ノートパソコン × 1
(1, 2, 1, 60000.00),  -- 注文1: スマートフォン × 1
(2, 4, 1, 3000.00),   -- 注文2: PHP入門書 × 1
(3, 6, 1, 1500.00);   -- 注文3: コーヒー豆 × 1

-- ========================================
-- 8. reviewsテーブル（商品レビュー）
-- ========================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_product_id (product_id)
);

INSERT INTO reviews (product_id, user_id, rating, comment) VALUES
(1, 1, 5, '素晴らしい商品です！'),
(1, 2, 4, '良い商品ですが、少し高いです'),
(2, 1, 5, '最高のスマホ！'),
(4, 2, 5, 'とても分かりやすい本です');

-- ========================================
-- 実践クエリ例
-- ========================================

-- 1. 商品一覧（カテゴリ名付き）
SELECT
    p.name AS product_name,
    p.price,
    p.stock,
    c.name AS category_name
FROM
    products AS p
INNER JOIN
    categories AS c ON p.category_id = c.id;

-- 2. カテゴリ別の商品数と平均価格
SELECT
    c.name AS category_name,
    COUNT(p.id) AS product_count,
    AVG(p.price) AS avg_price
FROM
    categories AS c
LEFT JOIN
    products AS p ON c.id = p.category_id
GROUP BY
    c.id, c.name;

-- 3. 商品とタグを取得
SELECT
    p.name AS product_name,
    GROUP_CONCAT(t.name) AS tags
FROM
    products AS p
LEFT JOIN
    product_tags AS pt ON p.id = pt.product_id
LEFT JOIN
    tags AS t ON pt.tag_id = t.id
GROUP BY
    p.id, p.name;

-- 4. カテゴリ別の売上集計
SELECT
    c.name AS category_name,
    COALESCE(SUM(oi.quantity * oi.price), 0) AS total_sales
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

-- 5. ユーザー別の購入履歴
SELECT
    u.name AS user_name,
    o.id AS order_id,
    o.total_amount,
    o.status,
    o.created_at
FROM
    users AS u
LEFT JOIN
    orders AS o ON u.id = o.user_id
ORDER BY
    o.created_at DESC;

-- 6. 商品の平均評価
SELECT
    p.name AS product_name,
    AVG(r.rating) AS avg_rating,
    COUNT(r.id) AS review_count
FROM
    products AS p
LEFT JOIN
    reviews AS r ON p.id = r.product_id
GROUP BY
    p.id, p.name
HAVING
    review_count > 0
ORDER BY
    avg_rating DESC;

-- 7. 人気商品ランキング（注文数が多い順）
SELECT
    p.name AS product_name,
    SUM(oi.quantity) AS total_sold
FROM
    products AS p
INNER JOIN
    order_items AS oi ON p.id = oi.product_id
GROUP BY
    p.id, p.name
ORDER BY
    total_sold DESC
LIMIT 5;

-- ========================================
-- 終わり
-- ========================================
