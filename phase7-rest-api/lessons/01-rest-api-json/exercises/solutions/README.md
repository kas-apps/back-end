# 補足: REST APIとJSON - 解答例 🌐

各問題の解答例と解説を示します。実際のREST API開発に使えるコードです！

---

## 🌱 基礎編

### 問題6-1：JSON生成 - 解答例

```php
<?php
// Content-Typeヘッダーを設定
header('Content-Type: application/json; charset=utf-8');

// 商品情報
$product = [
    'id' => 1,
    'name' => 'MacBook Pro',
    'price' => 198000,
    'stock' => 10,
    'inStock' => true
];

// JSONに変換して出力
echo json_encode($product, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
```

**解説**：

✅ **ポイント**

- `Content-Type: application/json`：レスポンスがJSON形式であることを明示
- `JSON_PRETTY_PRINT`：整形して読みやすくする
- `JSON_UNESCAPED_UNICODE`：日本語をエスケープせずに出力

---

### 問題6-2：JSON解析 - 解答例

```php
<?php
// JSON文字列
$json = '{
    "id": 1,
    "name": "MacBook Pro",
    "price": 198000,
    "stock": 10,
    "category": {
        "id": 1,
        "name": "電子機器"
    }
}';

// JSONをPHPの連想配列に変換
$product = json_decode($json, true);

// エラーチェック
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSONエラー: " . json_last_error_msg();
    exit;
}

// 値を取り出して表示
echo "商品名: " . htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . "\n";
echo "価格: " . number_format($product['price']) . "円\n";
echo "カテゴリー: " . htmlspecialchars($product['category']['name'], ENT_QUOTES, 'UTF-8') . "\n";
?>
```

**解説**：

✅ **ポイント**

- `json_decode($json, true)`：第2引数`true`で連想配列に変換
- `json_last_error()`：JSONのエラーをチェック
- 入れ子構造：`$product['category']['name']`でアクセス

---

### 問題6-3：GET - 商品一覧取得API - 解答例

**api/products.php**：

```php
<?php
// config.phpを読み込み（データベース接続）
require_once '../config.php';

// Content-Typeヘッダーを設定
header('Content-Type: application/json; charset=utf-8');

try {
    // 商品一覧を取得
    $stmt = $pdo->query("
        SELECT id, name, description, price, stock, created_at
        FROM products
        ORDER BY created_at DESC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 成功レスポンス
    http_response_code(200); // 200 OK
    echo json_encode([
        'success' => true,
        'data' => $products,
        'count' => count($products)
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // エラーレスポンス
    http_response_code(500); // 500 Internal Server Error
    echo json_encode([
        'success' => false,
        'message' => 'データベースエラーが発生しました。'
    ], JSON_UNESCAPED_UNICODE);

    // 本番環境ではログに記録
    // error_log($e->getMessage());
}
?>
```

**解説**：

✅ **ポイント**

- `http_response_code(200)`：HTTPステータスコードを設定
- `count($products)`：商品数を返す
- try-catchでエラーハンドリング

---

### 問題6-4：GET - 特定商品取得API - 解答例

```php
<?php
require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

// 商品IDを取得
$id = $_GET['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    http_response_code(400); // 400 Bad Request
    echo json_encode([
        'success' => false,
        'message' => '不正な商品IDです。'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // 特定の商品を取得
    $stmt = $pdo->prepare("
        SELECT id, name, description, price, stock, created_at, updated_at
        FROM products
        WHERE id = :id
    ");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $product
        ], JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404); // 404 Not Found
        echo json_encode([
            'success' => false,
            'message' => '商品が見つかりません。'
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'エラーが発生しました。'
    ], JSON_UNESCAPED_UNICODE);
}
?>
```

**解説**：

✅ **ポイント**

- `(int)$id`：整数型にキャスト（セキュリティ対策）
- 404 Not Found：商品が見つからない場合のステータスコード
- プリペアドステートメントでSQLインジェクション対策

---

## 🚀 応用編

### 問題6-5：POST - 商品作成API - 解答例

