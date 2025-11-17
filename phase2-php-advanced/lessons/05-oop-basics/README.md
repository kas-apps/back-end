# Lesson 05: オブジェクト指向プログラミング（OOP）の基礎 🏗️

**学習目標**：OOPの概念を理解し、クラスとオブジェクトを使ったコードを書けるようになる！

---

## 📖 このレッスンで学ぶこと

- クラスとオブジェクトの概念
- プロパティとメソッド
- コンストラクタ（__construct）
- アクセス修飾子（public、private、protected）
- 継承の基本（extends）
- 実用例：UserクラスProductクラス

---

## 🎯 なぜOOPを学ぶの？（Why）

### OOPは「設計図」と「製品」みたいなもの！

**設計図と製品の関係**：

- **設計図**：車の設計図（1つ）
- **製品**：その設計図から作られた車（たくさん）

**OOPの関係**：

- **クラス**：オブジェクトの設計図（1つ）
- **オブジェクト**：クラスから作られた実体（たくさん）

### なぜOOPが便利？

✨ **コードの再利用**：同じクラスから何個でもオブジェクトを作れる
✨ **保守性**：クラスを修正すれば、すべてのオブジェクトに反映される
✨ **AIとの協働**：「Userクラスを作って」と指示しやすい

---

## 💻 クラスとオブジェクトの基本

### クラスの定義

```php
<?php
// Userクラスの定義（設計図）
class User {
    // プロパティ（データ）
    public $name;
    public $email;
    public $age;

    // メソッド（機能）
    public function greet() {
        echo "こんにちは、" . $this->name . "です！";
    }
}
?>
```

### オブジェクトの作成

```php
<?php
// オブジェクトを作成（インスタンス化）
$user1 = new User();
$user1->name = "太郎";
$user1->email = "taro@example.com";
$user1->age = 25;

$user2 = new User();
$user2->name = "花子";
$user2->email = "hanako@example.com";
$user2->age = 30;

// メソッドを呼び出す
$user1->greet();  // こんにちは、太郎です！
$user2->greet();  // こんにちは、花子です！
?>
```

---

## 🔨 コンストラクタ

**コンストラクタ**：オブジェクト作成時に自動的に呼ばれる特別なメソッド

```php
<?php
class User {
    public $name;
    public $email;

    // コンストラクタ
    public function __construct($name, $email) {
        $this->name = $name;
        $this->email = $email;
    }

    public function introduce() {
        echo "{$this->name} ({$this->email})";
    }
}

// オブジェクト作成時に値を渡す
$user = new User("太郎", "taro@example.com");
$user->introduce();  // 太郎 (taro@example.com)
?>
```

---

## 🔒 アクセス修飾子

**カプセル化**：データを外部から保護する

```php
<?php
class User {
    private $password;  // 外部からアクセス不可

    public function setPassword($password) {
        // パスワードをハッシュ化して保存
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword($inputPassword) {
        return password_verify($inputPassword, $this->password);
    }
}

$user = new User();
$user->setPassword("secret123");

// $user->password にはアクセスできない（private）
// $user->password = "hack";  // エラー

// メソッド経由でのみアクセス
if ($user->verifyPassword("secret123")) {
    echo "パスワード正しい";
}
?>
```

**アクセス修飾子の種類**：

- `public`：どこからでもアクセス可能
- `private`：クラス内部からのみアクセス可能
- `protected`：クラス内部と継承したクラスからアクセス可能

---

## 🧬 継承

**継承**：既存のクラスを拡張して新しいクラスを作る

```php
<?php
// 親クラス
class User {
    public $name;

    public function __construct($name) {
        $this->name = $name;
    }

    public function greet() {
        echo "こんにちは、{$this->name}です";
    }
}

// 子クラス（AdminはUserを継承）
class Admin extends User {
    public function manageUsers() {
        echo "{$this->name}は管理者です";
    }
}

$admin = new Admin("管理者太郎");
$admin->greet();        // 親クラスのメソッド
$admin->manageUsers();  // 子クラスのメソッド
?>
```

---

## 💡 実用例

### Productクラス

```php
<?php
class Product {
    private $name;
    private $price;
    private $stock;

    public function __construct($name, $price, $stock) {
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function isInStock() {
        return $this->stock > 0;
    }

    public function purchase($quantity) {
        if ($quantity > $this->stock) {
            throw new Exception("在庫が不足しています");
        }
        $this->stock -= $quantity;
        return true;
    }
}

// 使用例
$product = new Product("りんご", 120, 50);

if ($product->isInStock()) {
    echo $product->getName() . " は在庫あり（" . $product->getPrice() . "円）";
}
?>
```

---

## 🤖 バイブコーディング実践

### AIへの指示例

**良い指示**：

```text
「ユーザー情報を管理するUserクラスを作ってください。
プロパティ：name（public）、email（public）、password（private）
メソッド：
- コンストラクタ（name、emailを受け取る）
- setPassword（パスワードをハッシュ化して保存）
- verifyPassword（パスワードを検証）
すべてのコードに日本語のコメントを入れてください。」
```

### コード品質チェック

- [ ] **カプセル化**：重要なデータはprivate
- [ ] **コンストラクタ**：初期化処理が適切
- [ ] **メソッド名**：わかりやすい名前
- [ ] **コメント**：クラスとメソッドに説明

---

## 💪 演習

👉 **[演習問題を見る](exercises/README.md)**

---

## ✅ まとめ

- ✅ クラス：オブジェクトの設計図
- ✅ オブジェクト：クラスから作られた実体
- ✅ コンストラクタ：`__construct()`
- ✅ アクセス修飾子：public、private、protected
- ✅ 継承：extendsで拡張

---

## 🚀 次のステップ

Phase 2のすべてのレッスンが完了！次は総合プロジェクトでこれまでの知識を統合しよう！

👉 **[総合プロジェクトを見る](../../projects/)**

---

**Let's vibe and code! 🎉**
