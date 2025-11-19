# Lesson 05: ページネーション（ページング処理） 📄

**学習目標**：大量のデータを効率的に表示するページネーション（ページング処理）を実装し、ユーザーフレンドリーで高速なアプリケーションを構築できるようになる！

---

## 📖 このレッスンで学ぶこと

- **ページネーションとは何か**（なぜ必要なのか）
- **LIMIT と OFFSET**の使い方
- **総ページ数の計算**（COUNT()とceil()）
- **ページネーションリンクの実装**（前へ・次へ・ページ番号）
- **検索結果のページネーション**（検索キーワード + ページング）
- **カテゴリフィルター + ページネーション**（複数パラメータの管理）
- **パフォーマンス最適化**（OFFSET問題と解決策）
- **セキュリティ対策**（ページ番号のバリデーション、XSS対策）
- **ユーザビリティ向上**（現在のページ強調、無効ボタンのスタイリング）

---

## 🎯 なぜページネーションを学ぶの？（Why）

### ページネーションは必須の機能！

**ページネーションなし**：

```text
❌ 全1,000件の記事を1ページに表示
  ↓
😱 ページの読み込みが遅い（10秒以上）
😱 ブラウザが重くなる
😱 スクロールが大変
😱 サーバーのメモリを大量消費
😱 ユーザーが離脱する
```

**ページネーションあり**：

```text
✅ 1ページ10件ずつ表示（100ページ）
  ↓
🚀 ページの読み込みが速い（1秒以内）
✨ ブラウザが軽快
👍 欲しい情報が見つけやすい
💾 サーバーの負荷が低い
😊 ユーザーが快適に使える
```

### 実例：ページネーションが必要な場面

| サイト/機能 | データ量 | ページネーション |
|-----------|--------|--------------|
| **Google検索** | 数億件 | 10件ずつ表示、ページ番号リンク |
| **Amazon商品一覧** | 数千〜数万件 | 48件ずつ表示、無限スクロール |
| **Twitterタイムライン** | 無限 | 自動読み込み（無限スクロール） |
| **ブログ記事一覧** | 数百〜数千件 | 10〜20件ずつ表示 |
| **ECサイト商品検索** | 数千〜数万件 | 20〜50件ずつ表示 |

### ページネーションの3つのメリット

✅ **1. パフォーマンス向上**

- データベースから必要なデータだけ取得
- ページの読み込み時間が短縮
- サーバーの負荷が軽減

✅ **2. ユーザビリティ向上**

- スクロールが少なく、見やすい
- 欲しい情報を素早く見つけられる
- ページ番号で特定ページに直接アクセス可能

✅ **3. スケーラビリティ**

- データが増えてもパフォーマンスが落ちにくい
- 数万件、数十万件のデータにも対応可能

### バックエンド開発における重要性

**ページネーションはバックエンドの責任**！

| フロントエンド | バックエンド |
|-------------|------------|
| ページネーションリンクの表示 | データの取得（LIMIT, OFFSET） |
| ユーザーのクリック処理 | 総ページ数の計算 |
| UI/UXデザイン | パフォーマンス最適化 |

**バックエンドがページネーションを実装しないと、パフォーマンスが悪化する！**

---

## 🏗️ ページネーションの基礎知識（What）

### ページネーションの仕組み

**ページネーションの数学**：

```text
【与えられる情報】
- 総件数（total_count）：100件
- 1ページあたりの件数（per_page）：10件
- 現在のページ番号（current_page）：3ページ目

【計算する情報】
- 総ページ数（total_pages）= ceil(100 / 10) = 10ページ
- OFFSET = (3 - 1) × 10 = 20
  → データベースから21〜30番目のデータを取得

【SQL】
SELECT * FROM articles ORDER BY created_at DESC LIMIT 10 OFFSET 20;
```

### LIMIT と OFFSET

**LIMIT**：取得する件数を制限

```sql
-- 最初の10件を取得
SELECT * FROM articles ORDER BY created_at DESC LIMIT 10;
```

**OFFSET**：開始位置を指定

