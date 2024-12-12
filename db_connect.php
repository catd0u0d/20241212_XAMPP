<?php
// 資料庫連線設定
$servername = "localhost"; // 資料庫伺服器名稱
$username = "root"; // 資料庫使用者名稱
$password = ""; // 資料庫密碼
$dbname = "crm"; // 資料庫名稱

// 建立資料庫連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線是否成功
if ($conn->connect_error) {
  die("資料庫連線失敗: " . $conn->connect_error);
}

// 設定資料庫編碼
$conn->set_charset("utf8");

// 回傳連線物件，方便其他檔案使用
function getConnection() {
  global $conn;
  return $conn;
}

// 範例：如果要關閉連線，可在適當的地方加入以下程式碼
// $conn->close();

?>