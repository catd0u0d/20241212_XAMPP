<?php
require_once 'db_connect.php';

$keyword = "";
$searchResult = [];
$errors = [];

// 搜尋請求
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["keyword"])) {
    $keyword = trim($_GET["keyword"]);

    // 限制字串長度和格式
    if (strlen($keyword) > 50) {
        $errors[] = "關鍵字過長，請輸入 50 個字以內的搜尋字串。";
    } elseif (!preg_match('/^[\p{L}\p{N}\s\-_\p{Han}]+$/u', $keyword)) {
        $errors[] = "錯誤的輸入不符合關鍵字檢查。";
    } elseif (!empty($keyword)) {
        $conn = getConnection();

        // 搜尋客戶資料
        $sql = "SELECT * FROM customers WHERE id = ? OR name LIKE ?";
        $stmt = $conn->prepare($sql);

        // 如果輸入是數字，嘗試匹配 ID，否則匹配客戶名稱
        if (is_numeric($keyword)) {
            $id = intval($keyword);
            $likeKeyword = "%{$keyword}%";
            $stmt->bind_param("is", $id, $likeKeyword);
        } else {
            $id = 0; // 占位符
            $likeKeyword = "%{$keyword}%";
            $stmt->bind_param("is", $id, $likeKeyword);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $searchResult[] = $row;
            }
        } else {
            $errors[] = "找不到與關鍵字「" . htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') . "」相符的客戶資料。";
        }

        $stmt->close();
        $conn->close();
    } else {
        $errors[] = "請輸入關鍵字進行搜尋。";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>搜尋客戶資料</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-top: 50px;
            font-size: 2.5rem;
        }
        .navigation {
            text-align: center;
            margin-top: 20px;
        }
        .navigation a {
            text-decoration: none;
            color: #1abc9c;
            font-size: 1.2rem;
            margin: 0 10px;
        }
        .navigation a:hover {
            text-decoration: underline;
        }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #1abc9c;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        td a {
            color: #3498db;
            text-decoration: none;
        }
        td a:hover {
            text-decoration: underline;
        }
        .no-data {
            text-align: center;
            color: #999;
        }
    </style>
</head>
<body>
    <div id="container">
        <h1>搜尋客戶資料</h1>
        <form method="get" id="search-form">
            <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'); ?>" placeholder="請輸入關鍵字">
            <input type="submit" value="搜尋">
        </form>
        
        <!-- 包裹在 div 中來使連結居中 -->
        <div id="back-link">
            <a href="index.php">返回首頁</a>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div style="color: red;">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
         
        <?php if (!empty($searchResult)): ?>
            <table border="1" id="search-result-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>客戶姓名</th>
                        <th>聯絡資訊</th>
                        <th>公司名稱</th>
                        <th>備註</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($searchResult as $customer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($customer["id"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($customer["name"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($customer["contact_info"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($customer["company_name"], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($customer["notes"], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="script.js"></script>
</body>
</html>
