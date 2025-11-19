# 総合プロジェクト1: ブログシステム 📝

**プロジェクト概要**：セキュリティを重視した本格的なブログシステムを構築する！

このプロジェクトは、**ユーザー認証とバックエンドセキュリティに重点を置いた**ブログシステムの実装例です。フロントエンドのスタイリングは最低限に抑え、堅牢なバックエンド実装とセキュアな認証機能に注力しています。

## 🎯 実装済み機能

### ユーザー認証機能

- ✅ **ユーザー登録**（register.php）：パスワードハッシュ化、バリデーション
- ✅ **ログイン**（login.php）：パスワード検証、セッション管理
- ✅ **ログアウト**（logout.php）：セッション破棄

### 記事管理機能（CRUD）

- ✅ **記事投稿**（add.php）：タイトル、内容、カテゴリ、公開/下書き
- ✅ **記事一覧**（index.php）：ページング、フィルタリング、検索
- ✅ **記事詳細**（view.php）：Markdown表示、閲覧数カウント
- ✅ **記事編集**（edit.php）：作者のみ編集可能
- ✅ **記事削除**（delete.php）：確認画面、作者のみ削除可能

### 検索・フィルタリング

- カテゴリフィルタ
- キーワード検索（タイトル・内容）
- 自分の記事フィルタ
- ページング（1ページ10件）

### UI機能

- 公開/下書きステータス管理
- カテゴリ別表示
- 閲覧数の表示
- 相対日時表示（「1時間前」など）
- Markdown対応（見出し、コードブロック、改行）

## 🔒 セキュリティ対策

このプロジェクトは**セキュリティファースト**で設計されています：

### 1. SQLインジェクション対策 ✅

- **すべてのデータベースクエリでPDOプリペアドステートメントを使用**
- パラメータバインディング（`:placeholder`）による安全なデータ挿入
- `PDO::ATTR_EMULATE_PREPARES => false` で真のプリペアドステートメントを強制

```php
// 安全な実装例（functions.php）
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
```

### 2. XSS（クロスサイトスクリプティング）対策 ✅

- **すべての出力で `htmlspecialchars()` を使用**
- `h()` ヘルパー関数による一貫したエスケープ処理
- `ENT_QUOTES` でシングル・ダブルクォート両方をエスケープ

```php
// 安全な出力例
<h1><?php echo h($post['title']); ?></h1>
```

### 3. CSRF（クロスサイトリクエストフォージェリ）対策 ✅

- セッションベースのCSRFトークン生成
- すべての状態変更操作（POST）でトークン検証
- 成功時にトークン再生成（トークンリプレイ攻撃防止）
- `hash_equals()` によるタイミング攻撃対策

```php
// CSRF検証例（config.php）
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
```

### 4. パスワード保護 ✅

- **`password_hash()` による強力なハッシュ化**（bcryptアルゴリズム）
- **`password_verify()` による安全な検証**
- パスワードは決して平文で保存しない
- 最小8文字の長さチェック

```php
// パスワードハッシュ化（auth_functions.php）
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// パスワード検証
if (!password_verify($password, $user['password_hash'])) {
    // 認証失敗
}
```

### 5. セッションセキュリティ ✅

- `session_regenerate_id(true)` でセッション固定化攻撃を防止
- `session.cookie_httponly = 1`（JavaScriptからのアクセスを防ぐ）
- `session.use_only_cookies = 1`（URLでのセッションID送信を禁止）
- `session.cookie_samesite = Strict`（CSRF追加防御）

### 6. 認可チェック ✅

- 記事の編集・削除は作者のみ可能
- 下書き記事は作者のみ閲覧可能
- すべての操作で権限チェック実施

### 7. 入力バリデーション ✅

- **ホワイトリスト方式**によるステータス検証
- メールアドレスの形式チェック（`filter_var()`）
- タイトル・内容の長さ制限
- 空値チェックとトリミング

### 8. エラーハンドリング ✅

- データベースエラーの詳細をユーザーに見せない
- エラーログへの記録（`error_log()`）
- ユーザーフレンドリーなエラーメッセージ

## 📁 ファイル構成

```text
blog-system/
├── database.sql           # データベーススキーマとサンプルデータ
├── config.php             # DB接続設定、CSRF関数、セキュリティ設定
├── auth_functions.php     # 認証関連関数（登録、ログイン、権限チェック）
├── functions.php          # 記事関連関数、カテゴリ、ヘルパー関数
├── register.php           # ユーザー登録フォーム
├── login.php              # ログインフォーム
├── logout.php             # ログアウト処理
├── index.php              # 記事一覧・検索・フィルタ
├── view.php               # 記事詳細表示
├── add.php                # 記事投稿フォーム
├── edit.php               # 記事編集フォーム
├── delete.php             # 記事削除処理
└── README.md              # このファイル
```

