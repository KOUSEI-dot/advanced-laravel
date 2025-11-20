# Advanced Laravel（勤怠管理システム）

従業員の **出勤・退勤・休憩の記録** および
**勤怠修正申請・承認フロー** を管理する Web アプリケーションです。

ユーザー（従業員）と管理者の 2 権限を持ち、
ユーザーは勤怠の打刻と修正申請、管理者は勤怠データの確認や承認管理を行います。

---

# 🚀 環境構築

Docker を使用してローカル環境を構築します。

---

## 1. リポジトリをクローン

```bash
git clone https://github.com/KOUSEI-dot/advanced-laravel.git
cd advanced-laravel
2. Docker ビルド & 起動
bash
コードをコピーする
docker-compose up --build
⚠ 実行後、一度ターミナルを閉じて開き直してください。

3. PHP コンテナに入る
bash
コードをコピーする
docker-compose exec php bash
4. Composer インストール
bash
コードをコピーする
composer install
# または
composer update
コンテナを抜けます：

bash
コードをコピーする
exit
5. Node パッケージのインストール
bash
コードをコピーする
cd src
npm install
npm run dev
6. .env の設定
bash
コードをコピーする
cd src
cp .env.example .env
php artisan key:generate
.env を以下に書き換えます：

ini
コードをコピーする
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
7. ロゴ画像の設定
アプリで使用するロゴ画像 logo.svg を配置します。

bash
コードをコピーする
cd src/public
mkdir storage
src/public/storage/logo.svg にファイルを保存してください。

🛠 使用技術
分類	技術
Backend Framework	Laravel 8.x
Language	PHP 7.3〜8.x
DB	MySQL
Authentication	Laravel Fortify
CSRF / Cookie 認証	Laravel Sanctum
HTTP Client	Guzzle
CORS	fruitcake/laravel-cors
開発環境	Docker / Laravel Sail
メール	MailHog
Testing	PHPUnit / Mockery / Faker
キャッシュ（任意）	Redis

🗂 ER 図
ER 図は docs/er-diagram.md にまとめています。

👉 ER Diagram を見る

※ GitHub / VSCode / Mermaid Live Editor で表示できます。

🌐 URL 一覧（ローカル環境）
用途	URL
アプリ（開発環境）	http://localhost
phpMyAdmin	http://localhost:8000

```
