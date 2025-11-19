# 総合プロジェクト2: タスク管理システム ✅

**プロジェクト概要**：セキュリティを重視した実用的なタスク管理システム（ToDoリスト）を構築する！

このプロジェクトは、**バックエンド機能とセキュリティに重点を置いた**タスク管理システムの実装例です。フロントエンドのスタイリングは最低限に抑え、堅牢なバックエンド実装に注力しています。

## 🎯 実装済み機能

### CRUD操作

- ✅ **タスク追加**（add.php）：タイトル、説明、優先度、ステータス、期限日
- ✅ **タスク一覧**（index.php）：ページング、フィルタリング、検索
- ✅ **タスク編集**（edit.php）：既存タスクの更新
- ✅ **タスク削除**（delete.php）：確認ダイアログ付き削除
- ✅ **ステータス切り替え**（toggle.php）：未完了⇔完了のワンクリック切り替え

### 検索・フィルタリング

- キーワード検索（タイトル・説明から検索）
- ステータスフィルタ（未完了/完了）
- 優先度フィルタ（低/中/高）
- ページング（1ページ10件、省略表示あり）

### UI機能

- 優先度による色分け表示（高=赤、中=黄、低=緑）
- 期限日の警告表示（超過=赤、3日以内=黄）
- フラッシュメッセージによるフィードバック
- フォーム入力値の保持（エラー時）

## 🔒 セキュリティ対策

このプロジェクトは**セキュリティファースト**で設計されています：

### 1. SQLインジェクション対策 ✅

- **すべてのデータベースクエリでPDOプリペアドステートメントを使用**
- パラメータバインディング（`:placeholder`）による安全なデータ挿入
- `PDO::ATTR_EMULATE_PREPARES => false` で真のプリペアドステートメントを強制

```php
// 安全な実装例（functions.php）
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
```

### 2. XSS（クロスサイトスクリプティング）対策 ✅

- **すべての出力で `htmlspecialchars()` を使用**
- `h()` ヘルパー関数による一貫したエスケープ処理
- `ENT_QUOTES` でシングル・ダブルクォート両方をエスケープ

```php
// 安全な出力例
<div class="task-title"><?php echo h($task['title']); ?></div>
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

### 4. 入力バリデーション ✅

- **ホワイトリスト方式**による優先度・ステータスの検証
- タイトル長の制限（255文字）
- 日付フォーマットの厳密な検証
- 空値チェックとトリミング

```php
// バリデーション例（functions.php）
function validatePriority($priority) {
    $allowed = ['low', 'medium', 'high'];
    return in_array($priority, $allowed, true);
}
```

### 5. セッションセキュリティ ✅

- `session.cookie_httponly = 1`（JavaScript からのアクセス防止）
- `session.use_only_cookies = 1`（URL でのセッションID送信を禁止）
- `session.cookie_samesite = Strict`（CSRF 追加防御）

### 6. エラーハンドリング ✅

- データベースエラーの詳細をユーザーに見せない
- エラーログへの記録（`error_log()`）
- ユーザーフレンドリーなエラーメッセージ

## 📁 ファイル構成

```text
task-manager/
├── database.sql        # データベーススキーマとサンプルデータ
├── config.php          # DB接続設定、CSRF関数、セキュリティ設定
├── functions.php       # ビジネスロジック、バリデーション、DB操作
├── index.php           # タスク一覧・検索・フィルタ
├── add.php             # タスク追加フォーム
├── edit.php            # タスク編集フォーム
├── delete.php          # タスク削除処理
├── toggle.php          # ステータス切り替え処理
└── README.md           # このファイル
```

### 各ファイルの役割

| ファイル | 役割 | 主な機能 |
|---------|------|---------|
| **database.sql** | データベース定義 | テーブル作成、インデックス、サンプルデータ |
| **config.php** | 設定・基盤 | DB接続、セッション設定、CSRF関数 |
| **functions.php** | ビジネスロジック | CRUD操作、バリデーション、ヘルパー関数 |
| **index.php** | メイン画面 | 一覧表示、検索、フィルタ、ページング |
| **add.php** | 作成 | タスク追加フォームと処理 |
| **edit.php** | 更新 | タスク編集フォームと処理 |
| **delete.php** | 削除 | タスク削除処理（POST のみ） |
| **toggle.php** | ステータス変更 | 完了/未完了切り替え（POST のみ） |

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
/Applications/MAMP/htdocs/task-manager/
```

