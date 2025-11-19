<?php
/**
 * ブログシステム - 記事詳細
 *
 * 機能:
 * - 記事内容の表示
 * - 閲覧数のカウント
 * - Markdownの簡易表示
 */

require_once 'functions.php';

// 記事IDを取得
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id <= 0) {
    setFlashMessage('error', '不正な記事IDです。');
    redirect('index.php');
}

// 記事を取得（閲覧数をインクリメント）
$post = getPostById($post_id, true);

if (!$post) {
    setFlashMessage('error', '記事が見つかりませんでした。');
    redirect('index.php');
}

// 下書き記事は作者のみ閲覧可能
if ($post['status'] === 'draft' && (!isLoggedIn() || !isCurrentUser($post['user_id']))) {
    setFlashMessage('error', 'この記事は閲覧できません。');
    redirect('index.php');
}

// Markdownの簡易変換（見出しと改行のみ）
function simpleMarkdown($text) {
    // 見出し変換
    $text = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $text);

    // コードブロック
    $text = preg_replace('/```(.*?)```/s', '<pre><code>$1</code></pre>', $text);

    // 改行を<br>に変換
    $text = nl2br($text);

    return $text;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($post['title']); ?> - ブログシステム</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f5f5; line-height: 1.6; }
        .header { background-color: #2196F3; color: white; padding: 20px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header-content { max-width: 900px; margin: 0 auto; padding: 0 20px; }
        .header a { color: white; text-decoration: none; }
        .header a:hover { text-decoration: underline; }
        .container { max-width: 900px; margin: 30px auto; padding: 0 20px; }
        .article { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .article-category { display: inline-block; background-color: #e3f2fd; color: #1565C0; padding: 4px 12px; border-radius: 12px; font-size: 14px; margin-bottom: 15px; }
        .article-title { font-size: 32px; margin-bottom: 20px; color: #333; }
        .article-meta { color: #999; padding-bottom: 20px; border-bottom: 2px solid #eee; margin-bottom: 30px; }
        .article-meta span { margin-right: 20px; }
        .article-status { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 14px; }
        .article-status.draft { background-color: #fff3e0; color: #e65100; }
        .article-status.published { background-color: #e8f5e9; color: #2e7d32; }
        .article-content { font-size: 16px; color: #444; }
        .article-content h1 { font-size: 28px; margin: 30px 0 15px; color: #333; }
        .article-content h2 { font-size: 24px; margin: 25px 0 12px; color: #333; }
        .article-content h3 { font-size: 20px; margin: 20px 0 10px; color: #333; }
        .article-content pre { background-color: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto; margin: 15px 0; }
        .article-content code { background-color: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; }
        .article-content pre code { background: none; padding: 0; }
        .article-actions { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
        .article-actions a { color: #2196F3; text-decoration: none; margin-right: 20px; }
        .article-actions a:hover { text-decoration: underline; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #2196F3; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <a href="index.php">📝 ブログシステム</a>
        </div>
    </div>

    <div class="container">
        <a href="index.php" class="back-link">← 記事一覧へ戻る</a>

        <article class="article">
            <?php if ($post['category_name']): ?>
                <div class="article-category"><?php echo h($post['category_name']); ?></div>
            <?php endif; ?>

            <h1 class="article-title"><?php echo h($post['title']); ?></h1>

            <div class="article-meta">
                <span>✍️ <?php echo h($post['author_name']); ?></span>
                <span>📅 <?php echo date('Y年m月d日 H:i', strtotime($post['created_at'])); ?></span>
                <span>👁️ <?php echo h($post['view_count']); ?>回</span>
                <?php if (isLoggedIn()): ?>
                    <span class="article-status <?php echo h($post['status']); ?>">
                        <?php echo h(getStatusLabel($post['status'])); ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="article-content">
                <?php echo simpleMarkdown($post['content']); ?>
            </div>

            <?php if (isLoggedIn() && isCurrentUser($post['user_id'])): ?>
                <div class="article-actions">
                    <a href="edit.php?id=<?php echo h($post['id']); ?>">✏️ この記事を編集</a>
                    <a href="delete.php?id=<?php echo h($post['id']); ?>" onclick="return confirm('本当に削除しますか？')">🗑️ この記事を削除</a>
                </div>
            <?php endif; ?>
        </article>
    </div>
</body>
</html>