```sql
-- 11〜20件目を取得（最初の10件をスキップ）
SELECT * FROM articles ORDER BY created_at DESC LIMIT 10 OFFSET 10;
```

**ページネーションでの使い方**：

| ページ | LIMIT | OFFSET | 取得するデータ |
|-------|-------|--------|-------------|
| 1ページ目 | 10 | 0 | 1〜10件目 |
| 2ページ目 | 10 | 10 | 11〜20件目 |
| 3ページ目 | 10 | 20 | 21〜30件目 |
| Nページ目 | 10 | (N-1)×10 | ... |

### ページネーションのパターン

**1. 番号付きページネーション**（このレッスンで実装）

```text
« 前へ  [1] [2] [3] [4] [5]  次へ »
         現在のページを強調
```

**2. 無限スクロール**（SNS、画像サイトなど）

```text
スクロールすると自動的に次のデータを読み込む
```

**3. 「もっと見る」ボタン**（記事一覧など）

```text
ボタンをクリックすると次のデータを追加表示
```

---

## 💻 基本的なページネーションの実装（How）

### ステップ1: ページ番号を取得

```php
<?php
require_once 'config.php';

// URLパラメータからページ番号を取得（デフォルト: 1）
$current_page = $_GET['page'] ?? 1;

// 整数に変換（セキュリティ対策）
$current_page = (int)$current_page;

// 不正な値のチェック
if ($current_page < 1) {
    $current_page = 1;
}

// 1ページあたりの件数
$per_page = 10;
?>
```

### ステップ2: OFFSET を計算

```php
<?php
// OFFSET を計算
// 例：3ページ目 → (3 - 1) × 10 = 20
$offset = ($current_page - 1) * $per_page;
?>
```

### ステップ3: データを取得（LIMIT + OFFSET）

```php
<?php
try {
    // データを取得
    $stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");

    // 整数型でバインド（重要！）
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("エラーが発生しました。");
}
?>
```

**重要**：`bindValue()`で`PDO::PARAM_INT`を指定する理由

```php
// ❌ 悪い例（文字列として扱われる）
$stmt->execute([':limit' => $per_page, ':offset' => $offset]);

// ✅ 良い例（整数として扱われる）
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
```

### ステップ4: データを表示

```php
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>記事一覧 - <?php echo htmlspecialchars($current_page, ENT_QUOTES, 'UTF-8'); ?>ページ目</title>
</head>
<body>
    <h1>記事一覧</h1>

    <?php if (count($articles) > 0): ?>
        <?php foreach ($articles as $article): ?>
            <article>
                <h2><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?php echo htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><small><?php echo htmlspecialchars($article['created_at'], ENT_QUOTES, 'UTF-8'); ?></small></p>
            </article>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>記事がありません。</p>
    <?php endif; ?>

    <!-- ページネーションリンク（次のステップで実装） -->
</body>
</html>
```

---

## 💻 総ページ数の計算（How）

### 総件数を取得

```php
<?php
try {
    // 総件数を取得
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles");
    $stmt->execute();
    $total_count = $stmt->fetchColumn();

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("エラーが発生しました。");
}
?>
```

### 総ページ数を計算

```php
<?php
// 総ページ数を計算
// ceil() = 切り上げ
// 例：105件 ÷ 10件/ページ = 10.5 → 11ページ
$total_pages = ceil($total_count / $per_page);

// データが0件の場合
if ($total_pages == 0) {
    $total_pages = 1;
}
?>
```

### 現在のページ数が総ページ数を超えていないかチェック

```php
<?php
// ページ番号が総ページ数を超えている場合、最終ページにリダイレクト
if ($current_page > $total_pages) {
    header("Location: ?page={$total_pages}");
    exit;
}
?>
```

### 情報を表示

```php
<p>
    全<?php echo htmlspecialchars($total_count, ENT_QUOTES, 'UTF-8'); ?>件 /
    <?php echo htmlspecialchars($total_pages, ENT_QUOTES, 'UTF-8'); ?>ページ中
    <?php echo htmlspecialchars($current_page, ENT_QUOTES, 'UTF-8'); ?>ページ目
</p>
```

---

## 💻 ページネーションリンクの実装（How）