### 3. 設定の確認

`config.php` のデータベース接続情報を確認：

```php
define('DB_HOST', 'localhost');
define('DB_PORT', '8889');      // MAMPのデフォルト
define('DB_NAME', 'task_manager');
define('DB_USER', 'root');
define('DB_PASS', 'root');      // MAMPのデフォルト
```

### 4. アクセス

ブラウザで以下にアクセス：

```text
http://localhost:8888/task-manager/
```

## 📊 データベース設計

### tasks テーブル

| カラム | 型 | 説明 | 制約 |
|--------|-----|------|------|
| id | INT | タスクID | PRIMARY KEY, AUTO_INCREMENT |
| title | VARCHAR(255) | タスク名 | NOT NULL |
| description | TEXT | 詳細説明 | NULL許可 |
| priority | ENUM | 優先度 | 'low', 'medium', 'high' (デフォルト: 'medium') |
| status | ENUM | ステータス | 'pending', 'completed' (デフォルト: 'pending') |
| due_date | DATE | 期限日 | NULL許可 |
| created_at | DATETIME | 作成日時 | DEFAULT CURRENT_TIMESTAMP |
| updated_at | DATETIME | 更新日時 | ON UPDATE CURRENT_TIMESTAMP |

### インデックス

パフォーマンス最適化のため、以下のインデックスを設定：

- `idx_status` - ステータスでのフィルタリング高速化
- `idx_priority` - 優先度でのフィルタリング高速化
- `idx_due_date` - 期限日でのソート高速化
- `idx_created_at` - 作成日でのソート高速化

## 💻 使い方

### タスクの追加

1. トップページの「➕ 新規タスク追加」ボタンをクリック
2. タスク名（必須）、説明、優先度、ステータス、期限日を入力
3. 「✅ 追加」ボタンをクリック

### タスクの検索・フィルタ

- **キーワード検索**：タイトルや説明に含まれる単語で検索
- **ステータスフィルタ**：未完了/完了で絞り込み
- **優先度フィルタ**：低/中/高で絞り込み
- フィルタは組み合わせ可能

### タスクの編集

1. 各タスクの「✏️ 編集」ボタンをクリック
2. 情報を修正
3. 「💾 更新」ボタンをクリック

### タスクの完了切り替え

- 「✅ 完了」ボタン：未完了→完了
- 「↩️ 未完了に戻す」ボタン：完了→未完了

### タスクの削除

1. 「🗑️ 削除」ボタンをクリック
2. 確認ダイアログで「OK」をクリック

## 🧪 主要機能の実装詳細

### 動的フィルタリング（functions.php: getTasks()）

```php
function getTasks($filters = [], $page = 1, $per_page = 10) {
    $pdo = getDB();
    $where_conditions = [];
    $bind_params = [];

    // ステータスフィルタ
    if (isset($filters['status']) && validateStatus($filters['status'])) {
        $where_conditions[] = 'status = :status';
        $bind_params[':status'] = $filters['status'];
    }

    // 優先度フィルタ
    if (isset($filters['priority']) && validatePriority($filters['priority'])) {
        $where_conditions[] = 'priority = :priority';
        $bind_params[':priority'] = $filters['priority'];
    }

    // キーワード検索
    if (!empty($filters['keyword'])) {
        $where_conditions[] = '(title LIKE :keyword OR description LIKE :keyword)';
        $bind_params[':keyword'] = '%' . $filters['keyword'] . '%';
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // ... ページング処理とクエリ実行
}
```

### ページング機能（index.php）

- 1ページあたり10件表示
- 省略表示（... ）機能あり
- 現在ページの前後2ページを表示
- フィルタ条件を保持したままページ移動

### フラッシュメッセージ（functions.php）

```php
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}
```

## 🎓 バイブコーディング実践ポイント

### AIへの指示例

**良い指示**：

```text
「MySQLのtasksテーブルからタスク一覧を取得するPHP関数を作成してください。
以下の要件を満たすこと：
- PDOプリペアドステートメントを使用
- ステータス、優先度、キーワードによるフィルタリング機能
- ページング機能（LIMIT/OFFSET）
- SQLインジェクション対策を徹底」
```

