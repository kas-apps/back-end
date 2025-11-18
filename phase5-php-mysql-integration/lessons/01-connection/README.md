# Lesson 01: データベース接続 🔌

**学習目標**：PHPからMySQLに安全に接続し、接続エラーを適切に処理できるようになる！

---

## 📖 このレッスンで学ぶこと

- **PDO（PHP Data Objects）**とは何か
- データベース接続の基本（DSN、ユーザー名、パスワード）
- 接続オプションの設定（エラーモード、フェッチモード、文字エンコーディング）
- エラーハンドリング（try-catch）
- 設定ファイルの分離（config.php）
- MAMP環境での接続設定（ポート8889）
- 接続エラーのデバッグ方法

---

## 🎯 なぜデータベース接続を学ぶの？（Why）

### データベース接続はWebアプリケーションの入り口！

**静的HTMLとの違い**：

```text
静的HTML：
ブラウザ → Webサーバー → HTMLファイルを返す

動的Webアプリケーション（PHP + MySQL）：
ブラウザ → Webサーバー → PHP実行 → データベース接続 → データ取得 → HTMLを動的生成
                                    ↑ ここが重要！
```

**データベース接続がないと...**：
- ❌ ユーザー情報を保存できない
- ❌ ブログ記事を取得できない
- ❌ 商品データを表示できない
- ❌ ログイン機能が作れない

**データベース接続ができると**：
- ✅ ユーザー登録・ログイン機能
- ✅ ブログシステム
- ✅ ECサイト
- ✅ SNS
- ✅ ほとんどすべてのWebアプリケーション！

### バックエンド開発における重要性

**データベース接続は、バックエンド開発の最初のステップ**！

- 📊 **データの永続化**：サーバーを再起動してもデータが消えない
- 🔒 **セキュリティ**：安全な接続方法を使う（PDO）
- 🚀 **パフォーマンス**：効率的な接続管理（持続的接続）
- 🛠️ **保守性**：設定ファイルを分離して管理しやすく

---

## 🏗️ PDOとは？（What）

### PDO（PHP Data Objects）の基礎知識

**PDO**は、PHPからデータベースに接続するための**統一されたインターフェース**！

**アナロジー：電源プラグのアダプター**

- **PDO = 万能アダプター**
- **データベース（MySQL、PostgreSQL、SQLite） = 各国のコンセント**

```text
PHPアプリケーション
     ↓
    PDO（万能アダプター）
     ↓
  MySQL / PostgreSQL / SQLite などのデータベース
```

### MySQLへの接続方法：mysqliとPDOの違い

PHPからMySQLに接続する方法は主に2つあります：

**1. mysqli（MySQL Improved）**
- MySQL専用の拡張機能
- MySQLに特化した機能が使える
- 古いコードでよく使われている

**2. PDO（PHP Data Objects）**
- 複数のデータベースに対応した統一インターフェース
- **現代的な推奨方法**
- セキュアなプリペアドステートメントが使いやすい

**比較表**：

| 項目 | mysqli | PDO |
|-----|--------|-----|
| **対応データベース** | MySQLのみ | MySQL、PostgreSQL、SQLite、Oracle など |
| **プリペアドステートメント** | サポート | サポート（より直感的） |
| **エラーハンドリング** | 手動でチェック必要 | 例外（try-catch）が使える |
| **コードの移植性** | 低い（MySQL専用） | 高い（他のDBにも対応） |
| **推奨度** | △（特別な理由がない限り非推奨） | ✅ **推奨** |

**コード比較例**：

**mysqli（古い方法）**：
```php
<?php
// mysqliでの接続（非推奨）
$mysqli = new mysqli('localhost', 'root', 'root', 'mydb');

if ($mysqli->connect_error) {
    die('接続エラー: ' . $mysqli->connect_error);
}

// データ取得
$result = $mysqli->query("SELECT * FROM users");
while ($row = $result->fetch_assoc()) {
    echo $row['name'];
}

$mysqli->close();
?>
```

**PDO（推奨）**：
```php
<?php
// PDOでの接続（推奨）
try {
    $pdo = new PDO('mysql:host=localhost;dbname=mydb;charset=utf8mb4', 'root', 'root');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // データ取得
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as $user) {
        echo $user['name'];
    }

} catch (PDOException $e) {
    die('接続エラー: ' . $e->getMessage());
}
?>
```

**なぜPDOを使うべきか？**

✅ **将来性**：MySQLから他のデータベースに移行する可能性がある
✅ **セキュリティ**：プリペアドステートメントが使いやすい（SQLインジェクション対策）
✅ **エラーハンドリング**：例外処理が標準でサポート
✅ **業界標準**：現代的なPHPプロジェクトはほぼすべてPDOを使用
✅ **フレームワーク**：Laravel、Symfony、CakePHPなどの主要フレームワークはPDOを使用

**このレッスンではPDOを使います！**

---

### PDOのメリット

