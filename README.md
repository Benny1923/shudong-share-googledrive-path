樹洞外鏈 Google drive 模組
===
作者我練習php及google-api-clien出現的產物
僅作為練習而已，穩定度並沒有經過完整的測試
只能說是能用而已
正常操作下是不會發生錯誤的

需求
---
Apache rewrite_mod
php_openssl
以及樹洞外鏈的需求
[google-api-php-client](https://github.com/google/google-api-php-client) 2.2.0(TESTED)

###### 非強制
[樹洞外鏈在3472d07](https://github.com/HFO4/shudong-share/tree/3472d070080a5f01c63dd234f9db5affc8d846af) (TESTED)

安裝
---
此安裝適用於已安裝及未安裝
1. 解壓縮shudong-share-googledrive-path至樹洞外鏈
2. 下載[google-api-php-client](https://github.com/google/google-api-php-client/releases/tag/v2.2.0)
3. 解壓縮google-api-php-client至{樹洞外鏈}/includes/google_drive/apiclient 如下:
   ```
   apiclient
   ├─src
   ├─vendor
   ├─.gitattributes
   ├─composer.json
   ├─composer.lock
   ├─LICENSE
   └─README.md
   ```
4. 申請google drive api
5. 建立OAuth憑證
6. 下載憑證JSON
7. 到 {樹洞外鏈}/includes/google_drive/install.php 安裝憑證
8. 完成，可以開始新增上傳方案