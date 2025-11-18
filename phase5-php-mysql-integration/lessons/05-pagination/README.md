# Lesson 05: ページング処理 📄

**学習目標**：大量データを効率的に表示するページング処理を実装できるようになる！

## ページングの実装

```php
<?php
$per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $per_page;

$stmt = $pdo->prepare("SELECT * FROM posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

👉 **[演習問題を見る](exercises/README.md)**

**Let's vibe and code! 🎉**