### 基本的なページネーションリンク

```php
<div class="pagination">
    <!-- 前へボタン -->
    <?php if ($current_page > 1): ?>
        <a href="?page=<?php echo $current_page - 1; ?>">« 前へ</a>
    <?php else: ?>
        <span class="disabled">« 前へ</span>
    <?php endif; ?>

    <!-- ページ番号リンク -->
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <?php if ($i == $current_page): ?>
            <strong><?php echo $i; ?></strong>
        <?php else: ?>
            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <!-- 次へボタン -->
    <?php if ($current_page < $total_pages): ?>
        <a href="?page=<?php echo $current_page + 1; ?>">次へ »</a>
    <?php else: ?>
        <span class="disabled">次へ »</span>
    <?php endif; ?>
</div>
```

**CSSスタイル例**：

```css
.pagination {
    display: flex;
    gap: 5px;
    margin: 20px 0;
}

.pagination a {
    padding: 8px 12px;
    border: 1px solid #ddd;
    text-decoration: none;
    color: #333;
}

.pagination a:hover {
    background-color: #f0f0f0;
}

.pagination strong {
    padding: 8px 12px;
    border: 1px solid #007bff;
    background-color: #007bff;
    color: white;
}

.pagination .disabled {
    padding: 8px 12px;
    border: 1px solid #ddd;
    color: #ccc;
    cursor: not-allowed;
}
```

### ページ番号リンクの最適化（ページ数が多い場合）

**問題**：ページ数が100ページある場合、すべてのリンクを表示すると見づらい

**解決策**：現在のページ周辺のページ番号だけ表示

```php
<?php
// 表示するページ数の範囲
$range = 2;  // 現在のページの前後2ページずつ

// 開始ページと終了ページを計算
$start_page = max(1, $current_page - $range);
$end_page = min($total_pages, $current_page + $range);
?>

<div class="pagination">
    <!-- 前へボタン -->
    <?php if ($current_page > 1): ?>
        <a href="?page=<?php echo $current_page - 1; ?>">« 前へ</a>
    <?php else: ?>
        <span class="disabled">« 前へ</span>
    <?php endif; ?>

    <!-- 最初のページへのリンク -->
    <?php if ($start_page > 1): ?>
        <a href="?page=1">1</a>
        <?php if ($start_page > 2): ?>
            <span>...</span>
        <?php endif; ?>
    <?php endif; ?>

    <!-- ページ番号リンク（現在のページ周辺のみ） -->
    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
        <?php if ($i == $current_page): ?>
            <strong><?php echo $i; ?></strong>
        <?php else: ?>
            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <!-- 最後のページへのリンク -->
    <?php if ($end_page < $total_pages): ?>
        <?php if ($end_page < $total_pages - 1): ?>
            <span>...</span>
        <?php endif; ?>
        <a href="?page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
    <?php endif; ?>

    <!-- 次へボタン -->
    <?php if ($current_page < $total_pages): ?>
        <a href="?page=<?php echo $current_page + 1; ?>">次へ »</a>
    <?php else: ?>
        <span class="disabled">次へ »</span>
    <?php endif; ?>
</div>
```

**表示例**：

```text
現在3ページ目（全100ページ）の場合：
« 前へ  [1] ... [2] [3] [4] ... [100]  次へ »

現在50ページ目（全100ページ）の場合：
« 前へ  [1] ... [48] [49] [50] [51] [52] ... [100]  次へ »
```

---

## 💻 検索結果のページネーション（How）

### 検索 + ページネーションの実装

**課題**：検索キーワードを保持しながらページネーション