```php
<?php
require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

// POSTメソッドのみ許可
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // 405 Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'POSTメソッドのみサポートしています。'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// リクエストボディからJSONを取得
$input = json_decode(file_get_contents('php://input'), true);

// JSONのパースエラーチェック
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '不正なJSON形式です。'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// バリデーション
$errors = [];

if (empty($input['name'])) {
    $errors[] = '商品名は必須です。';
}

if (!isset($input['price'])) {
    $errors[] = '価格は必須です。';
} elseif (!is_numeric($input['price']) || $input['price'] < 0) {
    $errors[] = '価格は0以上の数値で入力してください。';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'バリデーションエラー',
        'errors' => $errors
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// データを準備
$name = $input['name'];
$description = $input['description'] ?? '';
$price = $input['price'];
$stock = $input['stock'] ?? 0;

try {
    // 商品を作成
    $stmt = $pdo->prepare("
        INSERT INTO products (name, description, price, stock)
        VALUES (:name, :description, :price, :stock)
    ");
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':price' => $price,
        ':stock' => $stock
    ]);

    // 作成された商品のIDを取得
    $lastId = $pdo->lastInsertId();

    // 成功レスポンス
    http_response_code(201); // 201 Created
    echo json_encode([
        'success' => true,
        'message' => '商品を作成しました。',
        'data' => [
            'id' => (int)$lastId,
            'name' => $name,
            'price' => (float)$price,
            'stock' => (int)$stock
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'データベースエラーが発生しました。'
    ], JSON_UNESCAPED_UNICODE);
}
?>
```

**解説**：

✅ **ポイント**

- `file_get_contents('php://input')`：リクエストボディを取得
- `json_decode($input, true)`：JSONをPHPの配列に変換
- `http_response_code(201)`：201 Created（作成成功）
- `lastInsertId()`：作成された商品のIDを取得

---

### 問題6-8：HTTPメソッドに応じた処理 - 解答例

**api/products.php**（完全版）：

```php
<?php
require_once '../config.php';

// CORSヘッダーを設定
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// OPTIONSリクエストの処理（プリフライト）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// HTTPメソッドを取得
$method = $_SERVER['REQUEST_METHOD'];

try {
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
            http_response_code(405); // 405 Method Not Allowed
            echo json_encode([
                'success' => false,
                'message' => 'サポートされていないHTTPメソッドです。'
            ], JSON_UNESCAPED_UNICODE);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'エラーが発生しました。'
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * GET: 商品一覧取得 / 特定商品取得
 */
function handleGet($pdo) {
    $id = $_GET['id'] ?? null;
    $search = $_GET['search'] ?? null;

    try {
        if ($id) {
            // 特定商品を取得
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->execute([':id' => (int)$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'data' => $product
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => '商品が見つかりません。'
                ], JSON_UNESCAPED_UNICODE);
            }

        } elseif ($search) {
            // 商品名で検索
            $stmt = $pdo->prepare("
                SELECT * FROM products
                WHERE name LIKE :search
                ORDER BY created_at DESC
            ");
            $stmt->execute([':search' => '%' . $search . '%']);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $products,
                'count' => count($products),
                'query' => $search
            ], JSON_UNESCAPED_UNICODE);

        } else {
            // 商品一覧を取得
            $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $products,
                'count' => count($products)
            ], JSON_UNESCAPED_UNICODE);
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'エラーが発生しました。'
        ], JSON_UNESCAPED_UNICODE);
    }
}

/**
 * POST: 商品作成
 */
function handlePost($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);

    // バリデーション
    if (empty($input['name']) || !isset($input['price'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => '商品名と価格は必須です。'
        ], JSON_UNESCAPED_UNICODE);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO products (name, description, price, stock)
            VALUES (:name, :description, :price, :stock)
        ");
        $stmt->execute([
            ':name' => $input['name'],
            ':description' => $input['description'] ?? '',
            ':price' => $input['price'],
            ':stock' => $input['stock'] ?? 0
        ]);

        $lastId = $pdo->lastInsertId();

        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => '商品を作成しました。',
            'data' => [
                'id' => (int)$lastId,
                'name' => $input['name'],
                'price' => (float)$input['price']
            ]
        ], JSON_UNESCAPED_UNICODE);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'エラーが発生しました。'
        ], JSON_UNESCAPED_UNICODE);
    }
}

/**
 * PUT: 商品更新
 */
function handlePut($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => '商品IDは必須です。'
        ], JSON_UNESCAPED_UNICODE);
        return;
    }

    try {
        $stmt = $pdo->prepare("
            UPDATE products
            SET name = COALESCE(:name, name),
                description = COALESCE(:description, description),
                price = COALESCE(:price, price),
                stock = COALESCE(:stock, stock)
            WHERE id = :id
        ");
        $stmt->execute([
            ':name' => $input['name'] ?? null,
            ':description' => $input['description'] ?? null,
            ':price' => $input['price'] ?? null,
            ':stock' => $input['stock'] ?? null,
            ':id' => $input['id']
        ]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => '商品を更新しました。'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => '商品が見つかりません。'
            ], JSON_UNESCAPED_UNICODE);
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'エラーが発生しました。'
        ], JSON_UNESCAPED_UNICODE);
    }
}

/**
 * DELETE: 商品削除
 */
function handleDelete($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => '商品IDは必須です。'
        ], JSON_UNESCAPED_UNICODE);
        return;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $input['id']]);

        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => '商品を削除しました。'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => '商品が見つかりません。'
            ], JSON_UNESCAPED_UNICODE);
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'エラーが発生しました。'
        ], JSON_UNESCAPED_UNICODE);
    }
}
?>
```

