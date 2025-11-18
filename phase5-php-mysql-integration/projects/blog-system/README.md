# 総合プロジェクト1: ブログシステム 📝

**プロジェクト概要**：Phase 5で学んだすべての技術を使って、完全なブログシステムを構築する！

## 機能一覧

### ユーザー機能
- ユーザー登録（password_hash()）
- ログイン（password_verify()、session_regenerate_id()）
- ログアウト

### 記事機能
- 記事投稿（INSERT）
- 記事一覧（SELECT、ページング）
- 記事詳細（SELECT、JOIN）
- 記事編集（UPDATE）
- 記事削除（DELETE）

### カテゴリ機能
- カテゴリ別記事一覧（JOIN）

### コメント機能
- コメント投稿
- コメント一覧

## データベース設計

```sql
CREATE DATABASE IF NOT EXISTS blog_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_system;

-- ユーザーテーブル
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 記事テーブル
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## セキュリティチェックリスト

✅ SQLインジェクション対策（プリペアドステートメント）
✅ XSS対策（htmlspecialchars()）
✅ CSRF対策（トークン）
✅ パスワードハッシュ化（password_hash()）
✅ セッション管理（session_regenerate_id()）

**Let's vibe and code! 🎉**
