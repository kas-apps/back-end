-- ブログシステム データベース設計
-- セキュアで効率的なブログシステムのためのスキーマ

-- データベース作成
CREATE DATABASE IF NOT EXISTS blog_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_system;

-- ユーザーテーブル
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ユーザー情報テーブル';

-- カテゴリテーブル
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='記事カテゴリテーブル';

-- 記事テーブル
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    excerpt VARCHAR(500),
    status ENUM('draft', 'published') DEFAULT 'draft',
    view_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_category_id (category_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_published_at (published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='記事テーブル';

-- サンプルユーザーデータ（パスワードは全て "password123"）
INSERT INTO users (name, email, password_hash) VALUES
('山田太郎', 'yamada@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('佐藤花子', 'sato@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('鈴木一郎', 'suzuki@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- サンプルカテゴリデータ
INSERT INTO categories (name, slug, description) VALUES
('技術', 'technology', 'プログラミングや開発に関する記事'),
('デザイン', 'design', 'UI/UXデザインに関する記事'),
('ビジネス', 'business', 'ビジネスやマーケティングに関する記事'),
('ライフスタイル', 'lifestyle', '日常生活や趣味に関する記事'),
('チュートリアル', 'tutorial', '学習用のチュートリアル記事');

-- サンプル記事データ
INSERT INTO posts (user_id, category_id, title, content, excerpt, status, view_count, published_at) VALUES
(1, 1, 'PHPとMySQLで作るセキュアなWebアプリケーション',
'## はじめに\n\nWebアプリケーション開発において、セキュリティは最も重要な要素の一つです。この記事では、PHPとMySQLを使用して、セキュアなWebアプリケーションを構築する方法を解説します。\n\n## SQLインジェクション対策\n\nプリペアドステートメントを使用することで、SQLインジェクション攻撃を防ぐことができます。\n\n```php\n$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");\n$stmt->execute([":email" => $email]);\n```\n\n## XSS対策\n\nユーザー入力を出力する際は、必ずhtmlspecialchars()を使用します。\n\n```php\necho htmlspecialchars($user_input, ENT_QUOTES, "UTF-8");\n```\n\n## まとめ\n\nセキュリティは後回しにせず、開発の最初から組み込むことが重要です。',
'PHPとMySQLを使用したセキュアなWebアプリケーション開発のベストプラクティスを紹介します。',
'published', 152, NOW()),

(1, 5, 'バイブコーディング入門：AIと協働する新しい開発スタイル',
'## バイブコーディングとは？\n\nバイブコーディングは、AI（特にClaude CodeやGitHub Copilot）を活用した新しいコーディングスタイルです。\n\n## AIへの指示のコツ\n\n良い指示の例：\n「MySQLのusersテーブルからメールアドレスでユーザーを検索するPHPコードを書いてください。SQLインジェクション対策として、必ずプリペアドステートメントを使ってください。」\n\n悪い指示の例：\n「ユーザー検索のコードを書いて」\n\n## 生成コードのレビューポイント\n\n- セキュリティ対策が含まれているか\n- エラーハンドリングは適切か\n- コードは読みやすいか\n\n## まとめ\n\nAIは強力なツールですが、最終的な判断は人間が行う必要があります。',
'AIを活用した効率的なコーディング方法「バイブコーディング」について解説します。',
'published', 89, NOW()),

(2, 2, 'モダンなUIデザインの基本原則',
'## デザインの基本\n\nユーザーインターフェースデザインには、いくつかの重要な原則があります。\n\n### 1. 一貫性\n\n同じ機能には同じデザインパターンを使用します。\n\n### 2. フィードバック\n\nユーザーの操作に対して、適切なフィードバックを提供します。\n\n### 3. シンプルさ\n\n不要な要素を削除し、本質的な機能に集中します。\n\n### 4. アクセシビリティ\n\nすべてのユーザーが使いやすいデザインを心がけます。',
'美しく使いやすいUIを作るための基本原則を紹介します。',
'published', 234, NOW()),

(2, 3, 'スタートアップ企業でのプロダクト開発の進め方',
'## MVPの重要性\n\nMinimum Viable Product（実用最小限の製品）は、スタートアップにとって非常に重要です。\n\n## イテレーション\n\n小さく始めて、ユーザーフィードバックを元に改善を繰り返します。\n\n## チーム開発\n\n効率的なコミュニケーションとタスク管理が成功の鍵です。',
'スタートアップでのプロダクト開発における実践的なアプローチを解説します。',
'published', 67, NOW()),

(3, 1, 'REST API設計のベストプラクティス',
'## RESTful APIとは\n\nREST（Representational State Transfer）は、Webサービスを設計するためのアーキテクチャスタイルです。\n\n## HTTPメソッドの使い分け\n\n- GET: リソースの取得\n- POST: リソースの作成\n- PUT: リソースの更新\n- DELETE: リソースの削除\n\n## エンドポイント設計\n\n```\nGET    /api/posts      - 記事一覧取得\nGET    /api/posts/:id  - 記事詳細取得\nPOST   /api/posts      - 記事作成\nPUT    /api/posts/:id  - 記事更新\nDELETE /api/posts/:id  - 記事削除\n```\n\n## レスポンス形式\n\nJSON形式で統一し、適切なHTTPステータスコードを返します。',
'スケーラブルで保守性の高いREST APIを設計する方法を紹介します。',
'published', 198, NOW()),

(3, 5, 'GitとGitHubを使ったチーム開発入門',
'## バージョン管理の重要性\n\nGitを使うことで、コードの変更履歴を管理し、チームでの協働が可能になります。\n\n## 基本的なワークフロー\n\n1. ブランチを作成\n2. コードを修正\n3. コミット\n4. プルリクエスト作成\n5. レビュー\n6. マージ\n\n## よく使うコマンド\n\n```bash\ngit clone <repository>\ngit checkout -b feature/new-feature\ngit add .\ngit commit -m "Add new feature"\ngit push origin feature/new-feature\n```',
'Git と GitHub を使った効率的なチーム開発の方法を解説します。',
'published', 321, NOW()),

(1, 4, '在宅勤務で生産性を上げる5つの方法',
'## リモートワークの課題\n\n在宅勤務は便利ですが、集中力の維持が難しいこともあります。\n\n## 生産性向上のコツ\n\n1. 専用のワークスペースを確保\n2. 定期的な休憩を取る\n3. タスクを細かく分割\n4. コミュニケーションツールの活用\n5. 運動習慣を取り入れる\n\n## まとめ\n\n自分に合った働き方を見つけることが大切です。',
'在宅勤務での生産性を高めるための実践的なテクニックを紹介します。',
'published', 145, NOW()),

(2, 1, 'TypeScriptで型安全なコードを書く',
'## TypeScriptの利点\n\nTypeScriptは、JavaScriptに型システムを追加した言語です。型安全性により、多くのバグを開発段階で発見できます。\n\n## 基本的な型定義\n\n```typescript\ninterface User {\n  id: number;\n  name: string;\n  email: string;\n}\n\nfunction getUser(id: number): User {\n  // ...\n}\n```\n\n## ジェネリクス\n\n再利用可能な型安全なコードを書くことができます。\n\n```typescript\nfunction identity<T>(arg: T): T {\n  return arg;\n}\n```',
'TypeScriptを使って、より安全で保守性の高いコードを書く方法を解説します。',
'draft', 0, NULL),

(3, 2, 'Figmaでのプロトタイピング実践ガイド',
'## Figmaとは\n\nFigmaは、ブラウザベースのUIデザインツールです。チームでのリアルタイムコラボレーションが可能です。\n\n## プロトタイピングの手順\n\n1. ワイヤーフレーム作成\n2. ビジュアルデザイン\n3. インタラクション設定\n4. 共有とフィードバック\n\n## 便利な機能\n\n- コンポーネント\n- Auto Layout\n- プラグイン\n- バージョン管理',
'Figmaを使った効率的なプロトタイピングの方法を紹介します。',
'draft', 0, NULL);
