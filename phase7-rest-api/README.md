# Phase 7: REST API開発 - モダンなバックエンドAPI構築 🌐

**学習目標**：PHPでREST APIを設計・実装し、JSON形式でデータを送受信する、モダンなバックエンドAPIを構築できるようになる！

---

## 📖 このPhaseで学ぶこと

Phase 7では、**Phase 5で学んだPHP + MySQL統合**をさらに発展させて、**REST API**を構築します！

- **REST APIの基礎**：RESTの原則、リソース指向アーキテクチャ
- **JSONデータ形式**：`json_encode()`と`json_decode()`の使い方
- **HTTPメソッド**：GET、POST、PUT、DELETEの使い分け
- **HTTPステータスコード**：200、201、400、404、500などの適切な返却
- **API設計**：エンドポイント設計、レスポンス形式の統一
- **セキュリティ対策**：CORS設定、API Key認証、レートリミット🔒
- **実践的なAPI実装**：商品管理API、CRUD操作のAPI化

**Phase 5との関係**：

- **Phase 5（PHP + MySQL統合）**：データベース接続、CRUD操作、セキュリティ対策
- **→ Phase 7でCRUD操作をAPI化！フロントエンドとバックエンドを分離！**

---

## 🎯 Phase 7の全体像

### なぜREST APIを学ぶの？

これまで、PHPでHTMLを生成して返すWebアプリケーションを作ってきたね！でも、**モダンなWebアプリケーション**では、フロントエンドとバックエンドを分離して開発するのが主流なんだ。

**従来のWebアプリケーション**：

```text
ブラウザ → PHPファイル → HTMLを生成して返す
→ ページ全体がリロードされる
```

**モダンなWebアプリケーション（REST API）**：

```text
フロントエンド（React、Vue.js）→ REST API → JSONデータを返す
                                      ↓
                            フロントエンドがデータを表示
                            （ページリロード不要！）
```

#### REST APIができるようになると...

✅ **フロントエンドとバックエンドの分離**

- React、Vue.js、Angularなどのモダンなフロントエンドフレームワークと連携
- フロントエンドとバックエンドを独立して開発・更新できる

✅ **マルチプラットフォーム対応**

- Webアプリ、iOSアプリ、Androidアプリが**同じAPI**を使用
- 1つのAPIで複数のクライアントに対応

✅ **SPA（Single Page Application）の構築**

- ページ全体をリロードせずに、データだけを更新
- ユーザー体験が格段に向上

✅ **マイクロサービスアーキテクチャ**

- サービスを小さく分割して開発
- スケーラブルで保守性の高いシステム

#### 実際の使用例

**モダンなWebアプリケーション**：

- **フロントエンド（React）**：ユーザーインターフェース
- **バックエンド（PHP REST API）**：データ処理、データベース操作
- **データベース（MySQL）**：データ保存

**スマホアプリとの連携**：

- **iOSアプリ（Swift）** → REST API ← **Androidアプリ（Kotlin）**
- **Webアプリ（React）** ↗

すべてのクライアントが同じAPIを使用！

### Phase 7のゴール

このPhaseを終えると、こんなことができるようになるよ！

- 🌐 **REST APIの設計**：エンドポイント設計、HTTPメソッドの使い分け
- 📊 **JSONデータの扱い**：`json_encode()`と`json_decode()`のマスター
- 🔄 **HTTPメソッドに応じた処理**：GET、POST、PUT、DELETEの実装
- 📡 **適切なステータスコード**：200、201、400、404、500などの返却
- 🔒 **API セキュリティ**：CORS設定、API Key認証、レートリミット
- ✅ **バリデーション**：入力検証とエラーレスポンス
- 🧪 **APIテスト**：JavaScriptからのAPI呼び出し
- 🤖 **AIにAPI生成を依頼**：セキュアなREST APIをAIと構築

---

## 📚 レッスン一覧

### Lesson 01: REST APIとJSON 🌐

**学習時間の目安**：4-5時間

**何を学ぶ？**

- **REST APIの基礎**
  - RESTとは何か（RESTful API設計原則）
  - リソース指向アーキテクチャ
  - HTTPメソッドとCRUD操作の対応
  - HTTPステータスコードの意味と使い分け

- **JSONの基礎**
  - JSONとは何か（データ交換形式）
  - JSON構文（オブジェクト、配列、データ型）
  - `json_encode()`：PHPからJSONへ
  - `json_decode()`：JSONからPHPへ
  - エラーハンドリング（`json_last_error()`）

- **REST APIの実装**
  - HTTPメソッドに応じた処理の分岐
  - `$_SERVER['REQUEST_METHOD']`の使い方
  - `file_get_contents('php://input')`でリクエストボディを取得
  - `http_response_code()`でステータスコードを設定
  - レスポンス形式の統一

- **セキュリティ対策**
  - CORS（Cross-Origin Resource Sharing）設定
  - 入力検証（バリデーション）
  - API Key認証
  - レートリミット（アクセス制限）

- **実践的な実装**
  - 商品管理API（CRUD操作）
  - 検索機能付きAPI
  - エラーハンドリングとレスポンス設計
  - JavaScriptからのAPI呼び出し（Fetch API）

**前提知識**：

- Phase 5（PHP + MySQL統合）の内容
- データベース接続（PDO）
- プリペアドステートメント
- CRUD操作の実装

**成果物**：

- 完全なREST API（GET、POST、PUT、DELETE）
- HTMLテストページ
- API設計書

---

## 🚀 学習の進め方

### ステップ1：Phase 5の復習（推奨）