```php
<?php
require_once 'config.php';

// 検索キーワードを取得
$keyword = $_GET['keyword'] ?? '';

// ページ番号を取得
$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;

if ($current_page < 1) {
    $current_page = 1;
}

$per_page = 10;
$offset = ($current_page - 1) * $per_page;

try {
    if (!empty($keyword)) {
        // 検索クエリ + ページネーション
        // 総件数を取得
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE title LIKE :keyword OR content LIKE :keyword");
        $stmt->execute([':keyword' => '%' . $keyword . '%']);
        $total_count = $stmt->fetchColumn();

        // データを取得
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE title LIKE :keyword OR content LIKE :keyword ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } else {
        // すべて取得 + ページネーション
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles");
        $stmt->execute();
        $total_count = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $total_pages = ceil($total_count / $per_page);

    if ($total_pages == 0) {
        $total_pages = 1;
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("エラーが発生しました。");
}
?>

<!-- 検索フォーム -->
<form method="GET" action="">
    <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>" placeholder="検索キーワード">
    <button type="submit">検索</button>
</form>

<?php if (!empty($keyword)): ?>
    <p>「<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>」の検索結果：<?php echo $total_count; ?>件</p>
<?php endif; ?>

<!-- データ表示 -->
<?php foreach ($articles as $article): ?>
    <article>
        <h2><?php echo htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
        <p><?php echo htmlspecialchars($article['content'], ENT_QUOTES, 'UTF-8'); ?></p>
    </article>
<?php endforeach; ?>

<!-- ページネーションリンク（検索キーワードを保持） -->
<div class="pagination">
    <?php if ($current_page > 1): ?>
        <a href="?page=<?php echo $current_page - 1; ?>&keyword=<?php echo urlencode($keyword); ?>">« 前へ</a>
    <?php else: ?>
        <span class="disabled">« 前へ</span>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <?php if ($i == $current_page): ?>
            <strong><?php echo $i; ?></strong>
        <?php else: ?>
            <a href="?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword); ?>"><?php echo $i; ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($current_page < $total_pages): ?>
        <a href="?page=<?php echo $current_page + 1; ?>&keyword=<?php echo urlencode($keyword); ?>">次へ »</a>
    <?php else: ?>
        <span class="disabled">次へ »</span>
    <?php endif; ?>
</div>
```

**重要**：`urlencode()`を使ってURLエンコード

```php
// ✅ 良い例（URLエンコード）
<a href="?page=2&keyword=<?php echo urlencode($keyword); ?>">

// ❌ 悪い例（スペースや特殊文字が含まれると壊れる）
<a href="?page=2&keyword=<?php echo $keyword; ?>">
```

---

## 💻 カテゴリフィルター + ページネーション（How）

### 複数パラメータの管理

```php
<?php
require_once 'config.php';

// カテゴリを取得
$category = $_GET['category'] ?? '';

// ページ番号を取得
$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;

if ($current_page < 1) {
    $current_page = 1;
}

$per_page = 10;
$offset = ($current_page - 1) * $per_page;

try {
    if (!empty($category)) {
        // カテゴリでフィルター + ページネーション
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE category = :category");
        $stmt->execute([':category' => $category]);
        $total_count = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT * FROM articles WHERE category = :category ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':category', $category, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } else {
        // すべて取得
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles");
        $stmt->execute();
        $total_count = $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $total_pages = ceil($total_count / $per_page);

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("エラーが発生しました。");
}

// ページネーションURLを生成するヘルパー関数
function buildPaginationUrl($page, $category) {
    $params = ['page' => $page];
    if (!empty($category)) {
        $params['category'] = $category;
    }
    return '?' . http_build_query($params);
}
?>

<!-- カテゴリフィルター -->
<ul>
    <li><a href="?">すべて</a></li>
    <li><a href="?category=テクノロジー">テクノロジー</a></li>
    <li><a href="?category=ライフスタイル">ライフスタイル</a></li>
    <li><a href="?category=ビジネス">ビジネス</a></li>
</ul>

<!-- ページネーションリンク -->
<div class="pagination">
    <?php if ($current_page > 1): ?>
        <a href="<?php echo buildPaginationUrl($current_page - 1, $category); ?>">« 前へ</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <?php if ($i == $current_page): ?>
            <strong><?php echo $i; ?></strong>
        <?php else: ?>
            <a href="<?php echo buildPaginationUrl($i, $category); ?>"><?php echo $i; ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($current_page < $total_pages): ?>
        <a href="<?php echo buildPaginationUrl($current_page + 1, $category); ?>">次へ »</a>
    <?php endif; ?>
</div>
```

