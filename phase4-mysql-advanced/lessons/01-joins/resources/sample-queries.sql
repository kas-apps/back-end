-- ========================================
-- Lesson 01: JOIN（結合）サンプルクエリ集
-- ========================================

-- このファイルには、Lesson 01で学ぶJOINのサンプルクエリをまとめています。
-- phpMyAdminのSQLタブで実行して、結果を確認してください。

-- ========================================
-- 準備：サンプルデータベースの作成
-- ========================================

-- データベース作成
CREATE DATABASE IF NOT EXISTS blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_db;

-- テーブル削除（既存データをリセット）
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- usersテーブル作成
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email) VALUES
('太郎', 'taro@example.com'),
('花子', 'hanako@example.com'),
('次郎', 'jiro@example.com'),
('美咲', 'misaki@example.com');

-- categoriesテーブル作成
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

INSERT INTO categories (name) VALUES
('技術'),
('日記'),
('趣味'),
('旅行');

-- postsテーブル作成
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

INSERT INTO posts (user_id, category_id, title, content, created_at) VALUES
(1, 1, '初めての投稿', 'こんにちは！', '2024-01-01 10:00:00'),
(1, 1, 'PHP学習中', '楽しいです！', '2024-01-02 11:00:00'),
(2, 2, '花子の日記', 'よろしくお願いします', '2024-01-03 12:00:00'),
(2, 3, '趣味について', '読書が好きです', '2024-01-04 13:00:00'),
(3, 4, '旅行記', '京都に行ってきました', '2024-01-05 14:00:00');

-- commentsテーブル作成
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO comments (post_id, user_id, content) VALUES
(1, 2, '素晴らしい投稿ですね！'),
(1, 3, 'ありがとうございます'),
(2, 2, 'PHP楽しいですよね'),
(3, 1, 'よろしく！');

-- ========================================
-- 実例1：INNER JOIN（内部結合）
-- ========================================

-- 基本的なINNER JOIN
SELECT
    users.name,
    posts.title,
    posts.content
FROM
    users
INNER JOIN
    posts
ON
    users.id = posts.user_id;

-- ========================================
-- 実例2：LEFT JOIN（左外部結合）
-- ========================================

-- 投稿がないユーザーも表示
SELECT
    users.name,
    posts.title,
    posts.content
FROM
    users
LEFT JOIN
    posts
ON
    users.id = posts.user_id;

-- ========================================
-- 実例3：エイリアス（別名）を使う
-- ========================================

SELECT
    u.name,
    p.title,
    p.content
FROM
    users AS u
INNER JOIN
    posts AS p
ON
    u.id = p.user_id;

-- ========================================
-- 実例4：3つのテーブルをJOIN
-- ========================================

-- ユーザー名、投稿タイトル、カテゴリ名を取得
SELECT
    u.name AS user_name,
    p.title AS post_title,
    c.name AS category_name
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
INNER JOIN
    categories AS c ON p.category_id = c.id;

-- ========================================
-- 実例5：WHERE句と組み合わせる
-- ========================================

-- 「技術」カテゴリの投稿のみ取得
SELECT
    u.name,
    p.title,
    c.name AS category_name
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
INNER JOIN
    categories AS c ON p.category_id = c.id
WHERE
    c.name = '技術';

-- ========================================
-- 実例6：ORDER BYで並び替え
-- ========================================

-- 投稿日時が新しい順に並び替え
SELECT
    u.name,
    p.title,
    p.created_at
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
ORDER BY
    p.created_at DESC;

-- ========================================
-- 実例7：コメントと投稿、ユーザーをJOIN
-- ========================================

-- コメント投稿者、投稿タイトル、コメント内容を取得
SELECT
    u.name AS commenter_name,
    p.title AS post_title,
    c.content AS comment_content
FROM
    comments AS c
INNER JOIN
    posts AS p ON c.post_id = p.id
INNER JOIN
    users AS u ON c.user_id = u.id;

-- ========================================
-- 実例8：投稿者とコメント投稿者の両方を表示
-- ========================================

-- usersテーブルを2回JOINする
SELECT
    post_author.name AS post_author,
    p.title AS post_title,
    commenter.name AS commenter,
    c.content AS comment_content
FROM
    comments AS c
INNER JOIN
    posts AS p ON c.post_id = p.id
INNER JOIN
    users AS post_author ON p.user_id = post_author.id
INNER JOIN
    users AS commenter ON c.user_id = commenter.id;

-- ========================================
-- 実例9：投稿がないユーザーのみ表示
-- ========================================

-- LEFT JOINとWHERE IS NULLを組み合わせる
SELECT
    u.name AS user_name
FROM
    users AS u
LEFT JOIN
    posts AS p ON u.id = p.user_id
WHERE
    p.id IS NULL;

-- ========================================
-- 実例10：各ユーザーの投稿数を表示
-- ========================================

-- LEFT JOINとCOUNT、GROUP BYを組み合わせる
SELECT
    u.name AS user_name,
    COUNT(p.id) AS post_count
FROM
    users AS u
LEFT JOIN
    posts AS p ON u.id = p.user_id
GROUP BY
    u.id, u.name;

-- ========================================
-- 実例11：サブクエリで投稿が2件以上のユーザーを取得
-- ========================================

-- サブクエリを使って条件を指定
SELECT
    u.name AS user_name,
    p.title AS post_title
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
WHERE
    u.id IN (
        SELECT user_id
        FROM posts
        GROUP BY user_id
        HAVING COUNT(*) >= 2
    );

-- ========================================
-- 実例12：LIMITで件数を制限
-- ========================================

-- 最新の3件の投稿を取得
SELECT
    u.name AS user_name,
    p.title AS post_title,
    p.created_at
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
ORDER BY
    p.created_at DESC
LIMIT 3;

-- ========================================
-- 実例13：複数の条件でフィルタ
-- ========================================

-- 「技術」カテゴリで、2024年1月2日以降の投稿を取得
SELECT
    u.name AS user_name,
    p.title AS post_title,
    c.name AS category_name,
    p.created_at
FROM
    users AS u
INNER JOIN
    posts AS p ON u.id = p.user_id
INNER JOIN
    categories AS c ON p.category_id = c.id
WHERE
    c.name = '技術'
    AND p.created_at >= '2024-01-02 00:00:00';

-- ========================================
-- 補足：RIGHT JOIN（右外部結合）
-- ========================================

-- RIGHT JOINの例（実務では少ない）
-- postsテーブルの全データ + マッチするusersデータ
SELECT
    u.name,
    p.title
FROM
    users AS u
RIGHT JOIN
    posts AS p ON u.id = p.user_id;

-- 注：LEFT JOINの方が読みやすいので、実務ではLEFT JOINを使うことが多い

-- ========================================
-- 終わり
-- ========================================
