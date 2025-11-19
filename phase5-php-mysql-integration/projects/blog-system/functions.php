<?php
/**
 * ブログシステム - 共通関数
 *
 * 記事のCRUD操作、カテゴリ管理、ヘルパー関数を提供
 * すべての関数でセキュリティ対策を実施
 */

require_once 'config.php';
require_once 'auth_functions.php';

/**
 * XSS対策のためのHTMLエスケープ
 *
 * @param string $str エスケープする文字列
 * @return string エスケープされた文字列
 */
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * リダイレクト
 *
 * @param string $url リダイレクト先URL
 * @return void
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * フラッシュメッセージを設定
 *
 * @param string $type メッセージタイプ（success, error, info）
 * @param string $message メッセージ内容
 * @return void
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * フラッシュメッセージを取得（取得後は削除される）
 *
 * @return array|null フラッシュメッセージ
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// ========================================
// カテゴリ関連関数
// ========================================

/**
 * 全カテゴリを取得
 *
 * @return array カテゴリ一覧
 */
function getAllCategories() {
    $pdo = getDB();

    try {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Get categories error: ' . $e->getMessage());
        return [];
    }
}

/**
 * カテゴリIDからカテゴリ情報を取得
 *
 * @param int $category_id カテゴリID
 * @return array|null カテゴリ情報
 */
function getCategoryById($category_id) {
    $pdo = getDB();

    try {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Get category error: ' . $e->getMessage());
        return null;
    }
}

/**
 * カテゴリスラッグからカテゴリ情報を取得
 *
 * @param string $slug カテゴリスラッグ
 * @return array|null カテゴリ情報
 */
function getCategoryBySlug($slug) {
    $pdo = getDB();

    try {
        $sql = "SELECT * FROM categories WHERE slug = :slug";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Get category error: ' . $e->getMessage());
        return null;
    }
}

// ========================================
// 記事関連関数
// ========================================

/**
 * 記事一覧を取得
 *
 * @param array $filters フィルタ条件（category_id, status, keyword）
 * @param int $page ページ番号
 * @param int $per_page 1ページあたりの記事数
 * @return array ['posts' => array, 'total' => int, 'pages' => int]
 */
