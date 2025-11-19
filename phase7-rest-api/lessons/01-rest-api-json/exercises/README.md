# 補足: REST APIとJSON - 演習問題 🌐

REST APIとJSONの知識を活用して、実践的なAPIを構築しよう！

---

## 📝 準備

演習を始める前に、データベースとテーブルを準備しよう！

```sql
CREATE DATABASE IF NOT EXISTS phase5_api_practice CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE phase5_api_practice;

-- 商品テーブル
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- サンプルデータ
INSERT INTO products (name, description, price, stock) VALUES
('MacBook Pro', '高性能ノートパソコン', 198000.00, 10),
('iPhone 15', '最新スマートフォン', 118800.00, 25),
('iPad Air', 'タブレット端末', 84800.00, 15),
('AirPods Pro', 'ワイヤレスイヤホン', 39800.00, 30);
```

---

## 🌱 基礎編

### 問題6-1：JSON生成

**課題**：

PHPの配列をJSON形式に変換して出力してください。

**要件**：

- 以下の商品情報をJSON形式で出力
- `json_encode()`を使用
- `JSON_PRETTY_PRINT`と`JSON_UNESCAPED_UNICODE`オプションを使用
- ヘッダーに`Content-Type: application/json`を設定

**商品情報**：

```php
$product = [
    'id' => 1,
    'name' => 'MacBook Pro',
    'price' => 198000,
    'stock' => 10,
    'inStock' => true
];
```

**期待される出力**：

```json
{
    "id": 1,
    "name": "MacBook Pro",
    "price": 198000,
    "stock": 10,
    "inStock": true
}
```

---

### 問題6-2：JSON解析

**課題**：

JSON文字列をPHPの配列に変換して、値を取り出してください。

**要件**：

- `json_decode()`を使用
- 連想配列に変換（第2引数`true`）
- エラーハンドリング（`json_last_error()`）
- 商品名と価格を表示

**JSON文字列**：

```json
{
    "id": 1,
    "name": "MacBook Pro",
    "price": 198000,
    "stock": 10,
    "category": {
        "id": 1,
        "name": "電子機器"
    }
}
```

**ヒント**：

```php
<?php
$json = '{"id":1,"name":"MacBook Pro","price":198000}';
$product = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSONエラー: " . json_last_error_msg();
    exit;
}

echo "商品名: " . $product['name'];
?>
```

---

### 問題6-3：GET - 商品一覧取得API

**課題**：

すべての商品をJSON形式で返すAPIを作成してください。

**要件**：

- エンドポイント：`GET /api/products.php`
- データベースから商品一覧を取得
- JSON形式でレスポンス
- ステータスコード：200 OK
- レスポンス形式：

  ```json
  {
      "success": true,
      "data": [...],
      "count": 4
  }
  ```

**ヒント**：

```php
<?php
require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("SELECT * FROM products");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'data' => $products,
        'count' => count($products)
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'エラーが発生しました。'
    ], JSON_UNESCAPED_UNICODE);
}
?>
```

---

### 問題6-4：GET - 特定商品取得API

**課題**：

商品IDを指定して、特定の商品をJSON形式で返すAPIを作成してください。

**要件**：

- エンドポイント：`GET /api/products.php?id=1`
- クエリパラメータで商品IDを受け取る
- プリペアドステートメントで取得
- 商品が見つからない場合は404エラー
- JSON形式でレスポンス

**レスポンス例（成功）**：

```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "MacBook Pro",
        "price": 198000,
        "stock": 10
    }
}
```

**レスポンス例（失敗）**：

```json
{
    "success": false,
    "message": "商品が見つかりません。"
}
```

---

## 🚀 応用編

### 問題6-5：POST - 商品作成API

**課題**：

新しい商品を作成するAPIを作成してください。

**要件**：

- エンドポイント：`POST /api/products.php`
- リクエストボディからJSONデータを受け取る
- バリデーション（商品名、価格は必須）
- プリペアドステートメントでINSERT
- ステータスコード：201 Created
- `lastInsertId()`で作成された商品IDを返す

**リクエストボディ例**：

