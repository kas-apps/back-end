# 補足: REST APIとJSON 🌐

**学習目標**：PHPでREST APIを設計・実装し、JSON形式でデータを送受信できるようになる！

---

## 📖 このレッスンで学ぶこと

- **REST APIとは何か**（RESTの原則、リソース指向）
- **JSONとは何か**（データ形式、PHP での扱い方）
- **HTTPメソッド**（GET、POST、PUT、DELETE）
- **HTTPステータスコード**（200、201、404、500など）
- **PHPでのJSON処理**（`json_encode()`、`json_decode()`）
- **REST APIの設計**（エンドポイント、リソース、レスポンス形式）
- **セキュリティ対策**（CORS、認証、入力検証）
- **実践的なREST API実装**（商品API、CRUD操作）

---

## 🎯 なぜREST APIを学ぶの？（Why）

### REST APIは現代のWebアプリケーションの基盤！

**従来のWebアプリケーション**：

```text
ブラウザ → PHPファイル → HTMLを生成して返す
→ ページ全体がリロードされる
```

**REST API + モダンWebアプリケーション**：

```text
フロントエンド（React、Vue.js）→ REST API → JSONデータを返す
                                      ↓
                            フロントエンドがデータを表示
                            （ページリロード不要！）
```

### REST APIができるようになると、こんなことが可能に！

✅ **モダンなWebアプリケーション開発**

- React、Vue.js、Angularなどのフロントエンドと連携
- SPA（Single Page Application）の構築
- リッチなユーザー体験の提供

✅ **マルチプラットフォーム対応**

- Webアプリ、スマホアプリ、IoTデバイスが同じAPIを使用
- 1つのAPIで複数のクライアントに対応

✅ **マイクロサービスアーキテクチャ**

- サービスを小さく分割して開発
- 疎結合な設計で保守性向上

### バックエンド開発における重要性

**REST APIは、フロントエンドとバックエンドを分離する架け橋**！

- 🌐 **フロントエンドとの分離**：フロントエンドとバックエンドを独立して開発
- 📱 **複数クライアント対応**：Web、iOS、Androidが同じAPIを使用
- 🔄 **再利用性**：APIを他のサービスから呼び出せる
- 🛠️ **保守性**：フロントエンドとバックエンドを独立して更新

---

## 🏗️ REST APIの基礎知識（What）

### RESTとは？

**REST（Representational State Transfer）**は、Webサービスの設計原則！

**RESTの6つの原則**：

1. **クライアント・サーバー**：役割を明確に分離
2. **ステートレス**：各リクエストは独立（セッションに依存しない）
3. **キャッシュ可能**：レスポンスをキャッシュできる
4. **統一インターフェース**：標準的なHTTPメソッドを使用
5. **階層化システム**：中間レイヤー（ロードバランサーなど）を透過的に配置可能
6. **コードオンデマンド**（オプション）：必要に応じてコードをダウンロード

### リソース指向アーキテクチャ

RESTでは、すべてのデータを**リソース**として扱う！

```text
リソース = データのまとまり（商品、ユーザー、記事など）
```

**例：商品管理API**

| リソース | エンドポイント | 説明 |
|---------|---------------|------|
| 商品一覧 | `/api/products` | すべての商品 |
| 特定の商品 | `/api/products/1` | ID=1の商品 |
| 商品カテゴリー | `/api/categories` | すべてのカテゴリー |

### HTTPメソッドとCRUD操作

RESTでは、HTTPメソッドでCRUD操作を表現する！

| HTTPメソッド | CRUD | 意味 | エンドポイント例 |
|------------|------|------|-----------------|
| **GET** | Read | データ取得 | `GET /api/products` |
| **POST** | Create | データ作成 | `POST /api/products` |
| **PUT** | Update | データ更新（全体） | `PUT /api/products/1` |
| **PATCH** | Update | データ更新（一部） | `PATCH /api/products/1` |
| **DELETE** | Delete | データ削除 | `DELETE /api/products/1` |

### HTTPステータスコード

レスポンスの状態を表す3桁の数字！

**成功（2xx）**：

- **200 OK**：成功（GET、PUT、PATCH）
- **201 Created**：作成成功（POST）
- **204 No Content**：成功（コンテンツなし、DELETE）