### 各ファイルの役割

| ファイル | 役割 | 主な機能 |
|---------|------|---------|
| **database.sql** | データベース定義 | テーブル作成、インデックス、サンプルデータ |
| **config.php** | 設定・基盤 | DB接続、セッション設定、CSRF関数 |
| **auth_functions.php** | 認証 | ユーザー登録、ログイン、権限チェック |
| **functions.php** | ビジネスロジック | 記事CRUD、カテゴリ、ヘルパー関数 |
| **register.php** | ユーザー登録 | 登録フォームと処理 |
| **login.php** | ログイン | ログインフォームと処理 |
| **logout.php** | ログアウト | セッション破棄 |
| **index.php** | メイン画面 | 記事一覧、検索、フィルタ、ページング |
| **view.php** | 記事詳細 | Markdown表示、閲覧数カウント |
| **add.php** | 記事作成 | 投稿フォームと処理 |
| **edit.php** | 記事更新 | 編集フォームと処理 |
| **delete.php** | 記事削除 | 確認画面と削除処理 |

## 🚀 セットアップ手順

### 1. データベースの作成

MAMPのphpMyAdminを開き、以下の手順で実行：

1. phpMyAdmin にアクセス（`http://localhost:8888/phpMyAdmin/`）
2. 「SQL」タブをクリック
3. `database.sql` の内容をすべてコピー＆ペースト
4. 「実行」をクリック

または、ターミナルから：

```bash
mysql -u root -proot -P 8889 -h localhost < database.sql
```

### 2. ファイルの配置

すべてのPHPファイルをMAMPのドキュメントルートに配置：

```bash
/Applications/MAMP/htdocs/blog-system/
```

### 3. 設定の確認

`config.php` のデータベース接続情報を確認：

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '8889');      // MAMPのデフォルトポート
define('DB_NAME', 'blog_system');
define('DB_USER', 'root');
define('DB_PASS', 'root');      // MAMPのデフォルトパスワード
```

### 4. アクセス

ブラウザで以下にアクセス：

```text
http://localhost:8888/blog-system/
```

### 5. テストアカウント

ログインには以下のテストアカウントを使用できます：

- **メール**: `yamada@example.com`
- **パスワード**: `password123`

または新規登録して独自のアカウントを作成してください。

## 📊 データベース設計

### users テーブル

| カラム | 型 | 説明 | 制約 |
|--------|-----|------|------|
| id | INT | ユーザーID | PRIMARY KEY, AUTO_INCREMENT |
| name | VARCHAR(100) | ユーザー名 | NOT NULL |
| email | VARCHAR(255) | メールアドレス | NOT NULL, UNIQUE |
| password_hash | VARCHAR(255) | パスワードハッシュ | NOT NULL |
| created_at | DATETIME | 登録日時 | DEFAULT CURRENT_TIMESTAMP |
| updated_at | DATETIME | 更新日時 | ON UPDATE CURRENT_TIMESTAMP |

### categories テーブル

| カラム | 型 | 説明 | 制約 |
|--------|-----|------|------|
| id | INT | カテゴリID | PRIMARY KEY, AUTO_INCREMENT |
| name | VARCHAR(100) | カテゴリ名 | NOT NULL |
| slug | VARCHAR(100) | URL用スラッグ | NOT NULL, UNIQUE |
| description | TEXT | 説明 | NULL許可 |
| created_at | DATETIME | 作成日時 | DEFAULT CURRENT_TIMESTAMP |
| updated_at | DATETIME | 更新日時 | ON UPDATE CURRENT_TIMESTAMP |

### posts テーブル

| カラム | 型 | 説明 | 制約 |
|--------|-----|------|------|
| id | INT | 記事ID | PRIMARY KEY, AUTO_INCREMENT |
| user_id | INT | 作者ID | NOT NULL, FOREIGN KEY |
| category_id | INT | カテゴリID | NULL許可, FOREIGN KEY |
| title | VARCHAR(255) | タイトル | NOT NULL |
| content | TEXT | 本文 | NOT NULL |
| excerpt | VARCHAR(500) | 抜粋 | NULL許可 |
| status | ENUM | ステータス | 'draft', 'published' (デフォルト: 'draft') |
| view_count | INT | 閲覧数 | DEFAULT 0 |
| created_at | DATETIME | 作成日時 | DEFAULT CURRENT_TIMESTAMP |
| updated_at | DATETIME | 更新日時 | ON UPDATE CURRENT_TIMESTAMP |
| published_at | DATETIME | 公開日時 | NULL許可 |

### インデックス

パフォーマンス最適化のため、以下のインデックスを設定：

- **users**: `idx_email`, `idx_created_at`
- **categories**: `idx_slug`
- **posts**: `idx_user_id`, `idx_category_id`, `idx_status`, `idx_created_at`, `idx_published_at`

## 💻 使い方

### ユーザー登録

1. トップページの「新規登録」をクリック
2. 名前、メールアドレス、パスワード（8文字以上）を入力
3. 「登録」ボタンをクリック

### ログイン

1. トップページの「ログイン」をクリック
2. メールアドレスとパスワードを入力
3. 「ログイン」ボタンをクリック

### 記事を書く

1. ログイン後、「記事を書く」をクリック
2. タイトル、内容、カテゴリを入力
3. Markdown形式で記述可能（見出し: `#`, `##`, `###`, コードブロック: ` ``` `）
4. 「公開する」または「下書き保存」をクリック