**解説**：

✅ **ポイント**

- switch文でHTTPメソッドに応じた処理を分岐
- 各メソッドの処理を関数化
- CORS設定で異なるドメインからのアクセスを許可
- 統一されたレスポンス形式

---

## 🛡️ セキュリティチャレンジ

### 問題6-12：API Key認証 - 解答例

**api/products_secure.php**：

```php
<?php
require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

// API Key認証
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($apiKey !== 'your-secret-api-key-12345') {
    http_response_code(401); // 401 Unauthorized
    echo json_encode([
        'success' => false,
        'message' => '認証に失敗しました。API Keyが正しくありません。'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 以下、通常のAPI処理...
$method = $_SERVER['REQUEST_METHOD'];

// （handleGet、handlePost等の処理）
?>
```

**JavaScriptからの呼び出し例**：

```javascript
// 商品一覧取得
async function getProducts() {
    try {
        const response = await fetch('http://localhost:8888/api/products_secure.php', {
            method: 'GET',
            headers: {
                'X-API-KEY': 'your-secret-api-key-12345'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        console.log(data);

    } catch (error) {
        console.error('エラー:', error);
    }
}

// 商品作成
async function createProduct() {
    try {
        const response = await fetch('http://localhost:8888/api/products_secure.php', {
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
        console.log(data);

    } catch (error) {
        console.error('エラー:', error);
    }
}

// 実行
getProducts();
createProduct();
```

**解説**：

✅ **ポイント**

- `$_SERVER['HTTP_X_API_KEY']`：カスタムヘッダーを取得
- 401 Unauthorized：認証失敗のステータスコード
- 本番環境ではAPI Keyを環境変数に保存

---

## 💪 総合チャレンジ

### 問題6-15：HTMLテストページ - 解答例

**api_test.html**：

