<?php
/**
 * ブログシステム - トップページ（記事一覧）
 *
 * 機能:
 * - 記事一覧表示
 * - カテゴリフィルタ
 * - キーワード検索
 * - ページング
 */

require_once 'functions.php';

// フィルタ条件を取得
$filters = [];
if (isset($_GET['category_id']) && $_GET['category_id'] > 0) {
    $filters['category_id'] = (int)$_GET['category_id'];
}
if (isset($_GET['keyword']) && !empty(trim($_GET['keyword']))) {
    $filters['keyword'] = trim($_GET['keyword']);
}
if (isset($_GET['my_posts']) && isLoggedIn()) {
    $filters['user_id'] = $_SESSION['user_id'];
}

// ページ番号を取得
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// 記事を取得
$result = getPosts($filters, $page);
$posts = $result['posts'];
$total_pages = $result['pages'];
$current_page = $result['current_page'];

// カテゴリ一覧を取得
$categories = getAllCategories();

// フラッシュメッセージを取得
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ブログシステム</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; }
        .header { background-color: #2196F3; color: white; padding: 20px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header-content { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 24px; }
        .header-nav a { color: white; text-decoration: none; margin-left: 20px; }
        .header-nav a:hover { text-decoration: underline; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .flash { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .flash.success { background-color: #e8f5e9; color: #2e7d32; border-left: 4px solid #2e7d32; }
        .flash.error { background-color: #ffebee; color: #c62828; border-left: 4px solid #c62828; }
        .filters { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .filters form { display: flex; gap: 10px; flex-wrap: wrap; align-items: end; }
        .filter-group { flex: 1; min-width: 200px; }
        .filter-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .filter-group select, .filter-group input[type="text"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .filter-group button { padding: 9px 20px; background-color: #2196F3; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .filter-group button:hover { background-color: #1976D2; }
        .filter-group a { padding: 9px 20px; background-color: #757575; color: white; text-decoration: none; border-radius: 4px; display: inline-block; }
        .posts-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .post-card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .post-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }
        .post-category { display: inline-block; background-color: #e3f2fd; color: #1565C0; padding: 4px 12px; border-radius: 12px; font-size: 12px; margin-bottom: 10px; }
        .post-title { font-size: 20px; margin-bottom: 10px; color: #333; }
        .post-title a { color: #333; text-decoration: none; }
        .post-title a:hover { color: #2196F3; }
        .post-excerpt { color: #666; line-height: 1.6; margin-bottom: 15px; }
        .post-meta { color: #999; font-size: 14px; border-top: 1px solid #eee; padding-top: 10px; }
        .post-meta span { margin-right: 15px; }
        .post-status { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 12px; }
        .post-status.draft { background-color: #fff3e0; color: #e65100; }
        .post-status.published { background-color: #e8f5e9; color: #2e7d32; }
        .post-actions { margin-top: 10px; }
        .post-actions a { color: #2196F3; text-decoration: none; margin-right: 15px; font-size: 14px; }
        .post-actions a:hover { text-decoration: underline; }
        .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 30px; }
        .pagination a, .pagination span { padding: 8px 12px; background-color: white; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333; }
        .pagination a:hover { background-color: #2196F3; color: white; border-color: #2196F3; }
        .pagination .current { background-color: #2196F3; color: white; border-color: #2196F3; font-weight: bold; }
        .no-posts { background: white; padding: 40px; text-align: center; border-radius: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>📝 ブログシステム</h1>
            <div class="header-nav">
                <?php if (isLoggedIn()): ?>
                    <span>👤 <?php echo h($_SESSION['user_name']); ?>さん</span>
                    <a href="add.php">✏️ 記事を書く</a>
                    <a href="?my_posts=1">📄 自分の記事</a>
                    <a href="logout.php">🚪 ログアウト</a>
                <?php else: ?>
                    <a href="login.php">🔐 ログイン</a>
                    <a href="register.php">📝 新規登録</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if ($flash): ?>
            <div class="flash <?php echo h($flash['type']); ?>">
                <?php echo h($flash['message']); ?>
            </div>
        <?php endif; ?>

        <div class="filters">
            <form method="GET" action="">
                <div class="filter-group">
                    <label for="category_id">カテゴリ</label>
                    <select name="category_id" id="category_id">
                        <option value="">すべてのカテゴリ</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo h($category['id']); ?>"
                                <?php echo (isset($filters['category_id']) && $filters['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo h($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="keyword">キーワード検索</label>
                    <input type="text" name="keyword" id="keyword" value="<?php echo h($filters['keyword'] ?? ''); ?>" placeholder="タイトルや内容で検索">
                </div>
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button type="submit">🔍 検索</button>
                </div>
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <a href="index.php">クリア</a>
                </div>
            </form>
        </div>

        <?php if (empty($posts)): ?>
            <div class="no-posts">
                <h2>📭 記事が見つかりませんでした</h2>
                <p>検索条件を変更してお試しください。</p>
            </div>
        <?php else: ?>
            <div class="posts-grid">
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <?php if ($post['category_name']): ?>
                            <div class="post-category"><?php echo h($post['category_name']); ?></div>
                        <?php endif; ?>

                        <h2 class="post-title">
                            <a href="view.php?id=<?php echo h($post['id']); ?>">
                                <?php echo h($post['title']); ?>
                            </a>
                        </h2>

                        <div class="post-excerpt">
                            <?php echo h($post['excerpt'] ?: generateExcerpt($post['content'])); ?>
                        </div>

                        <div class="post-meta">
                            <span>👤 <?php echo h($post['author_name']); ?></span>
                            <span>📅 <?php echo h(timeAgo($post['created_at'])); ?></span>
                            <span>👁️ <?php echo h($post['view_count']); ?>回</span>
                            <?php if (isLoggedIn()): ?>
                                <span class="post-status <?php echo h($post['status']); ?>">
                                    <?php echo h(getStatusLabel($post['status'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if (isLoggedIn() && isCurrentUser($post['user_id'])): ?>
                            <div class="post-actions">
                                <a href="edit.php?id=<?php echo h($post['id']); ?>">✏️ 編集</a>
                                <a href="delete.php?id=<?php echo h($post['id']); ?>" onclick="return confirm('本当に削除しますか？')">🗑️ 削除</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($current_page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">最初</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">← 前へ</a>
                    <?php endif; ?>

                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);

                    for ($i = $start_page; $i <= $end_page; $i++):
                        if ($i == $current_page):
                    ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                    <?php
                        endif;
                    endfor;
                    ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">次へ →</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>">最後</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
