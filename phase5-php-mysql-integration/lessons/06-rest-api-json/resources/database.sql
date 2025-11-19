-- REST API練習用データベース

-- データベースを作成
CREATE DATABASE IF NOT EXISTS phase5_api_practice CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE phase5_api_practice;

-- 商品テーブル
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- サンプルデータを挿入
INSERT INTO products (name, description, price, stock) VALUES
('MacBook Pro', '高性能ノートパソコン。M3チップ搭載で驚異的なパフォーマンス。', 198000.00, 10),
('iPhone 15 Pro', '最新スマートフォン。チタニウムデザインとA17 Proチップ。', 159800.00, 25),
('iPad Air', 'タブレット端末。M2チップ搭載で軽量コンパクト。', 84800.00, 15),
('AirPods Pro', 'ワイヤレスイヤホン。アクティブノイズキャンセリング搭載。', 39800.00, 30),
('Apple Watch Ultra', 'スマートウォッチ。アウトドアやスポーツに最適。', 128800.00, 12);

-- 確認
SELECT * FROM products;
