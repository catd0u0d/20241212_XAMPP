<?php
// 引入資料庫連線
require_once 'db_connect.php';

// 初始化變數
$backupHistory = [];
$errors = [];

// 取得備份歷史資料
$conn = getConnection();
$sql = "SELECT id, backup_date, file_name FROM backup_history ORDER BY backup_date DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // 把查詢結果存入陣列
    while ($row = $result->fetch_assoc()) {
        $backupHistory[] = $row;
    }
} else {
    $errors[] = "目前尚無備份歷史資料。";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>備份歷史</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* 設定頁面整體樣式 */
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f8f8; /* 淺灰色背景 */
            color: #333; /* 深灰文字 */
            margin: 0;
            padding: 0;
        }

        /* 標題樣式 */
        h1 {
            text-align: center;
            color: #2c3e50; /* 深藍色 */
            margin-bottom: 30px;
            font-size: 2.5rem;
        }

        /* 顯示錯誤訊息區域 */
        .error {
            color: red;
            text-align: center;
        }

        /* 表格樣式 */
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* 輕微陰影效果 */
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #3498db; /* 藍色背景 */
            color: #fff; /* 白色文字 */
        }

        /* 偶數行背景顏色 */
        table tr:nth-child(even) {
            background-color: #f2f2f2; /* 淺灰色背景 */
        }

        /* 返回首頁的連結樣式 */
        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .back-link a {
            text-decoration: none;
            color: #3498db;
            font-size: 1.2rem;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>備份歷史</h1>

    <!-- 顯示錯誤訊息 -->
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- 顯示備份歷史資料 -->
    <?php if (!empty($backupHistory)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>備份日期</th>
                    <th>檔案名稱</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($backupHistory as $backup): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($backup['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($backup['backup_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($backup['file_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- 返回首頁的連結 -->
    <div class="back-link">
        <a href="index.php">返回首頁</a>
    </div>
</body>
</html>