### 記事の検索・フィルタ

- **カテゴリフィルタ**：特定のカテゴリの記事のみ表示
- **キーワード検索**：タイトルや内容に含まれる単語で検索
- **自分の記事**：自分が書いた記事のみ表示

### 記事の編集・削除

- 自分が書いた記事のみ編集・削除可能
- 記事詳細ページまたは一覧ページから操作可能

## 🧪 主要機能の実装詳細

### ユーザー認証フロー

```php
// 1. ユーザー登録（register.php + auth_functions.php）
$password_hash = password_hash($password, PASSWORD_DEFAULT);
INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)

// 2. ログイン（login.php + auth_functions.php）
SELECT * FROM users WHERE email = ?
if (password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    session_regenerate_id(true); // セッション固定化攻撃対策
}

// 3. ログイン状態チェック（functions.php）
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
```

### 記事の権限チェック

```php
// 記事編集・削除は作者のみ可能
if (!isCurrentUser($post['user_id'])) {
    setFlashMessage('error', 'この記事を編集する権限がありません。');
    redirect('index.php');
}

// 下書き記事は作者のみ閲覧可能
if ($post['status'] === 'draft' && (!isLoggedIn() || !isCurrentUser($post['user_id']))) {
    setFlashMessage('error', 'この記事は閲覧できません。');
    redirect('index.php');
}
```

### 動的フィルタリング（functions.php: getPosts()）

```php
function getPosts($filters = [], $page = 1, $per_page = POSTS_PER_PAGE) {
    $where_conditions = [];
    $bind_params = [];

    // 未ログインユーザーには公開記事のみ
    if (!isLoggedIn()) {
        $where_conditions[] = 'p.status = :status';
        $bind_params[':status'] = 'published';
    }

    // カテゴリフィルタ
    if (isset($filters['category_id'])) {
        $where_conditions[] = 'p.category_id = :category_id';
        $bind_params[':category_id'] = $filters['category_id'];
    }

    // キーワード検索
    if (!empty($filters['keyword'])) {
        $where_conditions[] = '(p.title LIKE :keyword OR p.content LIKE :keyword)';
        $bind_params[':keyword'] = '%' . $filters['keyword'] . '%';
    }

    // ... ページング処理とクエリ実行
}
```

## 🎓 バイブコーディング実践ポイント

### AIへの指示例

**良い指示（セキュリティ要件を含む）**：

```text
「PHPでユーザーログイン機能を実装してください。
要件:
- メールアドレスとパスワードで認証
- パスワードはpassword_verify()で検証
- ログイン成功時はsession_regenerate_id()でセッション固定化攻撃を防ぐ
- SQLインジェクション対策としてPDOプリペアドステートメントを使用
- CSRF対策としてトークンを検証」
```

**悪い指示（セキュリティ指示なし）**：

```text
「ログイン機能を作って」
```

### 生成コードのチェックリスト

#### 🔒 セキュリティチェック（最優先）

**認証関連**

- [ ] パスワードは`password_hash()`でハッシュ化
- [ ] パスワード検証は`password_verify()`使用
- [ ] ログイン成功時に`session_regenerate_id(true)`
- [ ] セッションにユーザー情報を安全に保存

**データベース操作**

- [ ] プリペアドステートメント使用（直接SQL埋め込みなし）
- [ ] パラメータバインディング（`:placeholder`）
- [ ] `PDO::PARAM_INT`、`PDO::PARAM_STR`の適切な使用

**出力・入力**