**クライアントエラー（4xx）**：

- **400 Bad Request**：リクエストが不正
- **401 Unauthorized**：認証が必要
- **403 Forbidden**：アクセス権限がない
- **404 Not Found**：リソースが見つからない
- **422 Unprocessable Entity**：バリデーションエラー

**サーバーエラー（5xx）**：

- **500 Internal Server Error**：サーバー内部エラー
- **503 Service Unavailable**：サービス利用不可

---

## 🔤 JSONの基礎知識（What）

### JSONとは？

**JSON（JavaScript Object Notation）**は、データ交換のための軽量なテキスト形式！

**特徴**：

- 📝 **人間が読みやすい**：テキスト形式で構造が明確
- 🚀 **軽量**：XMLより簡潔
- 🌐 **言語非依存**：JavaScript、PHP、Python、Javaなどあらゆる言語で使える
- 📊 **構造化データ**：配列、オブジェクト、入れ子に対応

### JSONの基本構文

**オブジェクト（連想配列）**：

```json
{
  "name": "MacBook Pro",
  "price": 198000,
  "stock": 10,
  "inStock": true
}
```

**配列**：

```json
[
  {"id": 1, "name": "MacBook Pro"},
  {"id": 2, "name": "iPhone 15"},
  {"id": 3, "name": "iPad Air"}
]
```

**入れ子構造**：

```json
{
  "product": {
    "id": 1,
    "name": "MacBook Pro",
    "category": {
      "id": 1,
      "name": "電子機器"
    },
    "reviews": [
      {"user": "太郎", "rating": 5},
      {"user": "花子", "rating": 4}
    ]
  }
}
```

### JSONのデータ型

| 型 | 説明 | 例 |
|----|------|-----|
| **string** | 文字列（ダブルクォートで囲む） | `"Hello"` |
| **number** | 数値（整数、小数） | `42`, `3.14` |
| **boolean** | 真偽値 | `true`, `false` |
| **null** | null値 | `null` |
| **array** | 配列 | `[1, 2, 3]` |
| **object** | オブジェクト | `{"key": "value"}` |

**⚠️ 注意点**：

- 文字列は**ダブルクォート**のみ（シングルクォートは不可）
- 最後の要素の後に**カンマをつけない**
- **コメント不可**
- キーは**必ず文字列**

---

## 💻 PHPでのJSON処理（How）

### json_encode() - PHPからJSONへ

**配列をJSON文字列に変換**：

```php
<?php
// 連想配列
$product = [
    'id' => 1,
    'name' => 'MacBook Pro',
    'price' => 198000,
    'stock' => 10
];

// JSONに変換
$json = json_encode($product);

echo $json;
// 出力: {"id":1,"name":"MacBook Pro","price":198000,"stock":10}
?>
```

**配列の配列をJSONへ**：

```php
<?php
$products = [
    ['id' => 1, 'name' => 'MacBook Pro', 'price' => 198000],
    ['id' => 2, 'name' => 'iPhone 15', 'price' => 118800],
    ['id' => 3, 'name' => 'iPad Air', 'price' => 84800]
];

$json = json_encode($products);

echo $json;
// 出力: [{"id":1,"name":"MacBook Pro","price":198000},...
?>
```

**オプション - 読みやすいJSON**：

```php
<?php
$product = ['id' => 1, 'name' => 'MacBook Pro'];

// JSON_PRETTY_PRINT: 改行・インデントで整形
// JSON_UNESCAPED_UNICODE: 日本語をエスケープしない
$json = json_encode($product, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

echo $json;
/*
出力:
{
    "id": 1,
    "name": "MacBook Pro"
}
*/
?>
```

### json_decode() - JSONからPHPへ

**JSON文字列をPHPの配列に変換**：

```php
<?php
$json = '{"id":1,"name":"MacBook Pro","price":198000}';

// 連想配列に変換（第2引数 true）
$product = json_decode($json, true);

echo $product['name'];  // MacBook Pro
echo $product['price']; // 198000
?>
```

**JSON文字列をPHPのオブジェクトに変換**：