```html
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REST API テスト</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        .section {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        pre {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            max-height: 400px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <h1>🌐 REST API テストページ</h1>

    <!-- 商品一覧取得 -->
    <div class="section">
        <h2>📦 商品一覧取得（GET）</h2>
        <button onclick="getProducts()">実行</button>
        <pre id="result-get"></pre>
    </div>

    <!-- 特定商品取得 -->
    <div class="section">
        <h2>🔍 特定商品取得（GET）</h2>
        <label>商品ID: <input type="number" id="product-id" value="1"></label>
        <button onclick="getProductById()">実行</button>
        <pre id="result-get-id"></pre>
    </div>

    <!-- 商品作成 -->
    <div class="section">
        <h2>➕ 商品作成（POST）</h2>
        <label>商品名: <input type="text" id="create-name" value="Apple Watch"></label><br>
        <label>価格: <input type="number" id="create-price" value="59800"></label><br>
        <label>在庫: <input type="number" id="create-stock" value="20"></label><br>
        <button onclick="createProduct()">実行</button>
        <pre id="result-post"></pre>
    </div>

    <!-- 商品更新 -->
    <div class="section">
        <h2>✏️ 商品更新（PUT）</h2>
        <label>商品ID: <input type="number" id="update-id" value="1"></label><br>
        <label>商品名: <input type="text" id="update-name" value="MacBook Pro M3"></label><br>
        <label>価格: <input type="number" id="update-price" value="218000"></label><br>
        <button onclick="updateProduct()">実行</button>
        <pre id="result-put"></pre>
    </div>

    <!-- 商品削除 -->
    <div class="section">
        <h2>🗑️ 商品削除（DELETE）</h2>
        <label>商品ID: <input type="number" id="delete-id" value="5"></label><br>
        <button onclick="deleteProduct()">実行</button>
        <pre id="result-delete"></pre>
    </div>

    <script>
        const API_URL = 'http://localhost:8888/api/products.php';
        const API_KEY = 'your-secret-api-key-12345'; // 認証が必要な場合

        /**
         * 商品一覧取得
         */
        async function getProducts() {
            try {
                const response = await fetch(API_URL, {
                    method: 'GET',
                    headers: {
                        // 'X-API-KEY': API_KEY  // 認証が必要な場合
                    }
                });

                const data = await response.json();
                document.getElementById('result-get').textContent = JSON.stringify(data, null, 2);

            } catch (error) {
                document.getElementById('result-get').textContent = 'エラー: ' + error.message;
                document.getElementById('result-get').classList.add('error');
            }
        }

        /**
         * 特定商品取得
         */
        async function getProductById() {
            const id = document.getElementById('product-id').value;

            try {
                const response = await fetch(`${API_URL}?id=${id}`, {
                    method: 'GET'
                });

                const data = await response.json();
                document.getElementById('result-get-id').textContent = JSON.stringify(data, null, 2);

            } catch (error) {
                document.getElementById('result-get-id').textContent = 'エラー: ' + error.message;
            }
        }

        /**
         * 商品作成
         */
        async function createProduct() {
            const name = document.getElementById('create-name').value;
            const price = parseFloat(document.getElementById('create-price').value);
            const stock = parseInt(document.getElementById('create-stock').value);

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name: name,
                        description: 'テストで作成',
                        price: price,
                        stock: stock
                    })
                });

                const data = await response.json();
                document.getElementById('result-post').textContent = JSON.stringify(data, null, 2);

            } catch (error) {
                document.getElementById('result-post').textContent = 'エラー: ' + error.message;
            }
        }

        /**
         * 商品更新
         */
        async function updateProduct() {
            const id = parseInt(document.getElementById('update-id').value);
            const name = document.getElementById('update-name').value;
            const price = parseFloat(document.getElementById('update-price').value);

            try {
                const response = await fetch(API_URL, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id,
                        name: name,
                        price: price
                    })
                });

                const data = await response.json();
                document.getElementById('result-put').textContent = JSON.stringify(data, null, 2);

            } catch (error) {
                document.getElementById('result-put').textContent = 'エラー: ' + error.message;
            }
        }

        /**
         * 商品削除
         */
        async function deleteProduct() {
            const id = parseInt(document.getElementById('delete-id').value);

            if (!confirm('本当に削除しますか？')) {
                return;
            }

            try {
                const response = await fetch(API_URL, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id
                    })
                });

                const data = await response.json();
                document.getElementById('result-delete').textContent = JSON.stringify(data, null, 2);

            } catch (error) {
                document.getElementById('result-delete').textContent = 'エラー: ' + error.message;
            }
        }
    </script>
</body>
</html>
```

**解説**：

✅ **ポイント**

- Fetch APIでHTTPリクエスト
- async/awaitで非同期処理
- try-catchでエラーハンドリング
- レスポンスをページに表示

---

## 🎓 まとめ

### 学んだこと

✅ **REST APIの実装**

- HTTPメソッドに応じた処理の分岐
- 適切なHTTPステータスコードの返却
- 統一されたJSON形式のレスポンス
- エラーハンドリング

✅ **JSONの扱い方**

- `json_encode()`：PHPからJSONへ
- `json_decode()`：JSONからPHPへ
- `file_get_contents('php://input')`：リクエストボディの取得

✅ **セキュリティ対策**

- プリペアドステートメント（SQLインジェクション対策）
- 入力検証（バリデーション）
- CORS設定
- API Key認証

✅ **実践的なテクニック**

- HTTPメソッドに応じた処理の分岐
- 検索機能の実装
- レートリミット
- JavaScriptからのAPI呼び出し

---

**Let's vibe and code! 🎉**

REST APIをマスターして、モダンなWebアプリケーション開発を楽しもう！