**ヘルパー関数**の利点：

- パラメータの管理が簡単
- URLの構築が一箇所にまとまる
- バグが減る

---

## 💡 パフォーマンス最適化

### OFFSET の問題点

**問題**：OFFSETが大きくなると、パフォーマンスが悪化する

```sql
-- 1ページ目（OFFSET 0）：速い
SELECT * FROM articles ORDER BY created_at DESC LIMIT 10 OFFSET 0;

-- 1,000ページ目（OFFSET 9990）：遅い！
SELECT * FROM articles ORDER BY created_at DESC LIMIT 10 OFFSET 9990;
```

**理由**：データベースは最初の9,990件を読み飛ばす必要がある

### 解決策1: インデックスを使う

```sql
-- created_atカラムにインデックスを作成
CREATE INDEX idx_created_at ON articles(created_at);

-- ORDER BYが高速化される
SELECT * FROM articles ORDER BY created_at DESC LIMIT 10 OFFSET 9990;
```

### 解決策2: キーセットページネーション（Cursor-based Pagination）

**OFFSET を使わない方法**：

```php
<?php
// 最後に表示した記事のIDを取得
$last_id = $_GET['last_id'] ?? 0;

// WHERE句で絞り込み
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id < :last_id ORDER BY id DESC LIMIT :limit");
$stmt->bindValue(':last_id', $last_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 次のページへのリンク
if (count($articles) > 0) {
    $last_article = end($articles);
    echo '<a href="?last_id=' . $last_article['id'] . '">次へ »</a>';
}
?>
```

**メリット**：

- OFFSETを使わないので、ページ数が多くても高速
- データの追加・削除があっても、重複や欠落が起こりにくい

**デメリット**：

- ページ番号を直接指定できない
- 「3ページ目に飛ぶ」ができない

---

## 🔒 セキュリティ対策

### 1. ページ番号のバリデーション

```php
<?php
// ❌ 悪い例（バリデーションなし）
$current_page = $_GET['page'];
$offset = ($current_page - 1) * $per_page;  // SQLエラーの可能性

// ✅ 良い例（バリデーションあり）
$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;  // 整数に変換

if ($current_page < 1) {
    $current_page = 1;
}

if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}
?>
```

### 2. SQLインジェクション対策

```php
<?php
// ✅ プリペアドステートメント + PDO::PARAM_INT
$stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
?>
```

### 3. XSS対策

```php
<!-- ✅ すべての出力でhtmlspecialchars() -->
<a href="?page=<?php echo htmlspecialchars($i, ENT_QUOTES, 'UTF-8'); ?>">
    <?php echo htmlspecialchars($i, ENT_QUOTES, 'UTF-8'); ?>
</a>
```

### 4. URLパラメータのエンコード

```php
<!-- ✅ urlencode()を使う -->
<a href="?page=2&keyword=<?php echo urlencode($keyword); ?>">
```

---

## 🤖 バイブコーディング実践

### AIへの指示例

**良い指示の例**：

```text
✅ 具体的で詳細な指示：

「記事一覧ページにページネーション機能を実装してください。

機能要件：
1. 1ページ10件表示
2. ページ番号をURLパラメータ（?page=1）で取得
3. LIMIT + OFFSETでデータを取得
4. 総ページ数を計算して表示
5. ページネーションリンク（前へ・次へ・ページ番号）

セキュリティ要件：
1. ページ番号のバリデーション（整数チェック、範囲チェック）
2. プリペアドステートメント使用
3. PDO::PARAM_INTでLIMIT、OFFSETをバインド
4. XSS対策（htmlspecialchars）

ユーザビリティ：
1. 現在のページを強調表示
2. 1ページ目では「前へ」ボタンを無効化
3. 最終ページでは「次へ」ボタンを無効化
4. 「全○件 / ○ページ中○ページ目」と表示

テーブル構成：
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);」
```

```text
❌ 曖昧な指示（問題あり）：

「ページネーション機能を追加して」

問題点：
- 1ページあたりの件数が不明
- セキュリティ要件がない
- UIの詳細が不明
```

### 生成されたコードのチェックポイント