REST APIを学ぶ前に、Phase 5の以下のレッスンを復習しておくと理解がスムーズです：

- **Lesson 01: データベース接続** - PDOの基礎
- **Lesson 02: プリペアドステートメント** - SQLインジェクション対策
- **Lesson 03: CRUD操作** - Create、Read、Update、Delete
- **Lesson 04: セキュリティ対策** - XSS、CSRF、バリデーション

### ステップ2：Lesson 01を学習

1. **教材を読む**（1-2時間）
   - REST APIの基礎知識
   - JSONの扱い方
   - セキュリティ対策

2. **コード例を実行**（1時間）
   - サンプルAPIを動かしてみる
   - レスポンスを確認

3. **演習問題に取り組む**（2-3時間）
   - 基礎編：JSON生成/解析、GET API
   - 応用編：POST/PUT/DELETE API
   - セキュリティチャレンジ：バリデーション、認証
   - 総合チャレンジ：完全なREST API

4. **解答例を確認**（30分）
   - 自分のコードと比較
   - セキュリティポイントを確認

### ステップ3：プロジェクト開発（オプション）

学んだ知識を活かして、実際のプロジェクトを作ってみよう！

**推奨プロジェクト**：

- **商品管理API**：Phase 5のCRUD操作をAPI化
- **ブログAPI**：記事の投稿、取得、更新、削除
- **タスク管理API**：ToDoリストのAPI
- **ユーザー管理API**：会員登録、ログイン、プロフィール取得

### ステップ4：フロントエンド連携（発展）

REST APIを作ったら、フロントエンドから呼び出してみよう！

- **HTML + JavaScript（Fetch API）**：シンプルなテストページ
- **React**：モダンなフロントエンドフレームワーク
- **Vue.js**：学習コストが低いフレームワーク

---

## 💡 学習のヒント

### ✅ REST API設計の原則を理解する

- **リソース指向**：URLはリソースを表す（動詞ではなく名詞）
- **HTTPメソッドの使い分け**：GET（取得）、POST（作成）、PUT（更新）、DELETE（削除）
- **ステートレス**：各リクエストは独立している（セッションに依存しない）
- **統一されたレスポンス形式**：JSON形式で一貫性のあるレスポンス

### ✅ セキュリティを最優先する

REST APIでもセキュリティ対策は必須！

- 🔒 **SQLインジェクション対策**：プリペアドステートメント
- 🔒 **入力検証**：すべての入力値をチェック
- 🔒 **CORS設定**：信頼できるドメインのみ許可
- 🔒 **認証**：API Keyやトークン認証
- 🔒 **レートリミット**：過度なリクエストを防ぐ

### ✅ AIを活用する

**良い指示の例**：

```text
「商品管理のREST APIを作成してください。

エンドポイント：
- GET /api/products - 商品一覧取得
- POST /api/products - 商品作成
- PUT /api/products - 商品更新
- DELETE /api/products - 商品削除

セキュリティ要件：
- プリペアドステートメント使用
- 入力バリデーション
- CORS設定
- 適切なHTTPステータスコード

レスポンス形式：JSON」
```

### ✅ テストページを活用する

- **resources/api_test.html**：インタラクティブなテストページ
- ブラウザから直接APIをテストできる
- レスポンスを確認しながら開発

---

## 🔗 関連Phase

### Phase 5との関係

**Phase 5（PHP + MySQL統合）**では、PHPからMySQLを操作して、動的なWebアプリケーションを作りました。

**Phase 7（REST API）**では、Phase 5で学んだCRUD操作を**API化**します！

```text
Phase 5: PHP → MySQL → HTMLを生成
Phase 7: PHP → MySQL → JSONを返す（API）
```

### 次のステップ

**Phase 7を終えたら、以下のステップに進めます**：

- **フロントエンド開発**：React、Vue.jsなどのフレームワークを学ぶ
- **認証・認可**：JWT（JSON Web Token）を使った認証
- **API設計**：OpenAPI（Swagger）でAPI仕様書を作成
- **デプロイ**：本番環境へのデプロイ方法を学ぶ

---

## 📂 Phase 7のファイル構成

```text
phase7-rest-api/
├── README.md                    # Phase全体の概要（このファイル）
└── lessons/
    └── 01-rest-api-json/        # Lesson 01: REST APIとJSON
        ├── README.md            # レッスン教材
        ├── exercises/
        │   ├── README.md        # 演習問題
        │   └── solutions/
        │       └── README.md    # 解答例
        └── resources/
            ├── database.sql     # サンプルSQL
            └── api_test.html    # APIテストページ
```

---

## 🎓 このPhaseで身につくスキル

Phase 7を終えると、以下のスキルが身につきます：

✅ **REST API設計**

- エンドポイント設計
- HTTPメソッドの使い分け
- レスポンス形式の設計

✅ **JSONデータの扱い**

- PHPとJSON間の変換
- エラーハンドリング

✅ **セキュアなAPI開発**

- CORS設定
- 認証・認可
- レートリミット

✅ **フロントエンドとの連携**

- JavaScriptからのAPI呼び出し
- Fetch APIの使い方

✅ **バイブコーディング**

- AIにAPI生成を依頼
- 生成されたAPIのセキュリティチェック

---

## 🌟 Phase 7を始めよう！

準備はできたかな？それでは、Lesson 01からスタートしよう！

👉 **[Lesson 01: REST APIとJSON](lessons/01-rest-api-json/README.md)**

---

**Let's vibe and code! 🎉**

REST APIを学んで、モダンなバックエンド開発をマスターしよう！