- [ ] 出力時の`htmlspecialchars()`使用
- [ ] CSRF トークン検証（POST操作）
- [ ] 入力値のバリデーション

**権限チェック**

- [ ] ログイン必須ページで`requireLogin()`
- [ ] 編集・削除時に作者チェック
- [ ] 下書き記事の閲覧制限

#### ✅ 機能チェック

- [ ] エラーハンドリング（try-catch）
- [ ] NULL値の適切な処理
- [ ] リダイレクト後のexit()
- [ ] フラッシュメッセージでユーザーフィードバック

#### 📝 コード品質チェック

- [ ] 意味のある変数名
- [ ] 適切なコメント
- [ ] DRY原則（重複排除）

### よくある問題と修正方法

#### ❌ 問題1：パスワードの平文保存

```php
// 危険なコード
$sql = "INSERT INTO users (email, password) VALUES (?, ?)";
$stmt->execute([$email, $password]); // 平文で保存！
```

**修正**：

```php
// 安全なコード
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$sql = "INSERT INTO users (email, password_hash) VALUES (?, ?)";
$stmt->execute([$email, $password_hash]);
```

#### ❌ 問題2：セッション固定化攻撃の脆弱性

```php
// 危険なコード
$_SESSION['user_id'] = $user['id'];
// セッションIDが変わらない！
```

**修正**：

```php
// 安全なコード
$_SESSION['user_id'] = $user['id'];
session_regenerate_id(true); // セッションIDを再生成
```

#### ❌ 問題3：権限チェックの欠如

```php
// 危険なコード（誰でも他人の記事を編集可能）
$post_id = $_GET['id'];
$post = getPostById($post_id);
// 権限チェックなし！
updatePost($post_id, $_POST);
```

**修正**：

```php
// 安全なコード
$post = getPostById($post_id);
if (!isCurrentUser($post['user_id'])) {
    setFlashMessage('error', 'この記事を編集する権限がありません。');
    redirect('index.php');
}
updatePost($post_id, $_POST);
```

## 🌟 カスタマイズポイント

### 1ページあたりの記事数を変更

`config.php` 28行目：

```php
define('POSTS_PER_PAGE', 10);  // 10を任意の数値に変更
```

### Markdown変換の拡張

`view.php` の `simpleMarkdown()` 関数を拡張：

```php
// 太字変換を追加
$text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);

// リンク変換を追加
$text = preg_replace('/\[(.+?)\]\((.+?)\)/', '<a href="$2">$1</a>', $text);
```

### カテゴリの追加

database.sqlにカテゴリを追加：

```sql
INSERT INTO categories (name, slug, description) VALUES
('新カテゴリ', 'new-category', '説明文');
```

## 📚 学習のポイント

このプロジェクトで学べること：

1. **セキュアな認証実装**：パスワードハッシュ化、セッション管理
2. **権限管理**：ユーザーごとの操作制限
3. **PDOの使い方**：プリペアドステートメント、パラメータバインディング
4. **セッション管理**：CSRF対策、セッション固定化攻撃対策
5. **動的SQL構築**：フィルタリング、JOIN、ページング
6. **バリデーション**：メールアドレス、パスワード強度、入力値検証
7. **エラーハンドリング**：try-catch、ユーザーフレンドリーなメッセージ
8. **コード設計**：関数の分割、責務の分離、再利用性

## ⚠️ 本番環境への展開前チェックリスト

- [ ] `config.php` のエラー表示を無効化（`display_errors = 0`）
- [ ] データベース認証情報を環境変数化
- [ ] HTTPS環境で `session.cookie_secure = 1` に設定
- [ ] エラーログの保存先を確認
- [ ] データベースバックアップ体制の確立
- [ ] アクセスログの設定
- [ ] メール通知機能の実装（パスワードリセットなど）
- [ ] CAPTCHA導入（スパム対策）

## 🎉 まとめ

このブログシステムは、**セキュリティとユーザー認証を最優先**にしながら、実用的な機能を実装した教材です。

**重要なポイント**：

- すべてのパスワードはハッシュ化（password_hash）
- すべてのセッション操作は安全に（session_regenerate_id）
- すべてのデータベース操作はプリペアドステートメント
- すべての出力はエスケープ
- すべての状態変更操作はCSRF保護
- すべての操作で権限チェック

**バイブコーダーとして**：

- AI に明確な指示を出す（セキュリティ要件を含める）
- 生成されたコードを必ずセキュリティチェック
- 特に認証周りの脆弱性に注意
- 権限チェックの漏れがないか確認

**Let's vibe and code securely! 🔒🎉**
