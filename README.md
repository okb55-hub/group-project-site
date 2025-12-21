# 架空 Webサイト（本格韓国料理　ソダム）

## 概要

本プロジェクトは、**架空の韓国料理店**を想定した Webサイトです。
PHP と MySQL を用いたグループワークとして制作しています。

※ 実在の店舗・サービスとは一切関係ありません。

---

## 主な機能

* TOPページ
* メニューページ
* 店舗情報ページ
* お問い合わせページ
* 店舗予約
* テイクアウト注文
* 注文・決済フロー（Stripe **テストモード**、疑似QR決済フロー）
* Discord 店舗用通知（**Webhook**）

---

## 動作環境

* PHP 8.0
* MySQL
* Composer

---

## セットアップ手順

### 1. リポジトリを取得

```bash
git clone <repository-url>
cd <project-directory>
```

### 2. ライブラリのインストール

```bash
composer install
```

### 3. 設定ファイルの作成

* 以下の example ファイルをコピーし、各自の環境に合わせて設定してください。

```bash
cp php/DbManager.example.php php/DbManager.php
cp php/stripe_config.example.php php/stripe_config.php
cp php/stripe_checkout.example.php php/stripe_checkout.php
cp php/discord_notify.example.php php/discord_notify.php
```

  * `DbManager.php`：データベース接続設定
  * `stripe_config.php`：Stripe API キー（テスト用）
  * `discord_notify.php`：Discord Webhook URL（テスト用）
  * `stripe_config.php`：Stripe API キーおよびリダイレクトURLの設定
    * **重要**: `success_url` と `cancel_url` を、ご自身のローカル環境のURLに合わせて修正してください。
    * 例: `http://localhost/プロジェクトフォルダ名/php/order_complete.php...`

※ これらのファイルは **Git 管理対象外** です。

---

### 4. データベース設定

* 任意の MySQL データベースを作成してください
* `DbManager.php` 内の以下の項目を環境に合わせて変更してください

  * データベース名
  * ユーザー名
  * パスワード

---

### 5. 動作確認

ローカルサーバーを起動し、ブラウザからアクセスしてください。

---

## 注意事項

* 本プロジェクトは **学習目的** で作成されています
* Stripe / Discord は **テスト環境のみ** を想定しています
* 実際の API キーやパスワードを **絶対に公開しないでください**
* `vendor/` および機密情報を含むファイルは GitHub に公開されません

---

## 補足

* 設定ファイルは `.example.php` をテンプレートとして管理しています
* 共同開発時は、各自で設定ファイルを作成してください

---

## ライセンス

This project is for educational purposes only.
