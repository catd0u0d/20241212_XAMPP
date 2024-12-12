# 客戶基本資料管理系統 (Customer Data Management System)

## 系統簡介
本系統旨在提高客戶基本資料管理的效率，簡化資料的存取與編輯。它提供一個直觀的平台，專為中小型企業設計，尤其適用於行銷部門與帳戶管理部門。系統支援客戶資料的輸入、編輯、搜尋、備份等基本功能，有助於提升資料管理的便捷性和效率。

## 主要功能
1. **客戶資料引入與編輯**  
   支援手動輸入與文件引入客戶資料，並提供資料預覽與格式檢查功能。

2. **基本資料顯示**  
   以表格形式展示客戶資料（姓名、聯絡資訊、公司名稱、備註），並提供欄位排序與分頁功能。

3. **資料搜尋功能**  
   支援關鍵字搜尋如姓名與精確帳戶編號搜尋。

4. **資料存儲與備份**  
   所有資料存儲於 MySQL 資料庫中，並支援自動與手動備份。

## 系統架構
- **後端語言**: PHP
- **資料庫**: MySQL
- **前端技術**: HTML, CSS, JavaScript (jQuery)
- **伺服器**: Apache

## 資料庫設計
### 客戶資料表 (`customers`):
- `id`: 客戶編號，自動遞增主鍵
- `name`: 客戶姓名
- `contact_info`: 聯絡資訊（電話、電子郵件）
- `company_name`: 公司名稱
- `notes`: 重要備註
- `created_at`: 資料建立時間
- `updated_at`: 資料更新時間

### 資料庫 SQL Script
```sql
CREATE DATABASE IF NOT EXISTS CRM;
USE CRM;

CREATE TABLE customers (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_info TEXT NULL,
    company_name VARCHAR(100) NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
