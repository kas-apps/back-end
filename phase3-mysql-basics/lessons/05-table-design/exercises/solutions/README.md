# Lesson 05: 基本的なテーブル設計 - 解答例 ✅

演習お疲れさま！ここでは各演習の解答例と詳しい解説を紹介するよ。

---

## 🎯 基礎編：制約とインデックス

### 演習 1-1: PRIMARY KEYとAUTO_INCREMENT ✅

**解答**：

```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**解説**：

- `AUTO_INCREMENT PRIMARY KEY`：idが自動で1, 2, 3...と増える
- `UNIQUE`：name は重複を許可しない（同じカテゴリ名は1つだけ）

---

### 演習 1-2: FOREIGN KEY制約 ✅

**解答**：

```sql
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_name VARCHAR(100) NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**解説**：

- `FOREIGN KEY (student_id) REFERENCES students(id)`：student_idがstudentsテーブルのidを参照
- `ON DELETE CASCADE`：学生が削除されたら、その学生の履修登録も自動削除

---

### 演習 1-3: UNIQUE制約 ✅

**解答**：

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**解説**：

- `UNIQUE`：username と email は重複を許可しない
- 自動的にインデックスが作成されるので、検索も高速

---

## 🚀 応用編：実践的なテーブル設計

### 演習 2-1: ブログシステムのテーブル設計 ✅

**解答**：

```sql
-- 1. usersテーブル
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. postsテーブル
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. commentsテーブル
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_post_id (post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**解説**：

- **外部キー制約**：user_id、post_idで親テーブルを参照
- **ON DELETE CASCADE**：親レコード削除時、子レコードも自動削除
- **インデックス**：頻繁に検索するカラム（user_id、created_at、post_id）に設定

---

### 演習 2-2: ECサイトのテーブル設計 ✅

**解答**：

```sql
-- 1. categoriesテーブル
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. productsテーブル
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    INDEX idx_category_id (category_id),
    INDEX idx_price (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**解説**：

- **外部キー制約**：category_idでcategoriesテーブルを参照
- **インデックス**：category_id（カテゴリ別検索）、price（価格順ソート）に設定
- **DECIMAL(10, 2)**：価格は正確な値を保存

---

## 💡 チャレンジ問題

### チャレンジ 1: タスク管理システムのテーブル設計 ✅

**解答**：

```sql
-- 1. usersテーブル
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. projectsテーブル
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. tasksテーブル
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    assigned_user_id INT,
    title VARCHAR(200) NOT NULL,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_project_id (project_id),
    INDEX idx_assigned_user_id (assigned_user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**解説**：

- **project_id**：ON DELETE CASCADE（プロジェクト削除時、タスクも削除）
- **assigned_user_id**：ON DELETE SET NULL（ユーザー削除時、タスクは残すが担当者をNULLに）
- **ENUM型**：statusは3つの値だけを許可
- **インデックス**：project_id、assigned_user_id、statusに設定（検索高速化）

---

### チャレンジ 2: AIにテーブル設計を依頼 ✅

**AIが生成するSQL**：

```sql
-- 1. usersテーブル
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. postsテーブル
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. followsテーブル
CREATE TABLE follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_follow (follower_id, following_id),
    INDEX idx_follower_id (follower_id),
    INDEX idx_following_id (following_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**チェックポイント**：

- ✅ 外部キー制約が適切に設定されている
- ✅ ON DELETE CASCADEが設定されている
- ✅ followsテーブルに UNIQUE制約（follower_id, following_id の組み合わせが一意）
- ✅ インデックスが適切に設定されている

**解説**：

- **followsテーブル**：フォロー関係を管理（follower_idがfollowing_idをフォロー）
- **UNIQUE制約**：同じユーザーを2回フォローできないようにする
- **複合インデックス**：follower_id + following_id で検索高速化

---

## 🎓 まとめ

演習を通じて、テーブル設計をマスターしたね！

### 重要なポイント

- ✅ **PRIMARY KEY**：各レコードを一意に識別
- ✅ **FOREIGN KEY**：1対多の関係を表現、データ整合性を保つ
- ✅ **UNIQUE**：重複を許可しない
- ✅ **NOT NULL**：必須項目
- ✅ **ON DELETE CASCADE**：親レコード削除時、子レコードも削除
- ✅ **INDEX**：頻繁に検索するカラムに設定、検索高速化

### 次のステップ

**Phase 3（MySQL基礎）の全レッスンを修了しました！おめでとう！**

次は以下のステップに進もう：

- **Phase 4（MySQL発展）**：JOIN、サブクエリ、集計関数、正規化の詳細
- **Phase 5（PHP+MySQL統合）**：PHPからMySQLに接続、CRUD操作、ログイン機能

実践的なWebアプリケーションを作って、学んだことを活かそう！

---

**Let's vibe and code! 🎉**

テーブル設計は、データベースの基盤！正規化と制約を理解して、効率的で保守性の高いデータベースを設計できるようになったね！
