# Lesson 05: 高度なテーブル設計 🏗️

**学習目標**：多対多の関係や正規化を理解し、実践的なテーブル設計ができるようになる！

---

## 📖 このレッスンで学ぶこと

- 正規化の復習と発展（第1、第2、第3正規形）
- 多対多の関係（中間テーブルの設計）
- 正規化と非正規化のバランス（パフォーマンスとのトレードオフ）
- 大規模データベース設計の考え方（スケーラビリティ）
- 実用的な設計パターン（タグ付け、権限管理、コメントのネスト）

---

## 🎯 なぜ高度なテーブル設計を学ぶの？（Why）

### 多対多の関係とは？

**実際のニーズ**：

- 「学生と授業」：1人の学生は複数の授業を履修、1つの授業には複数の学生
- 「商品とタグ」：1つの商品に複数のタグ、1つのタグには複数の商品
- 「ユーザーとロール」：1人のユーザーは複数のロール、1つのロールには複数のユーザー

Phase 3で学んだ1対多の関係では表現できない！

### 中間テーブルの登場

**多対多の関係**は、**中間テーブル**を使って表現する！

```text
学生テーブル    ←→    学生_授業テーブル（中間）    ←→    授業テーブル
```

---

## 💻 実例で理解しよう（How）

### 実例1：学生と授業の多対多

**studentsテーブル**：

```sql
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

INSERT INTO students (name) VALUES ('太郎'), ('花子'), ('次郎');
```

**coursesテーブル**：

```sql
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

INSERT INTO courses (name) VALUES ('数学'), ('英語'), ('物理');
```

**中間テーブル（student_courses）**：

```sql
CREATE TABLE student_courses (
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (student_id, course_id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

INSERT INTO student_courses (student_id, course_id) VALUES
(1, 1),  -- 太郎は数学を履修
(1, 2),  -- 太郎は英語を履修
(2, 1),  -- 花子は数学を履修
(2, 3),  -- 花子は物理を履修
(3, 2);  -- 次郎は英語を履修
```

**ポイント**：

- 中間テーブルで多対多の関係を表現
- `PRIMARY KEY (student_id, course_id)`：複合主キー
- 外部キー制約で整合性を保証

---

### 実例2：商品とタグの多対多

**productsテーブル**：

```sql
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10, 2) NOT NULL
);
```

**tagsテーブル**：

```sql
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);
```

**中間テーブル（product_tags）**：

```sql
CREATE TABLE product_tags (
    product_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (product_id, tag_id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (tag_id) REFERENCES tags(id)
);
```

---

### 実例3：多対多のデータ取得

**クエリ**：

```sql
-- 太郎さんが履修している授業を取得
SELECT
    s.name AS student_name,
    c.name AS course_name
FROM
    students AS s
INNER JOIN
    student_courses AS sc ON s.id = sc.student_id
INNER JOIN
    courses AS c ON sc.course_id = c.id
WHERE
    s.name = '太郎';
```

**ポイント**：

- 中間テーブルを経由してJOIN
- Lesson 01で学んだJOINが活きる！

---

## 🧩 正規化の復習と発展

### 第1正規形（Phase 3で学んだ）

- 各カラムに1つの値のみ
- 繰り返しのグループがない

### 第2正規形

- 第1正規形を満たす
- 部分関数従属を排除

### 第3正規形

- 第2正規形を満たす
- 推移的関数従属を排除

**実務では**：

- 第3正規形まで正規化することが多い
- 過度な正規化はパフォーマンス低下の原因にも

---

## 🤖 バイブコーディング実践

### AIへの指示例

```text
「学生と授業の多対多の関係を表現するテーブル設計をしてください。
studentsテーブル、coursesテーブル、student_coursesテーブル（中間テーブル）を作成してください。
外部キー制約と複合主キーを含めてください。」
```

### チェックポイント

- [ ] 中間テーブルが作成されているか
- [ ] 複合主キーが設定されているか
- [ ] 外部キー制約が設定されているか
- [ ] データの重複がないか

### よくある問題

**問題**：中間テーブルを作らずに直接関連付ける

**修正**：

- 必ず中間テーブルを作成
- 複合主キーで重複を防ぐ

---

## ✅ まとめ

- ✅ 多対多の関係は中間テーブルで表現
- ✅ 複合主キーと外部キー制約
- ✅ 正規化と非正規化のバランス
- ✅ JOINで中間テーブルを経由

---

## 🚀 次のステップ

Phase 4の総合プロジェクトにチャレンジしよう！

学んだJOIN、集計関数、インデックス、トランザクション、多対多設計を全部使って、実践的なデータベースを構築！

👉 **[総合プロジェクト: ecommerce-dbへ進む](../../projects/ecommerce-db/README.md)**

---

**Let's vibe and code! 🎉**

高度なテーブル設計で、スケーラブルなデータベースを作ろう！