```json
{
    "name": "Apple Watch",
    "description": "スマートウォッチ",
    "price": 59800,
    "stock": 20
}
```

**レスポンス例（成功）**：

```json
{
    "success": true,
    "message": "商品を作成しました。",
    "data": {
        "id": 5,
        "name": "Apple Watch",
        "price": 59800
    }
}
```

**ヒント**：

```php
<?php
// リクエストボディを取得
$input = json_decode(file_get_contents('php://input'), true);

// バリデーション
if (empty($input['name']) || !isset($input['price'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '商品名と価格は必須です。'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// INSERT処理...
?>
```

---

### 問題6-6：PUT - 商品更新API

**課題**：

既存の商品を更新するAPIを作成してください。

**要件**：

- エンドポイント：`PUT /api/products.php`
- リクエストボディからJSONデータを受け取る
- 商品IDは必須
- プリペアドステートメントでUPDATE
- 商品が見つからない場合は404エラー
- ステータスコード：200 OK

**リクエストボディ例**：

```json
{
    "id": 1,
    "name": "MacBook Pro M3",
    "price": 218000,
    "stock": 8
}
```

**レスポンス例（成功）**：

```json
{
    "success": true,
    "message": "商品を更新しました。"
}
```

---

### 問題6-7：DELETE - 商品削除API

**課題**：

商品を削除するAPIを作成してください。

**要件**：

- エンドポイント：`DELETE /api/products.php`
- リクエストボディからJSONデータを受け取る
- 商品IDは必須
- プリペアドステートメントでDELETE
- 商品が見つからない場合は404エラー
- ステータスコード：200 OK

**リクエストボディ例**：

```json
{
    "id": 1
}
```

**レスポンス例（成功）**：

```json
{
    "success": true,
    "message": "商品を削除しました。"
}
```

---

### 問題6-8：HTTPメソッドに応じた処理

**課題**：

1つのファイルで、HTTPメソッドに応じて処理を分岐するAPIを作成してください。

**要件**：

- エンドポイント：`/api/products.php`
- GET：商品一覧取得 / 特定商品取得
- POST：商品作成
- PUT：商品更新
- DELETE：商品削除
- サポートされていないメソッドは405エラー

**ヒント**：

```php
<?php
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($pdo);
        break;
    case 'POST':
        handlePost($pdo);
        break;
    case 'PUT':
        handlePut($pdo);
        break;
    case 'DELETE':
        handleDelete($pdo);
        break;
    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'サポートされていないHTTPメソッドです。'
        ], JSON_UNESCAPED_UNICODE);
        break;
}
?>
```

---

### 問題6-9：CORS設定

**課題**：

CORS（Cross-Origin Resource Sharing）を設定して、異なるドメインからのアクセスを許可してください。

**要件**：

- すべてのドメインからのアクセスを許可（`Access-Control-Allow-Origin: *`）
- GET、POST、PUT、DELETEメソッドを許可
- Content-Typeヘッダーを許可
- OPTIONSリクエスト（プリフライト）を処理

**ヒント**：

```php
<?php
// CORSヘッダーを設定
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// OPTIONSリクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 以下、通常のAPI処理...
?>
```

---

### 問題6-10：検索機能付きAPI

**課題**：

商品名で検索できるAPIを作成してください。

**要件**：

- エンドポイント：`GET /api/products.php?search=Mac`
- クエリパラメータ`search`で検索キーワードを受け取る
- 商品名に部分一致する商品を取得（LIKE）
- JSON形式でレスポンス
- 検索結果の件数も返す

