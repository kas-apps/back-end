-- ========================================
-- 図書館管理システムのデータベース設計
-- Phase 4: MySQL発展編 - 総合プロジェクト
-- ========================================

-- データベース作成
CREATE DATABASE IF NOT EXISTS library_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE library_db;

-- テーブル削除（既存データをリセット）
DROP TABLE IF EXISTS lendings;
DROP TABLE IF EXISTS book_authors;
DROP TABLE IF EXISTS authors;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS members;

-- ========================================
-- 1. membersテーブル（会員情報）
-- ========================================
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20),
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

INSERT INTO members (name, email, phone) VALUES
('太郎', 'taro@example.com', '090-1234-5678'),
('花子', 'hanako@example.com', '090-2345-6789'),
('次郎', 'jiro@example.com', '090-3456-7890');

-- ========================================
-- 2. booksテーブル（本の情報）
-- ========================================
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(20) UNIQUE,
    title VARCHAR(200) NOT NULL,
    publisher VARCHAR(100),
    published_year INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_isbn (isbn),
    INDEX idx_title (title)
);

INSERT INTO books (isbn, title, publisher, published_year) VALUES
('978-4-12345-678-0', 'PHP入門', '技術出版', 2023),
('978-4-12345-679-7', 'MySQL完全ガイド', '技術出版', 2023),
('978-4-12345-680-3', 'Web開発の基礎', 'プログラミング社', 2024),
('978-4-12345-681-0', 'データベース設計', 'プログラミング社', 2024);

-- ========================================
-- 3. authorsテーブル（著者情報）
-- ========================================
CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    bio TEXT
);

INSERT INTO authors (name, bio) VALUES
('山田太郎', 'PHPエキスパート'),
('佐藤花子', 'データベースエンジニア'),
('田中次郎', 'フルスタックエンジニア');

-- ========================================
-- 4. book_authorsテーブル（本と著者の多対多）
-- ========================================
CREATE TABLE book_authors (
    book_id INT NOT NULL,
    author_id INT NOT NULL,
    PRIMARY KEY (book_id, author_id),
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (author_id) REFERENCES authors(id)
);

INSERT INTO book_authors (book_id, author_id) VALUES
(1, 1),  -- PHP入門: 山田太郎
(2, 2),  -- MySQL完全ガイド: 佐藤花子
(3, 1),  -- Web開発の基礎: 山田太郎
(3, 3),  -- Web開発の基礎: 田中次郎（共著）
(4, 2);  -- データベース設計: 佐藤花子

-- ========================================
-- 5. lendingsテーブル（貸し出し履歴）
-- ========================================
CREATE TABLE lendings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    member_id INT NOT NULL,
    lent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    returned_at TIMESTAMP NULL,
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (member_id) REFERENCES members(id),
    INDEX idx_book_id (book_id),
    INDEX idx_member_id (member_id),
    INDEX idx_returned_at (returned_at)
);

INSERT INTO lendings (book_id, member_id, lent_at, returned_at) VALUES
(1, 1, '2024-01-01 10:00:00', '2024-01-15 14:00:00'),  -- 返却済み
(2, 1, '2024-01-05 11:00:00', NULL),                    -- 貸し出し中
(3, 2, '2024-01-10 12:00:00', '2024-01-20 15:00:00'),  -- 返却済み
(4, 3, '2024-01-12 13:00:00', NULL);                    -- 貸し出し中

-- ========================================
-- 実践クエリ例
-- ========================================

-- 1. 本と著者を取得
SELECT
    b.title,
    GROUP_CONCAT(a.name SEPARATOR ', ') AS authors,
    b.publisher,
    b.published_year
FROM
    books AS b
LEFT JOIN
    book_authors AS ba ON b.id = ba.book_id
LEFT JOIN
    authors AS a ON ba.author_id = a.id
GROUP BY
    b.id, b.title, b.publisher, b.published_year;

-- 2. 貸し出し中の本を取得
SELECT
    b.title,
    m.name AS member_name,
    l.lent_at
FROM
    books AS b
INNER JOIN
    lendings AS l ON b.id = l.book_id
INNER JOIN
    members AS m ON l.member_id = m.id
WHERE
    l.returned_at IS NULL;

-- 3. 会員別の貸し出し履歴
SELECT
    m.name AS member_name,
    b.title AS book_title,
    l.lent_at,
    l.returned_at,
    CASE
        WHEN l.returned_at IS NULL THEN '貸し出し中'
        ELSE '返却済み'
    END AS status
FROM
    members AS m
LEFT JOIN
    lendings AS l ON m.id = l.member_id
LEFT JOIN
    books AS b ON l.book_id = b.id
ORDER BY
    l.lent_at DESC;

-- 4. 人気の本ランキング（貸し出し回数が多い順）
SELECT
    b.title,
    COUNT(l.id) AS lending_count
FROM
    books AS b
LEFT JOIN
    lendings AS l ON b.id = l.book_id
GROUP BY
    b.id, b.title
ORDER BY
    lending_count DESC;

-- 5. 著者別の著書数
SELECT
    a.name AS author_name,
    COUNT(ba.book_id) AS book_count
FROM
    authors AS a
LEFT JOIN
    book_authors AS ba ON a.id = ba.author_id
GROUP BY
    a.id, a.name
ORDER BY
    book_count DESC;

-- ========================================
-- トランザクション例：本の貸し出し処理
-- ========================================

-- START TRANSACTION;

-- -- 1. 本が貸し出し可能か確認（貸し出し中でないか）
-- SELECT id FROM lendings
-- WHERE book_id = 1 AND returned_at IS NULL;
-- -- 結果が0件なら貸し出し可能

-- -- 2. 貸し出しレコードを追加
-- INSERT INTO lendings (book_id, member_id, lent_at)
-- VALUES (1, 1, NOW());

-- COMMIT;

-- ========================================
-- トランザクション例：本の返却処理
-- ========================================

-- START TRANSACTION;

-- -- 返却日時を更新
-- UPDATE lendings
-- SET returned_at = NOW()
-- WHERE book_id = 1 AND member_id = 1 AND returned_at IS NULL;

-- COMMIT;

-- ========================================
-- 終わり
-- ========================================
