# Lesson 02: プリペアドステートメント 🔒

**学習目標**：SQLインジェクション攻撃を理解し、プリペアドステートメントで完璧に防御できるようになる！

---

## 📖 このレッスンで学ぶこと

- **SQLインジェクション**とは何か（攻撃例と被害）
- プリペアドステートメントの仕組み（なぜ安全なのか）
- **prepare() → bindParam() → execute()**の基本フロー
- 名前付きプレースホルダー（**:name**）と疑問符プレースホルダー（**?**）
- プリペアドステートメントでのCRUD操作
- **fetch()、fetchAll()、lastInsertId()、rowCount()**の使い方
- バインドの型指定（PDO::PARAM_INT、PDO::PARAM_STR）

---

## 🎯 なぜプリペアドステートメントを学ぶの？（Why）

### SQLインジェクションは最も危険な脆弱性の1つ！

**OWASP Top 10**（最も危険なWebアプリケーション脆弱性トップ10）で、SQLインジェクションは常に上位にランクイン！

**SQLインジェクション攻撃を受けると...**：

🚨 **データベース全体が破壊される**
- すべてのデータが削除される（`DROP TABLE users;`）
- データが改ざんされる（管理者権限を不正取得）

🚨 **個人情報が流出する**
- ユーザーのパスワードが盗まれる
- クレジットカード情報が漏洩する
- メールアドレスが流出する

🚨 **サービスが停止する**
- データベースがクラッシュする
- 復旧に数日〜数週間かかる

### 実際の被害例

**某ECサイト（2019年）**：
- SQLインジェクション攻撃でクレジットカード情報10万件流出
- 損害賠償数億円
- サービス停止

**某会員サイト（2021年）**：
- 会員情報100万件流出
- 企業の信頼が失墜

**プリペアドステートメントを使えば、これらの攻撃を完全に防げる！**

### バックエンド開発における重要性

プリペアドステートメントは、**バックエンド開発の必須スキル**！

- 🔒 **セキュリティの要**：すべてのSQL操作で使用必須
- 🛡️ **防弾チョッキ**：SQLインジェクション攻撃を完全に防ぐ
- 📜 **業界標準**：プロのエンジニアは必ず使う
- 🤖 **AIへの指示**：「プリペアドステートメントで書いて」と必ず指示する

---

## 🏗️ SQLインジェクションとは？（What）

### SQLインジェクション攻撃の仕組み

**脆弱なコード例**：

```php
<?php
// 🚨 超危険！絶対にやってはいけない！
$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $pdo->query($sql);
$user = $result->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "ようこそ、" . $user['name'] . "さん！";
}
?>
```

**通常の使用（正常なユーザー）**：

```text
入力: taro@example.com

実行されるSQL:
SELECT * FROM users WHERE email = 'taro@example.com'
→ 正常に動作
```

**攻撃例1: ログイン回避**

```text
攻撃者の入力: ' OR '1'='1

実行されるSQL:
SELECT * FROM users WHERE email = '' OR '1'='1'
                                       ↑ 常にtrue！

→ すべてのユーザー情報が取得される（ログイン回避成功）
```

**攻撃例2: データベース破壊**

```text
攻撃者の入力: '; DROP TABLE users; --

実行されるSQL:
SELECT * FROM users WHERE email = ''; DROP TABLE users; --'
                                       ↑ usersテーブルを削除！

→ データベース全体が破壊される
```

**攻撃例3: 個人情報流出**

```text
攻撃者の入力: ' UNION SELECT id, password, email FROM users --

実行されるSQL:
SELECT * FROM users WHERE email = '' UNION SELECT id, password, email FROM users --'
                                       ↑ 全ユーザーのパスワードを取得！

→ すべてのユーザーのパスワードが流出
```

### なぜこんなことが起きる？

**SQL文と変数を文字列結合しているから！**

```php
// 🚨 危険な理由
$sql = "SELECT * FROM users WHERE email = '$email'";
//                                         ↑ ここに攻撃者の入力がそのまま入る！
```

**攻撃者の入力がSQLの一部として解釈される**：