✅ **機能チェック**

- [ ] `LIMIT`と`OFFSET`を使ってデータを取得しているか
- [ ] `COUNT(*)`で総件数を取得しているか
- [ ] `ceil()`で総ページ数を計算しているか
- [ ] ページ番号のバリデーションがあるか

✅ **セキュリティチェック**

- [ ] ページ番号を`(int)`で整数に変換しているか
- [ ] プリペアドステートメント（`prepare()` → `execute()`）を使っているか
- [ ] `bindValue()`で`PDO::PARAM_INT`を指定しているか
- [ ] `htmlspecialchars()`で出力をエスケープしているか

✅ **ユーザビリティチェック**

- [ ] 現在のページが強調表示されているか
- [ ] 「前へ」「次へ」ボタンが適切に無効化されているか
- [ ] 総件数・総ページ数・現在のページが表示されているか

✅ **パフォーマンスチェック**

- [ ] `ORDER BY`に使うカラムにインデックスがあるか（推奨）
- [ ] 不要なカラムを取得していないか（SELECT *より具体的に指定）

### よくあるAI生成コードの問題と修正

**問題1: PDO::PARAM_INTを指定していない**

```php
// ❌ 悪い例（文字列として扱われる可能性）
$stmt->execute([':limit' => $per_page, ':offset' => $offset]);
```

**修正**：

```php
// ✅ 良い例（整数として扱われる）
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
```

**AIへの修正指示**：
「LIMITとOFFSETのバインド時に、PDO::PARAM_INTを指定してください」

---

**問題2: ページ番号のバリデーションがない**

```php
// ❌ 悪い例（バリデーションなし）
$current_page = $_GET['page'];
```

**修正**：

```php
// ✅ 良い例（バリデーションあり）
$current_page = $_GET['page'] ?? 1;
$current_page = (int)$current_page;

if ($current_page < 1) {
    $current_page = 1;
}
```

**AIへの修正指示**：
「ページ番号のバリデーションを追加してください。整数に変換し、1未満の場合は1に設定してください」

---

**問題3: 総ページ数のチェックがない**

```php
// ❌ 悪い例（ページ番号が総ページ数を超える可能性）
$current_page = $_GET['page'];
```

**修正**：

```php
// ✅ 良い例（総ページ数を超えていないかチェック）
$total_pages = ceil($total_count / $per_page);

if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}
```

**AIへの修正指示**：
「ページ番号が総ページ数を超えている場合、最終ページにリダイレクトするようにしてください」

---

## 🎓 まとめ

このレッスンで学んだこと：

✅ **ページネーションの基礎**

- ページネーションの仕組み（LIMIT + OFFSET）
- 総ページ数の計算（COUNT() + ceil()）
- ページネーションリンクの実装

✅ **実践的な実装**

- 検索 + ページネーション
- カテゴリフィルター + ページネーション
- 複数パラメータの管理（ヘルパー関数）

✅ **パフォーマンス最適化**

- OFFSETの問題点
- インデックスの活用
- キーセットページネーション（Cursor-based Pagination）

✅ **セキュリティ対策**

- ページ番号のバリデーション
- SQLインジェクション対策（プリペアドステートメント + PDO::PARAM_INT）
- XSS対策（htmlspecialchars）
- URLエンコード（urlencode）

✅ **バイブコーディング**

- AIに具体的な要件を伝える方法
- 生成されたコードのチェックポイント
- よくある問題の発見と修正方法

### 次のステップ

Phase 5のすべてのレッスンが完了！おめでとう！🎉

次は実践プロジェクトに挑戦しよう！

👉 **[Phase 5 プロジェクト: ブログシステム](../../projects/blog-system/README.md)**

これまで学んだすべての技術を組み合わせて、実用的なブログシステムを構築しよう！

👉 **[演習問題を見る](exercises/README.md)**

実際にページネーション機能を実装して、大量データの扱い方をマスターしよう！

---

**Let's vibe and code! 🎉**

ページネーションができれば、大量データを扱うアプリケーションも怖くない！次は実践プロジェクトで、これまで学んだすべてのスキルを総動員しよう！