```php
<?php
$json = '{"id":1,"name":"MacBook Pro","price":198000}';

// オブジェクトに変換（第2引数なし、またはfalse）
$product = json_decode($json);

echo $product->name;  // MacBook Pro
echo $product->price; // 198000
?>
```

**エラーハンドリング**：

```php
<?php
$json = '{"invalid json}';  // 不正なJSON

$product = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo "JSONエラー: " . json_last_error_msg();
    // 出力: JSONエラー: Syntax error
}
?>
```

---

## 💻 REST APIの実装（How）

### ステップ1：基本的なAPIの構造

**api/products.php**（商品一覧を取得するAPI）：

```php
<?php
// config.phpを読み込み（データベース接続）
require_once '../config.php';

// レスポンスヘッダーを設定
header('Content-Type: application/json; charset=utf-8');

try {
    // 商品一覧を取得
    $stmt = $pdo->query("SELECT id, name, price, stock FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 成功レスポンス
    http_response_code(200); // 200 OK
    echo json_encode([
        'success' => true,
        'data' => $products
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    // エラーレスポンス
    http_response_code(500); // 500 Internal Server Error
    echo json_encode([
        'success' => false,
        'message' => 'データベースエラーが発生しました。'
    ], JSON_UNESCAPED_UNICODE);
}
?>
```

**解説**：

✅ **ヘッダー設定**

- `Content-Type: application/json`：レスポンスがJSONであることを明示
- `charset=utf-8`：日本語対応

✅ **レスポンス構造**

- `success`：成功/失敗を示すフラグ
- `data`：取得したデータ
- `message`：エラーメッセージ

✅ **HTTPステータスコード**

- `http_response_code(200)`：成功
- `http_response_code(500)`：サーバーエラー

### ステップ2：HTTPメソッドに応じた処理

**api/products.php**（CRUD操作を実装）：

```php
<?php
require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

// HTTPメソッドを取得
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // 商品一覧取得 または 特定商品取得
            handleGet($pdo);
            break;

        case 'POST':
            // 商品作成
            handlePost($pdo);
            break;

        case 'PUT':
            // 商品更新
            handlePut($pdo);
            break;

        case 'DELETE':
            // 商品削除
            handleDelete($pdo);
            break;

        default:
            // サポートされていないメソッド
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

    if ($id) {
        // 特定商品を取得
        $stmt = $pdo->prepare("SELECT id, name, description, price, stock FROM products WHERE id = :id");
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
    } else {
        // 商品一覧を取得
        $stmt = $pdo->query("SELECT id, name, description, price, stock FROM products ORDER BY created_at DESC");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'data' => $products,
            'count' => count($products)
        ], JSON_UNESCAPED_UNICODE);
    }
}

/**
 * POST: 商品作成
 */
function handlePost($pdo) {
    // リクエストボディからJSONを取得
    $input = json_decode(file_get_contents('php://input'), true);

    // バリデーション
    if (empty($input['name']) || !isset($input['price'])) {
        http_response_code(400); // 400 Bad Request
        echo json_encode([
            'success' => false,
            'message' => '商品名と価格は必須です。'
        ], JSON_UNESCAPED_UNICODE);
        return;
    }

    $name = $input['name'];
    $description = $input['description'] ?? '';
    $price = $input['price'];
    $stock = $input['stock'] ?? 0;

    // 商品を作成
    $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock) VALUES (:name, :description, :price, :stock)");
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':price' => $price,
        ':stock' => $stock
    ]);

    $lastId = $pdo->lastInsertId();

    http_response_code(201); // 201 Created
    echo json_encode([
        'success' => true,
        'message' => '商品を作成しました。',
        'data' => [
            'id' => $lastId,
            'name' => $name,
            'price' => $price
        ]
    ], JSON_UNESCAPED_UNICODE);
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

    $id = $input['id'];
    $name = $input['name'] ?? null;
    $description = $input['description'] ?? null;
    $price = $input['price'] ?? null;
    $stock = $input['stock'] ?? null;

    // 更新
    $stmt = $pdo->prepare("
        UPDATE products
        SET name = COALESCE(:name, name),
            description = COALESCE(:description, description),
            price = COALESCE(:price, price),
            stock = COALESCE(:stock, stock)
        WHERE id = :id
    ");
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':price' => $price,
        ':stock' => $stock,
        ':id' => $id
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

    $id = $input['id'];

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);

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
}
?>
```

