# Lesson 05: OOP基礎 - 解答例 ✅

## 演習 05-01: Productクラスの作成

### ポイント

```php
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
}
```

---

## 演習 05-02: Userクラスの作成

### セキュリティポイント

```php
class User {
    public $name;
    public $email;
    private $password;

    public function __construct($name, $email) {
        $this->name = $name;
        $this->email = $email;
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword($inputPassword) {
        return password_verify($inputPassword, $this->password);
    }
}
```

---

## 演習 05-03: カプセル化の修正

### 修正後

```php
class User {
    private $password;  // ✅ privateに変更

    public function setPassword($password) {
        // ✅ ハッシュ化して保存
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
}
```

---

**Let's vibe and code! 🎉**