**レスポンス例**：

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "MacBook Pro",
            "price": 198000,
            "stock": 10
        }
    ],
    "count": 1,
    "query": "Mac"
}
```

**ヒント**：

```php
<?php
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $stmt = $pdo->prepare("
        SELECT * FROM products
        WHERE name LIKE :search
        ORDER BY created_at DESC
    ");
    $stmt->execute([':search' => '%' . $search . '%']);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
```

---

## 🛡️ セキュリティチャレンジ

### 問題6-11：入力検証（バリデーション）

**課題**：

商品作成APIに、詳細な入力検証を実装してください。

**要件**：

- 商品名：必須、255文字以内
- 価格：必須、0以上の数値
- 在庫：0以上の整数
- バリデーションエラー時はステータスコード422
- エラーメッセージを配列で返す

**レスポンス例（バリデーションエラー）**：

```json
{
    "success": false,
    "message": "バリデーションエラー",
    "errors": [
        "商品名は必須です。",
        "価格は0以上の数値で入力してください。"
    ]
}
```

**ヒント**：

```php
<?php
function validateProduct($data) {
    $errors = [];

    if (empty($data['name'])) {
        $errors[] = '商品名は必須です。';
    } elseif (mb_strlen($data['name']) > 255) {
        $errors[] = '商品名は255文字以内で入力してください。';
    }

    if (!isset($data['price'])) {
        $errors[] = '価格は必須です。';
    } elseif (!is_numeric($data['price']) || $data['price'] < 0) {
        $errors[] = '価格は0以上の数値で入力してください。';
    }

    return $errors;
}

$input = json_decode(file_get_contents('php://input'), true);
$errors = validateProduct($input);

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'バリデーションエラー',
        'errors' => $errors
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
```

---

### 問題6-12：API Key認証

**課題**：

API Key認証を実装してください。

**要件**：

- リクエストヘッダー`X-API-KEY`でAPI Keyを受け取る
- 正しいAPI Keyでない場合は401エラー
- API Key：`your-secret-api-key-12345`（本番環境では環境変数に保存）

**リクエストヘッダー例**：

```text
X-API-KEY: your-secret-api-key-12345
```

**レスポンス例（認証失敗）**：

```json
{
    "success": false,
    "message": "認証に失敗しました。API Keyが正しくありません。"
}
```

**ヒント**：

```php
<?php
// API Keyを取得
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

// 検証
if ($apiKey !== 'your-secret-api-key-12345') {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => '認証に失敗しました。API Keyが正しくありません。'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 以下、通常のAPI処理...
?>
```

**JavaScriptからのAPI呼び出し例**：

```javascript
fetch('http://localhost:8888/api/products.php', {
  method: 'GET',
  headers: {
    'X-API-KEY': 'your-secret-api-key-12345'
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

---

### 問題6-13：レートリミット

**課題**：

過度なリクエストを防ぐために、レートリミットを実装してください。

**要件**：

- セッションでリクエスト数を管理
- 1分間に10回までのリクエストを許可
- 超えた場合は429エラー

**レスポンス例（レートリミット超過）**：

```json
{
    "success": false,
    "message": "リクエスト数が多すぎます。しばらく待ってから再試行してください。"
}
```

---

## 💪 総合チャレンジ

### 問題6-14：完全なREST API

**課題**：

以下の機能を持つ完全な商品管理REST APIを作成してください。

**機能一覧**：

1. **GET /api/products.php** - 商品一覧取得
   - クエリパラメータ`search`で検索
   - クエリパラメータ`id`で特定商品取得

2. **POST /api/products.php** - 商品作成
   - バリデーション
   - ステータスコード：201 Created

3. **PUT /api/products.php** - 商品更新
   - バリデーション
   - ステータスコード：200 OK

4. **DELETE /api/products.php** - 商品削除
   - ステータスコード：200 OK

**セキュリティ要件**：

- すべてプリペアドステートメント
- 入力検証（バリデーション）
- CORS設定
- API Key認証
- レートリミット
- 適切なHTTPステータスコード

**レスポンス形式の統一**：

```json
{
    "success": true/false,
    "message": "...",
    "data": {...},
    "errors": [...]
}
```

**ファイル構成例**：

```text
/api/
├── config.php           # データベース接続
├── functions.php        # 共通関数（バリデーション、認証など）
└── products.php         # 商品API
```

---

### 問題6-15：HTMLテストページ

**課題**：

JavaScriptからAPIを呼び出すHTMLテストページを作成してください。

**要件**：

- Fetch APIを使用
- GET、POST、PUT、DELETEすべてのメソッドをテスト
- レスポンスをページに表示
- エラーハンドリング

**HTML例**：

```html
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>API Test</title>
</head>
<body>
    <h1>商品API テスト</h1>

    <h2>商品一覧取得（GET）</h2>
    <button onclick="getProducts()">実行</button>
    <pre id="result-get"></pre>

    <h2>商品作成（POST）</h2>
    <button onclick="createProduct()">実行</button>
    <pre id="result-post"></pre>

    <script>
        async function getProducts() {
            try {
                const response = await fetch('http://localhost:8888/api/products.php', {
                    method: 'GET',
                    headers: {
                        'X-API-KEY': 'your-secret-api-key-12345'
                    }
                });

                const data = await response.json();
                document.getElementById('result-get').textContent = JSON.stringify(data, null, 2);

            } catch (error) {
                console.error('エラー:', error);
            }
        }

        async function createProduct() {
            try {
                const response = await fetch('http://localhost:8888/api/products.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-API-KEY': 'your-secret-api-key-12345'
                    },
                    body: JSON.stringify({
                        name: 'Apple Watch',
                        description: 'スマートウォッチ',
                        price: 59800,
                        stock: 20
                    })
                });

                const data = await response.json();
                document.getElementById('result-post').textContent = JSON.stringify(data, null, 2);

            } catch (error) {
                console.error('エラー:', error);
            }
        }
    </script>
</body>
</html>
```

---

## 🤖 バイブコーディングのヒント

### AIへの良い指示例

```text
「商品管理のREST APIを作成してください。

エンドポイント：
- GET /api/products - 商品一覧取得
- GET /api/products?id=1 - 特定商品取得
- POST /api/products - 商品作成
- PUT /api/products - 商品更新
- DELETE /api/products - 商品削除

レスポンス形式：JSON
- 成功時: {"success": true, "data": {...}}
- エラー時: {"success": false, "message": "..."}

セキュリティ要件：
- すべてプリペアドステートメントを使用
- 入力バリデーション実装
- 適切なHTTPステータスコードを返す
- CORSヘッダーを設定
- API Key認証を実装

技術要件：
- PDOを使用
- Content-Typeヘッダーを application/json に設定
- file_get_contents('php://input') でリクエストボディを取得
- try-catchでエラーハンドリング
- json_encode()にJSON_UNESCAPED_UNICODEを指定」
```

### チェックポイント

✅ **セキュリティチェック**

- [ ] すべてのSQL文でプリペアドステートメント使用
- [ ] 入力バリデーション実装
- [ ] CORS設定
- [ ] API Key認証
- [ ] レートリミット

✅ **API設計チェック**

- [ ] HTTPメソッドの適切な使い分け
- [ ] 適切なHTTPステータスコード
- [ ] 統一されたレスポンス形式
- [ ] エラーメッセージの明確化

✅ **コード品質チェック**

- [ ] Content-Typeヘッダー設定
- [ ] json_encode()にJSON_UNESCAPED_UNICODE指定
- [ ] try-catchでエラーハンドリング
- [ ] 関数で処理を分離

---

## 💡 よくある問題

### 問題：CORSエラー

```text
Access to fetch at 'http://localhost:8888/api/products.php' from origin 'http://localhost:3000' has been blocked by CORS policy
```

**解決**：

```php
<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
?>
```

---

### 問題：POST/PUT/DELETEで$_POSTが使えない

**原因**：`$_POST`はフォーム送信（`application/x-www-form-urlencoded`）でしか使えない

**解決**：

```php
<?php
// ❌ 間違い
$input = $_POST;  // PUT、DELETEでは空

// ✅ 正しい
$input = json_decode(file_get_contents('php://input'), true);
?>
```

---

### 問題：日本語が文字化けする

**原因**：UTF-8設定が不足

**解決**：

```php
<?php
// ヘッダーにcharset=utf-8を指定
header('Content-Type: application/json; charset=utf-8');

// json_encode()にJSON_UNESCAPED_UNICODEを指定
echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
```

---

👉 **[解答例を見る](solutions/README.md)**

**Let's vibe and code! 🎉**

REST APIを実装して、モダンなWebアプリケーション開発の第一歩を踏み出そう！