**解説**：

✅ **HTTPメソッドに応じた処理**

- `$_SERVER['REQUEST_METHOD']`でHTTPメソッドを取得
- switch文で処理を分岐

✅ **リクエストボディの取得**

- `file_get_contents('php://input')`でリクエストボディを取得
- `json_decode()`でPHPの配列に変換

✅ **適切なステータスコード**

- 201 Created：POST成功
- 400 Bad Request：バリデーションエラー
- 404 Not Found：リソースが見つからない

---

## 🔒 セキュリティ対策（Security）

### 1. CORS（Cross-Origin Resource Sharing）

異なるドメインからのAPIアクセスを許可する設定！

```php
<?php
// CORSヘッダーを設定（すべてのドメインを許可）
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// OPTIONSリクエストの処理（プリフライトリクエスト）
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 以下、通常のAPI処理...
?>
```

**⚠️ セキュリティ上の注意**：

- 本番環境では`*`ではなく、**特定のドメインのみ許可**する
- 例：`header('Access-Control-Allow-Origin: https://example.com');`

### 2. 入力検証（バリデーション）

すべての入力値をチェックする！

```php
<?php
function validateProduct($data) {
    $errors = [];

    // 商品名チェック
    if (empty($data['name'])) {
        $errors[] = '商品名は必須です。';
    } elseif (mb_strlen($data['name']) > 255) {
        $errors[] = '商品名は255文字以内で入力してください。';
    }

    // 価格チェック
    if (!isset($data['price'])) {
        $errors[] = '価格は必須です。';
    } elseif (!is_numeric($data['price']) || $data['price'] < 0) {
        $errors[] = '価格は0以上の数値で入力してください。';
    }

    // 在庫チェック
    if (isset($data['stock']) && (!is_numeric($data['stock']) || $data['stock'] < 0)) {
        $errors[] = '在庫は0以上の整数で入力してください。';
    }

    return $errors;
}

// 使用例
$input = json_decode(file_get_contents('php://input'), true);
$errors = validateProduct($input);

if (!empty($errors)) {
    http_response_code(422); // 422 Unprocessable Entity
    echo json_encode([
        'success' => false,
        'message' => 'バリデーションエラー',
        'errors' => $errors
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
?>
```

### 3. 認証（API Key、JWT）

**API Key認証**：

```php
<?php
// API Keyをチェック
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($apiKey !== 'your-secret-api-key') {
    http_response_code(401); // 401 Unauthorized
    echo json_encode([
        'success' => false,
        'message' => '認証に失敗しました。'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 以下、通常のAPI処理...
?>
```

**クライアント側からのAPI Key送信例（JavaScript）**：

```javascript
fetch('http://localhost:8888/api/products.php', {
  method: 'GET',
  headers: {
    'X-API-KEY': 'your-secret-api-key'
  }
})
.then(response => response.json())
.then(data => console.log(data));
```

### 4. レートリミット（アクセス制限）

過度なリクエストを防ぐ！

```php
<?php
session_start();

// 現在時刻
$now = time();

// セッションに最終アクセス時刻を保存
if (!isset($_SESSION['api_last_access'])) {
    $_SESSION['api_last_access'] = $now;
    $_SESSION['api_request_count'] = 0;
}

// 1分以内のリクエスト数をカウント
if ($now - $_SESSION['api_last_access'] < 60) {
    $_SESSION['api_request_count']++;

    // 1分間に10回を超えたら拒否
    if ($_SESSION['api_request_count'] > 10) {
        http_response_code(429); // 429 Too Many Requests
        echo json_encode([
            'success' => false,
            'message' => 'リクエスト数が多すぎます。しばらく待ってから再試行してください。'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
} else {
    // 1分経過したらリセット
    $_SESSION['api_last_access'] = $now;
    $_SESSION['api_request_count'] = 1;
}

// 以下、通常のAPI処理...
?>
```

---

## 🤖 バイブコーディング実践

### AIへの指示例

**良い指示の例**：