```text
SELECT * FROM users WHERE email = '' OR '1'='1'
                                  ↑ここまでが1つの文字列
                                     ↑ ORはSQL文として解釈される！
```

---

## 💻 プリペアドステートメントの仕組み（How）

### プリペアドステートメントとは？

**プリペアドステートメント**は、「SQL文とデータを分離する」技術！

**アナロジー：テンプレートと穴埋め**

```text
通常のSQL（危険）：
「太郎さんのメールアドレスを検索して」← 文章全体を解釈

プリペアドステートメント（安全）：
テンプレート: 「【　　】さんのメールアドレスを検索して」
データ: 「太郎」
→ 【　　】の部分にはデータしか入らない（命令は入らない）
```

### 3ステップの基本フロー

**1. prepare()：SQL文のテンプレートを準備**

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
//                                                      ↑ プレースホルダー
```

**2. bindParam()：データをバインド**

```php
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
//                ↑ プレースホルダー名
//                         ↑ 変数
//                                 ↑ データ型
```

**3. execute()：実行**

```php
$stmt->execute();
```

### なぜ安全なのか？

**プリペアドステートメントでは、データは「データ」としてのみ扱われる**：

```text
攻撃者の入力: ' OR '1'='1

プリペアドステートメントでは:
SELECT * FROM users WHERE email = '\' OR \'1\'=\'1'
                                   ↑ 文字列としてエスケープされる

→ 「' OR '1'='1」という名前のメールアドレスを探すだけ
→ 攻撃失敗！
```

---

## 💻 プリペアドステートメントの基本（How）

### 基本的な使い方（名前付きプレースホルダー）

```php
<?php
require_once 'config.php';

// ユーザー入力を受け取る
$email = $_POST['email'] ?? '';