**悪い指示**：

```text
「タスク一覧を取得する関数を作って」
```

### 生成コードのチェックリスト

#### 🔒 セキュリティチェック（最優先）

- [ ] プリペアドステートメント使用（直接SQL埋め込みなし）
- [ ] パラメータバインディング（`:placeholder` または `?`）
- [ ] 出力時の `htmlspecialchars()` 使用
- [ ] CSRF トークン検証（POST操作）
- [ ] 入力値のバリデーション

#### ✅ 機能チェック

- [ ] エラーハンドリング（try-catch）
- [ ] データベース接続エラーの処理
- [ ] NULL値の適切な処理
- [ ] リダイレクト後のexit()

#### 📝 コード品質チェック

- [ ] 意味のある変数名
- [ ] 適切なコメント
- [ ] DRY原則（重複排除）

### よくある問題と修正方法

#### ❌ 問題1：SQLインジェクション脆弱性

```php
// 危険なコード
$status = $_GET['status'];
$sql = "SELECT * FROM tasks WHERE status = '$status'";
$result = $pdo->query($sql);
```

**修正**：

```php
// 安全なコード
$status = $_GET['status'];
if (validateStatus($status)) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE status = :status");
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->execute();
}
```

#### ❌ 問題2：XSS脆弱性

```php
// 危険なコード
<div><?php echo $task['title']; ?></div>
```

**修正**：

```php
// 安全なコード
<div><?php echo h($task['title']); ?></div>
```

#### ❌ 問題3：CSRF脆弱性

```php
// 危険なコード（トークンなし）
<form method="POST" action="delete.php">
    <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
    <button type="submit">削除</button>
</form>
```

**修正**：

```php
// 安全なコード
<form method="POST" action="delete.php">
    <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">
    <input type="hidden" name="id" value="<?php echo h($task['id']); ?>">
    <button type="submit">削除</button>
</form>
```

## 🌟 カスタマイズポイント

### 1ページあたりの表示件数を変更

`index.php` 33行目：

```php
$result = getTasks($filters, $page, 10);  // 10を任意の数値に変更
```

### 優先度の選択肢を増やす

1. `database.sql` の ENUM を変更
2. `functions.php` の `validatePriority()` を更新
3. `functions.php` の `getPriorityLabel()` を更新
4. フォーム（add.php, edit.php）の選択肢を追加

### ソート順を変更

`functions.php` 126-133行目：

```php
ORDER BY
    CASE priority
        WHEN 'high' THEN 1
        WHEN 'medium' THEN 2
        WHEN 'low' THEN 3
    END,
    due_date ASC,
    created_at DESC
```

## 📚 学習のポイント

このプロジェクトで学べること：

1. **セキュアなCRUD実装**：SQLインジェクション、XSS、CSRF対策の実践
2. **PDOの使い方**：プリペアドステートメント、パラメータバインディング
3. **セッション管理**：CSRF トークン、フラッシュメッセージ
4. **動的SQL構築**：フィルタリング、ページング
5. **バリデーション**：入力値の検証、ホワイトリスト方式
6. **エラーハンドリング**：try-catch、ユーザーフレンドリーなメッセージ
7. **コード設計**：関数の分割、責務の分離

## ⚠️ 本番環境への展開前チェックリスト

- [ ] `config.php` のエラー表示を無効化（`display_errors = 0`）
- [ ] データベース認証情報を環境変数化
- [ ] HTTPS環境で `session.cookie_secure = 1` に設定
- [ ] エラーログの保存先を確認
- [ ] データベースバックアップ体制の確立
- [ ] アクセスログの設定

## 🎉 まとめ

このタスク管理システムは、**セキュリティを最優先**にしながら、実用的な機能を実装した教材です。

**重要なポイント**：

- すべてのユーザー入力は疑う（バリデーション）
- すべてのデータベース操作はプリペアドステートメント
- すべての出力はエスケープ
- すべての状態変更操作はCSRF保護

**バイブコーダーとして**：

- AI に明確な指示を出す（セキュリティ要件を含める）
- 生成されたコードを必ずセキュリティチェック
- 脆弱性を見つけたら即座に修正

**Let's vibe and code securely! 🔒🎉**