**1. データベースの種類に依存しない**

```php
// MySQLに接続
$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'root', 'password');

// PostgreSQLに接続（同じコードの流れ）
$pdo = new PDO('pgsql:host=localhost;dbname=mydb', 'root', 'password');
```

**2. プリペアドステートメントのサポート**

SQLインジェクション対策に必須！（Lesson 02で詳しく学ぶ）

**3. 例外処理（エラーハンドリング）**

try-catchでエラーを適切に処理できる

**4. オブジェクト指向**

オブジェクト指向のスタイルでデータベース操作ができる

### DSN（Data Source Name）とは？

**DSN**は、データベースへの接続情報をまとめた文字列！

```php
// DSNの構成
"mysql:host=localhost;port=8889;dbname=mydb;charset=utf8mb4"
 ↓     ↓             ↓          ↓            ↓
 DB種類 ホスト名      ポート番号  DB名         文字コード
```

**MAMPの場合の標準的なDSN**：

```php
$dsn = 'mysql:host=localhost;port=8889;dbname=mydb;charset=utf8mb4';
```

---

## 💻 PDOでの接続方法（How）

### 基本的な接続コード

```php
<?php
// データベース接続情報
$host = 'localhost';
$port = '8889';  // MAMPのデフォルトポート
$dbname = 'mydb';
$username = 'root';
$password = 'root';  // MAMPのデフォルトパスワード

// DSNを構築
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    // PDOインスタンスを作成（データベースに接続）
    $pdo = new PDO($dsn, $username, $password);

    echo "接続成功！";

} catch (PDOException $e) {
    // 接続エラーの場合
    die("接続エラー: " . $e->getMessage());
}
?>
```

**ポイント**：

- `new PDO()`でデータベースに接続
- try-catchでエラーをキャッチ
- 接続失敗時は`PDOException`が投げられる

### 接続オプションの設定

**接続オプション**で、PDOの動作をカスタマイズできる！

```php
<?php
$dsn = "mysql:host=localhost;port=8889;dbname=mydb;charset=utf8mb4";
$username = 'root';
$password = 'root';

try {
    $pdo = new PDO($dsn, $username, $password, [
        // エラーモード：例外を投げる
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

        // デフォルトのフェッチモード：連想配列
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

        // エミュレートプリペアを無効化（セキュリティ向上）
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    echo "接続成功（オプション設定済み）！";

} catch (PDOException $e) {
    die("接続エラー: " . $e->getMessage());
}
?>
```

**各オプションの説明**：

**1. PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION**
- エラーが発生したときに例外を投げる
- try-catchでエラーをキャッチできる

**2. PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC**
- データ取得時のデフォルトモードを連想配列にする
- `$row['name']`のようにアクセスできる

**3. PDO::ATTR_EMULATE_PREPARES => false**
- 真のプリペアドステートメントを使用
- セキュリティ向上（SQLインジェクション対策）

### 設定ファイルの分離（config.php）

**ベストプラクティス**：接続情報は別ファイルに分離する！

**config.php**：

```php
<?php
// データベース接続設定
define('DB_HOST', 'localhost');
define('DB_PORT', '8889');
define('DB_NAME', 'mydb');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// DSNを構築
$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

try {
    // PDOインスタンスを作成
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    // 接続成功（通常はechoしない）
    // echo "データベース接続成功！";

} catch (PDOException $e) {
    // 本番環境では詳細なエラーメッセージを表示しない
    die("データベース接続エラーが発生しました。");

    // 開発環境ではエラーメッセージを表示
    // die("接続エラー: " . $e->getMessage());
}
?>
```

**他のPHPファイルから使う**：

```php
<?php
// config.phpを読み込み
require_once 'config.php';

// $pdoが使える！
$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll();

foreach ($users as $user) {
    echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "<br>";
}
?>
```

**メリット**：

✅ **コードの再利用**：すべてのファイルで同じ接続を使える
✅ **保守性向上**：接続情報を1箇所で管理
✅ **セキュリティ**：config.phpを.gitignoreに追加して、パスワードを隠せる

---

## 🤖 バイブコーディング実践

### AIへの指示例

**基本的な接続コードを生成させる**：

```text
✅ 良い指示：

「MAMP環境でMySQLに接続するPHPコードを書いてください。

要件：
- PDOを使用
- ホスト: localhost、ポート: 8889、データベース名: mydb
- ユーザー名: root、パスワード: root
- 文字コード: utf8mb4
- エラーモード: 例外を投げる（ERRMODE_EXCEPTION）
- デフォルトフェッチモード: 連想配列（FETCH_ASSOC）
- エミュレートプリペアを無効化
- try-catchでエラーハンドリング」
```

**設定ファイルを生成させる**：

```text
✅ 良い指示：

「データベース接続設定ファイル（config.php）を作成してください。

要件：
- define()で定数を定義（DB_HOST、DB_PORT、DB_NAME、DB_USER、DB_PASS）
- PDOインスタンスを作成して$pdo変数に格納
- セキュアな接続オプションを設定
- try-catchでエラーハンドリング
- 本番環境ではエラーメッセージを隠す」
```