try {
    // 1. prepare()：SQL文のテンプレートを準備
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");

    // 2. bindParam()：データをバインド
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);

    // 3. execute()：実行
    $stmt->execute();

    // 4. データを取得
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "ようこそ、" . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "さん！";
    } else {
        echo "ユーザーが見つかりませんでした。";
    }

} catch (PDOException $e) {
    echo "エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**ポイント**：

- `:email`は名前付きプレースホルダー
- `bindParam()`で変数とプレースホルダーを紐付ける
- `PDO::PARAM_STR`で文字列型を指定

### 疑問符プレースホルダー（?）

名前付きプレースホルダーの代わりに`?`を使うこともできる：

```php
<?php
require_once 'config.php';

$email = $_POST['email'] ?? '';

try {
    // ?プレースホルダーを使用
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");

    // 位置でバインド（1から始まる）
    $stmt->bindParam(1, $email, PDO::PARAM_STR);

    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "ようこそ、" . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "さん！";
    }

} catch (PDOException $e) {
    echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**名前付き vs 疑問符**：

| 名前付き（:name） | 疑問符（?） |
|-------------------|-------------|
| 分かりやすい | シンプル |
| パラメータが多い時に便利 | パラメータが少ない時に便利 |
| 順序を気にしなくていい | 順序が重要 |

**推奨**: パラメータが2個以上なら名前付きプレースホルダー！

### execute()に配列を渡す方法（簡潔）

`bindParam()`を省略して、`execute()`に配列で渡すこともできる：

```php
<?php
require_once 'config.php';

$email = $_POST['email'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");

    // execute()に連想配列で渡す
    $stmt->execute([':email' => $email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**疑問符の場合**：

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]); // 配列で渡す（キーなし）
```

---

## 📝 CRUD操作でのプリペアドステートメント

### CREATE（INSERT）

```php
<?php
require_once 'config.php';

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';

try {
    $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");

    $stmt->execute([
        ':name' => $name,
        ':email' => $email
    ]);

    // 挿入したIDを取得
    $lastId = $pdo->lastInsertId();

    echo "ユーザー登録成功！（ID: {$lastId}）";

} catch (PDOException $e) {
    echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**lastInsertId()**：
- AUTO_INCREMENTで自動生成されたIDを取得
- INSERT直後に呼び出す

### READ（SELECT）

**1件取得（fetch()）**：

```php
<?php
require_once 'config.php';

$id = $_GET['id'] ?? 0;
$id = (int)$id; // 整数型にキャスト

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // 1件取得
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo "名前: " . htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8');
    } else {
        echo "ユーザーが見つかりませんでした。";
    }

} catch (PDOException $e) {
    echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**全件取得（fetchAll()）**：

```php
<?php
require_once 'config.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC");
    $stmt->execute();

    // 全件取得
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "全" . count($users) . "件<br>";

    foreach ($users as $user) {
        echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') . "<br>";
    }

} catch (PDOException $e) {
    echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**fetch() vs fetchAll()**：

| fetch() | fetchAll() |
|---------|-----------|
| 1件だけ取得 | 全件を配列で取得 |
| メモリ効率が良い | すべてメモリに読み込む |
| 結果が1件の時 | 一覧表示の時 |

### UPDATE

```php
<?php
require_once 'config.php';

$id = $_POST['id'] ?? 0;
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';

$id = (int)$id;

try {
    $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");

    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':id' => $id
    ]);

    // 更新された行数を取得
    $rowCount = $stmt->rowCount();

    if ($rowCount > 0) {
        echo "更新成功！（{$rowCount}件）";
    } else {
        echo "更新されませんでした（該当データなし、または変更なし）。";
    }

} catch (PDOException $e) {
    echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**rowCount()**：
- UPDATE、DELETEで影響を受けた行数を取得
- 更新が成功したか確認できる

### DELETE

```php
<?php
require_once 'config.php';

$id = $_POST['id'] ?? 0;
$id = (int)$id;

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $rowCount = $stmt->rowCount();

    if ($rowCount > 0) {
        echo "削除成功！（{$rowCount}件）";
    } else {
        echo "削除対象が見つかりませんでした。";
    }

} catch (PDOException $e) {
    echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>
```

**重要**: DELETE、UPDATEには必ずWHERE句を付ける！

---

## 🔢 バインドの型指定

`bindParam()`の第3引数で、データ型を指定できる：

```php
// 文字列型
$stmt->bindParam(':name', $name, PDO::PARAM_STR);

// 整数型
$stmt->bindParam(':age', $age, PDO::PARAM_INT);

// 真偽値（boolean）
$stmt->bindParam(':active', $active, PDO::PARAM_BOOL);

// NULL
$stmt->bindParam(':deleted_at', $deletedAt, PDO::PARAM_NULL);
```

**型指定のメリット**：

✅ **セキュリティ向上**：期待する型以外を受け付けない
✅ **バリデーション**：整数型を指定すれば、文字列は自動的に整数に変換される
✅ **明示的**：コードを読む人が期待する型を理解できる

**例**：

```php
<?php
// IDは整数型として厳密にバインド
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
?>
```

---

## 🤖 バイブコーディング実践

### AIへの指示例

**SELECTのプリペアドステートメント**：

```text
✅ 良い指示：

「プリペアドステートメントを使って、ユーザーをメールアドレスで検索するPHPコードを書いてください。

要件：
- prepare()、execute()を使用
- 名前付きプレースホルダー（:email）を使用
- fetch()で1件取得
- try-catchでエラーハンドリング
- 結果をhtmlspecialchars()でXSS対策して表示
- ユーザーが見つからない場合のメッセージも表示」
```

**INSERTのプリペアドステートメント**：

```text
✅ 良い指示：

「プリペアドステートメントを使って、ユーザー登録を実装してください。

要件：
- フォームから名前とメールアドレスを受け取る
- prepare()でINSERT文を準備
- バリデーション（空チェック）
- 登録成功時にlastInsertId()で登録IDを表示
- UNIQUE制約違反（メールアドレス重複）のエラーハンドリング
- try-catchでPDOExceptionをキャッチ」
```

### 生成されたコードのチェックポイント

✅ **セキュリティチェック**
- [ ] プリペアドステートメントを使っている（prepare()）
- [ ] 直接SQL文に変数を埋め込んでいない
- [ ] bindParam()またはexecute([])でバインドしている
- [ ] htmlspecialchars()でXSS対策している

✅ **コードの正しさチェック**
- [ ] prepare() → execute()の順序が正しい
- [ ] プレースホルダー名とバインドする変数名が一致している
- [ ] fetch()とfetchAll()を適切に使い分けている
- [ ] エラーハンドリング（try-catch）がある

✅ **ベストプラクティスチェック**
- [ ] 型指定（PDO::PARAM_INT、PDO::PARAM_STR）をしている
- [ ] rowCount()で影響行数を確認している（UPDATE、DELETE）
- [ ] lastInsertId()で挿入IDを取得している（INSERT）

### よくあるAI生成コードの問題と修正

**問題1: 直接SQL文に変数を埋め込んでいる（危険！）**

```php
// ❌ 超危険！
$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $pdo->query($sql);
```

**修正**：

```php
// ✅ 安全！
$email = $_POST['email'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([':email' => $email]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
```

**AIへの修正指示**：
```text
「SQLインジェクション脆弱性があります。プリペアドステートメントに書き換えてください。」
```

**問題2: bindParam()とexecute()の順序が逆**

```php
// ❌ 間違い
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute();
$stmt->bindParam(':email', $email, PDO::PARAM_STR); // 遅い！
```

**修正**：

```php
// ✅ 正しい
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->execute();
```

**問題3: プレースホルダーとバインド変数が一致していない**

```php
// ❌ 間違い
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([':mail' => $email]); // :emailではなく:mail（エラー）
```

**修正**：

```php
// ✅ 正しい
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([':email' => $email]); // 名前が一致
```

---

## 💡 よくあるエラーと解決方法

### エラー1: "Invalid parameter number: parameter was not defined"

**原因**：プレースホルダー名とバインド変数名が一致していない

```php
// ❌ 間違い
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute([':id' => $id]); // :user_idではなく:id
```

**解決**：

```php
// ✅ 正しい
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute([':user_id' => $id]); // 名前を一致させる
```

### エラー2: "SQLSTATE[HY093]: Invalid parameter number"

**原因**：プレースホルダーの数とバインドする値の数が一致していない

```php
// ❌ 間違い
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
$stmt->execute([':name' => $name]); // :emailがない
```

**解決**：

```php
// ✅ 正しい
$stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
$stmt->execute([':name' => $name, ':email' => $email]); // 両方指定
```

### エラー3: データが取得できない（fetch()がfalseを返す）

**原因1**：該当するデータがない
**原因2**：execute()を忘れている

```php
// ❌ 間違い
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
// execute()を忘れた！
$user = $stmt->fetch(); // false
```

**解決**：

```php
// ✅ 正しい
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute(); // 忘れずに実行！
$user = $stmt->fetch();
```

---

## 🎓 まとめ

このレッスンで学んだこと：

✅ **SQLインジェクションの危険性**
- データベース破壊、個人情報流出の危険
- 実際の被害例
- OWASP Top 10の脆弱性

✅ **プリペアドステートメントの仕組み**
- SQL文とデータを分離
- prepare() → bindParam() → execute()の3ステップ
- なぜ安全なのか

✅ **実践的な使い方**
- 名前付きプレースホルダー（:name）と疑問符（?）
- CRUD操作すべてで使用
- fetch()、fetchAll()、lastInsertId()、rowCount()

✅ **バイブコーディング**
- AIにセキュアなコードを指示する方法
- 生成されたコードのセキュリティチェック
- よくある問題の発見と修正

### 重要なポイント

🔒 **プリペアドステートメントは絶対に必須！**

すべてのSQL操作で、必ずプリペアドステートメントを使おう。「動けばOK」ではなく、「セキュアに動く」を目指そう！

**AIと協働する時も、必ず「プリペアドステートメントで書いて」と指示する！**

### 次のステップ

プリペアドステートメントをマスターしたら、次は**CRUD操作の実装**を学ぼう！

👉 **[Lesson 03: CRUD操作の実装](../03-crud-operations/README.md)**

フォームからデータを受け取って、データベースに保存・更新・削除する実践的なコードを学ぼう！

👉 **[演習問題を見る](exercises/README.md)**

---

**Let's vibe and code! 🎉**

プリペアドステートメントでSQLインジェクション攻撃を完全に防ごう！セキュアなバックエンドエンジニアへの第一歩だ！
