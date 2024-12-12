<?php
// 引入資料庫連線
require_once 'db_connect.php';

// 設定備份目錄
$backupDir = "backup/";

// 檢查備份目錄是否存在，若不存在則建立
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);  // 建立目錄並設定權限
}

// 生成備份檔案名稱
$backupFile = $backupDir . "backup_" . date("Ymd_His") . ".sql";

// 執行備份命令
// 使用 root 作為用戶名，空密碼，以及 crm 資料庫進行備份
$command = "mysqldump -u root -p'' crm > " . escapeshellarg($backupFile);

// 執行命令，並檢查執行結果
$output = null;
$returnVar = null;
exec($command, $output, $returnVar);

// 根據命令執行結果回應
if ($returnVar === 0) {
    echo "資料備份完成！<br>";

    // 儲存備份記錄到資料庫
    $conn = getConnection();
    $sql = "INSERT INTO backup_history (backup_date, file_name) VALUES (NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $backupFile);

    if ($stmt->execute()) {
        echo "備份記錄已儲存！<br>";
    } else {
        echo "儲存備份記錄時發生錯誤：" . $stmt->error . "<br>";
    }

    $stmt->close();
    $conn->close();
} else {
    // 顯示錯誤訊息，便於診斷問題
    echo "資料備份失敗，請檢查命令與伺服器設定。<br>";
    echo "請確認 `mysqldump` 是否可用，並檢查資料庫設定是否正確。<br>";
}

// 提供返回首頁的連結
echo "<a href='index.php'>返回首頁</a>";
?>