function getPosts($filters = [], $page = 1, $per_page = POSTS_PER_PAGE) {
    $pdo = getDB();
    $where_conditions = [];
    $bind_params = [];

    // ステータスフィルタ（未ログインユーザーには公開済みのみ表示）
    if (!isLoggedIn()) {
        $where_conditions[] = 'p.status = :status';
        $bind_params[':status'] = 'published';
    } elseif (isset($filters['status']) && validateStatus($filters['status'])) {
        $where_conditions[] = 'p.status = :status';
        $bind_params[':status'] = $filters['status'];
    }

    // カテゴリフィルタ
    if (isset($filters['category_id']) && $filters['category_id'] > 0) {
        $where_conditions[] = 'p.category_id = :category_id';
        $bind_params[':category_id'] = (int)$filters['category_id'];
    }

    // ユーザーフィルタ（自分の記事のみ）
    if (isset($filters['user_id']) && $filters['user_id'] > 0) {
        $where_conditions[] = 'p.user_id = :user_id';
        $bind_params[':user_id'] = (int)$filters['user_id'];
    }

    // キーワード検索
    if (!empty($filters['keyword'])) {
        $where_conditions[] = '(p.title LIKE :keyword OR p.content LIKE :keyword)';
        $bind_params[':keyword'] = '%' . $filters['keyword'] . '%';
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    try {
        // 総件数を取得
        $count_sql = "SELECT COUNT(*) FROM posts p $where_clause";
        $count_stmt = $pdo->prepare($count_sql);
        foreach ($bind_params as $key => $value) {
            $count_stmt->bindValue($key, $value);
        }
        $count_stmt->execute();
        $total = $count_stmt->fetchColumn();

        // ページング計算
        $total_pages = ceil($total / $per_page);
        $page = max(1, min($page, $total_pages ?: 1));
        $offset = ($page - 1) * $per_page;

        // 記事を取得（JOINでユーザーとカテゴリ情報も取得）
        $sql = "SELECT
                    p.*,
                    u.name AS author_name,
                    c.name AS category_name,
                    c.slug AS category_slug
                FROM posts p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                $where_clause
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        foreach ($bind_params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'posts' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => $total_pages,
            'current_page' => $page
        ];
    } catch (PDOException $e) {
        error_log('Get posts error: ' . $e->getMessage());
        return ['posts' => [], 'total' => 0, 'pages' => 0, 'current_page' => 1];
    }
}

/**
 * 記事IDから記事を取得
 *
 * @param int $post_id 記事ID
 * @param bool $increment_view 閲覧数をインクリメントするか
 * @return array|null 記事情報
 */
function getPostById($post_id, $increment_view = false) {
    $pdo = getDB();

    try {
        $sql = "SELECT
                    p.*,
                    u.name AS author_name,
                    u.email AS author_email,
                    c.name AS category_name,
                    c.slug AS category_slug
                FROM posts p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->execute();

        $post = $stmt->fetch();

        // 閲覧数をインクリメント
        if ($post && $increment_view) {
            incrementViewCount($post_id);
            $post['view_count']++;
        }

        return $post;
    } catch (PDOException $e) {
        error_log('Get post error: ' . $e->getMessage());
        return null;
    }
}

/**
 * 記事を作成
 *
 * @param array $data 記事データ
 * @return int|false 作成された記事ID（失敗時はfalse）
 */
function createPost($data) {
    $pdo = getDB();

    try {
        $sql = "INSERT INTO posts (user_id, category_id, title, content, excerpt, status, published_at)
                VALUES (:user_id, :category_id, :title, :content, :excerpt, :status, :published_at)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':category_id', !empty($data['category_id']) ? $data['category_id'] : null, PDO::PARAM_INT);
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':content', $data['content'], PDO::PARAM_STR);
        $stmt->bindValue(':excerpt', $data['excerpt'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'] ?? 'draft', PDO::PARAM_STR);

        // 公開ステータスの場合、公開日時を設定
        $published_at = ($data['status'] ?? 'draft') === 'published' ? date('Y-m-d H:i:s') : null;
        $stmt->bindValue(':published_at', $published_at, PDO::PARAM_STR);

        $stmt->execute();

        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log('Create post error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 記事を更新
 *
 * @param int $post_id 記事ID
 * @param array $data 記事データ
 * @return bool 成功時true
 */
function updatePost($post_id, $data) {
    $pdo = getDB();

    try {
        // 既存の記事を取得
        $existing_post = getPostById($post_id);
        if (!$existing_post) {
            return false;
        }

        // ステータスが下書き→公開に変わる場合、公開日時を設定
        $published_at = $existing_post['published_at'];
        if ($existing_post['status'] === 'draft' && $data['status'] === 'published' && !$published_at) {
            $published_at = date('Y-m-d H:i:s');
        }

        $sql = "UPDATE posts
                SET category_id = :category_id,
                    title = :title,
                    content = :content,
                    excerpt = :excerpt,
                    status = :status,
                    published_at = :published_at
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':category_id', !empty($data['category_id']) ? $data['category_id'] : null, PDO::PARAM_INT);
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':content', $data['content'], PDO::PARAM_STR);
        $stmt->bindValue(':excerpt', $data['excerpt'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'] ?? 'draft', PDO::PARAM_STR);
        $stmt->bindValue(':published_at', $published_at, PDO::PARAM_STR);

        return $stmt->execute();
    } catch (PDOException $e) {
        error_log('Update post error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 記事を削除
 *
 * @param int $post_id 記事ID
 * @return bool 成功時true
 */
function deletePost($post_id) {
    $pdo = getDB();

    try {
        $sql = "DELETE FROM posts WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log('Delete post error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 閲覧数をインクリメント
 *
 * @param int $post_id 記事ID
 * @return bool 成功時true
 */
function incrementViewCount($post_id) {
    $pdo = getDB();

    try {
        $sql = "UPDATE posts SET view_count = view_count + 1 WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log('Increment view count error: ' . $e->getMessage());
        return false;
    }
}

// ========================================
// バリデーション関数
// ========================================

/**
 * 記事タイトルをバリデーション
 *
 * @param string $title タイトル
 * @return array エラーメッセージの配列
 */
function validateTitle($title) {
    $errors = [];

    if (empty($title)) {
        $errors[] = 'タイトルは必須です。';
    } elseif (mb_strlen($title) > 255) {
        $errors[] = 'タイトルは255文字以内で入力してください。';
    }

    return $errors;
}

/**
 * 記事内容をバリデーション
 *
 * @param string $content 内容
 * @return array エラーメッセージの配列
 */
function validateContent($content) {
    $errors = [];

    if (empty($content)) {
        $errors[] = '内容は必須です。';
    }

    return $errors;
}

/**
 * ステータスをバリデーション
 *
 * @param string $status ステータス
 * @return bool 有効な場合true
 */
function validateStatus($status) {
    $allowed = ['draft', 'published'];
    return in_array($status, $allowed, true);
}

// ========================================
// ヘルパー関数
// ========================================

/**
 * 抜粋を生成（HTMLタグを削除し、指定文字数で切る）
 *
 * @param string $content 本文
 * @param int $length 文字数
 * @return string 抜粋
 */
function generateExcerpt($content, $length = EXCERPT_LENGTH) {
    // Markdownの見出し記号などを削除
    $text = preg_replace('/^#+\s+/m', '', $content);
    $text = preg_replace('/```.*?```/s', '', $text);

    // HTMLタグを削除
    $text = strip_tags($text);

    // 改行を空白に置換
    $text = preg_replace('/\s+/', ' ', $text);

    // 指定文字数で切る
    if (mb_strlen($text) > $length) {
        $text = mb_substr($text, 0, $length) . '...';
    }

    return trim($text);
}

/**
 * 日時を相対表記に変換
 *
 * @param string $datetime 日時
 * @return string 相対表記
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return '1分以内';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . '分前';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . '時間前';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . '日前';
    } else {
        return date('Y年m月d日', $timestamp);
    }
}

/**
 * ステータスのラベルを取得
 *
 * @param string $status ステータス
 * @return string ラベル
 */
function getStatusLabel($status) {
    $labels = [
        'draft' => '下書き',
        'published' => '公開中'
    ];

    return $labels[$status] ?? '不明';
}