```text
✅ 具体的でセキュアな指示：

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
- 適切なHTTPステータスコードを返す（200, 201, 400, 404, 500）
- CORSヘッダーを設定
- json_encode()にJSON_UNESCAPED_UNICODEを指定

技術要件：
- PDOを使用
- Content-Typeヘッダーを application/json に設定
- file_get_contents('php://input') でリクエストボディを取得
- try-catchでエラーハンドリング」
```

### 生成されたコードのチェックポイント

✅ **セキュリティチェック（最優先）**

**SQLインジェクション対策**：

- [ ] すべてのSQL文でプリペアドステートメントを使用
- [ ] ユーザー入力を直接SQL文に埋め込んでいない

**バリデーション**：

- [ ] すべての入力値をチェックしている
- [ ] 空チェック、型チェック、範囲チェックを実施

**CORS**：

- [ ] CORSヘッダーを設定している
- [ ] 本番環境では特定のドメインのみ許可

✅ **API設計チェック**

**HTTPメソッド**：

- [ ] GET：データ取得のみ（副作用なし）
- [ ] POST：データ作成
- [ ] PUT/PATCH：データ更新
- [ ] DELETE：データ削除

**HTTPステータスコード**：

- [ ] 200 OK：成功（GET、PUT）
- [ ] 201 Created：作成成功（POST）
- [ ] 400 Bad Request：バリデーションエラー
- [ ] 404 Not Found：リソースが見つからない
- [ ] 500 Internal Server Error：サーバーエラー

**レスポンス形式**：

- [ ] JSON形式で統一されている
- [ ] `success`フラグがある
- [ ] エラー時に`message`がある

✅ **コード品質チェック**

- [ ] `Content-Type: application/json`ヘッダーを設定
- [ ] `json_encode()`に`JSON_UNESCAPED_UNICODE`を指定
- [ ] try-catchでエラーハンドリング
- [ ] 関数で処理を分離している

### よくあるAI生成コードの問題と修正

**問題1: Content-Typeヘッダーがない**

```php
// ❌ 悪い例（ヘッダーなし）
echo json_encode($data);
```

**修正**：

```php
// ✅ 良い例（ヘッダー設定）
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
```

**問題2: リクエストボディの取得方法が間違っている**

```php
// ❌ 悪い例（$_POSTは使えない）
$input = $_POST; // PUT、DELETEでは使えない！
```

**修正**：

```php
// ✅ 良い例（php://input）
$input = json_decode(file_get_contents('php://input'), true);
```

**問題3: HTTPステータスコードがない**

```php
// ❌ 悪い例（常に200）
echo json_encode(['success' => false, 'message' => 'エラー']);
```

**修正**：

```php
// ✅ 良い例（適切なステータスコード）
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'エラー'], JSON_UNESCAPED_UNICODE);
```

---

## 🎓 まとめ

このレッスンで学んだこと：

✅ **REST APIの基礎**

- RESTとは何か（リソース指向、ステートレス）
- HTTPメソッドとCRUD操作の対応
- HTTPステータスコードの意味と使い分け

✅ **JSONの扱い方**

- JSONとは何か（軽量なデータ交換形式）
- `json_encode()`：PHPからJSONへ
- `json_decode()`：JSONからPHPへ

✅ **REST APIの実装**

- HTTPメソッドに応じた処理の分岐
- リクエストボディの取得（`php://input`）
- 適切なレスポンス形式（JSON）
- ステータスコードの設定

✅ **セキュリティ対策**

- CORS設定（異なるドメインからのアクセス許可）
- 入力検証（バリデーション）
- 認証（API Key）
- レートリミット（アクセス制限）

✅ **バイブコーディング**

- AIに REST APIを生成させる指示方法
- 生成されたコードのセキュリティチェック
- よくある問題の発見と修正

### 次のステップ

REST APIをマスターしたら、実際のプロジェクトで使ってみよう！

👉 **[演習問題を見る](exercises/README.md)**

REST APIを実装して、モダンなWebアプリケーションを構築しよう！

---

**Let's vibe and code! 🎉**

REST APIができれば、フロントエンドとバックエンドを分離して、モダンなWebアプリケーションが作れる！次は実際に手を動かして、APIを作ってみよう！