### 生成されたコードのチェックポイント

✅ **接続設定チェック**
- [ ] PDOを使っている（mysql_connect()は古い）
- [ ] DSNに文字コード（charset=utf8mb4）を指定している
- [ ] ポート番号が正しい（MAMP: 8889）

✅ **セキュリティチェック**
- [ ] エラーモードをEXCEPTIONに設定している
- [ ] エミュレートプリペアを無効化している（EMULATE_PREPARES => false）
- [ ] 本番環境で詳細なエラーメッセージを表示していない

✅ **エラーハンドリングチェック**
- [ ] try-catchを使っている
- [ ] PDOExceptionをキャッチしている
- [ ] エラー時に適切なメッセージを表示している

### よくあるAI生成コードの問題と修正

**問題1: 文字コードの指定がない**

```php
// ❌ 悪い例（文字化けする可能性）
$dsn = "mysql:host=localhost;dbname=mydb";
```

**修正**：

```php
// ✅ 良い例
$dsn = "mysql:host=localhost;dbname=mydb;charset=utf8mb4";
```

**問題2: エラーハンドリングがない**

```php
// ❌ 悪い例（エラーが握りつぶされる）
$pdo = new PDO($dsn, $username, $password);
```

**修正**：

```php
// ✅ 良い例
try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die("接続エラー: " . $e->getMessage());
}
```

**問題3: セキュアな接続オプションがない**

```php
// ❌ 悪い例（デフォルト設定のまま）
$pdo = new PDO($dsn, $username, $password);
```

**修正**：

```php
// ✅ 良い例
$pdo = new PDO($dsn, $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
]);
```

---

## 💡 よくあるエラーと解決方法

### エラー1: "SQLSTATE[HY000] [2002] Connection refused"

**原因**：MySQLサーバーが起動していない

**解決**：

1. MAMPを起動
2. 「Start Servers」をクリック
3. MySQLサーバーが起動していることを確認

### エラー2: "SQLSTATE[HY000] [1049] Unknown database 'mydb'"

**原因**：指定したデータベースが存在しない

**解決**：

1. phpMyAdminにアクセス
2. 新しいデータベース「mydb」を作成
3. 文字コード「utf8mb4_unicode_ci」を選択

### エラー3: "SQLSTATE[HY000] [2002] No such file or directory"

**原因**：ポート番号が間違っている、またはソケットファイルが見つからない

**解決**：

```php
// MAMPの場合はポート8889を明示的に指定
$dsn = "mysql:host=localhost;port=8889;dbname=mydb;charset=utf8mb4";
```

### エラー4: "Access denied for user 'root'@'localhost'"

**原因**：ユーザー名またはパスワードが間違っている

**解決**：

- MAMPのデフォルト: ユーザー名 `root`、パスワード `root`
- パスワードを変更した場合は、変更後のパスワードを使用

---

## 📊 接続確認コード

接続が正しくできているか確認するためのコード：

```php
<?php
require_once 'config.php';

try {
    // 接続確認クエリ
    $stmt = $pdo->query("SELECT VERSION()");
    $version = $stmt->fetchColumn();

    echo "✅ データベース接続成功！<br>";
    echo "MySQLバージョン: " . htmlspecialchars($version, ENT_QUOTES, 'UTF-8');

    // データベース名を確認
    $stmt = $pdo->query("SELECT DATABASE()");
    $dbname = $stmt->fetchColumn();

    echo "<br>接続中のデータベース: " . htmlspecialchars($dbname, ENT_QUOTES, 'UTF-8');

} catch (PDOException $e) {
    echo "❌ 接続エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

---

## 🎓 まとめ

このレッスンで学んだこと：

✅ **PDOの基礎**
- PDOとは何か
- DSNの構成
- 接続オプションの設定

✅ **安全な接続方法**
- try-catchでエラーハンドリング
- セキュアな接続オプション
- 文字コードの指定（utf8mb4）

✅ **ベストプラクティス**
- 設定ファイルの分離（config.php）
- 本番環境でのエラーメッセージの扱い
- MAMPでの接続設定

✅ **バイブコーディング**
- AIに接続コードを生成させる指示方法
- 生成されたコードのセキュリティチェック
- よくある問題の発見と修正

### 次のステップ

データベースに接続できたら、次は**プリペアドステートメント**を学ぼう！

👉 **[Lesson 02: プリペアドステートメント](../02-prepared-statements/README.md)**

SQLインジェクション対策の必須技術を学んで、セキュアなデータベース操作をマスターしよう！

👉 **[演習問題を見る](exercises/README.md)**

---

**Let's vibe and code! 🎉**

データベース接続ができたら、あとは自由にデータを操作できる！次のレッスンでセキュアなデータ操作を学ぼう！
