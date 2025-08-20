# Admin Panel — Laravel

A lightweight Laravel-based admin panel built without admin packages or migrations.  
Includes custom authentication, product & category management, bulk CSV/XLSX import (FastExcel), low-stock tracking, invoices with tax/discount logic, and audit logging.

---

## 🚀 Features
- **Custom Authentication** (no external admin/login packages)
- **Products & Categories CRUD**
- **Bulk CSV/XLSX Import** with [`rap2hpoutre/fast-excel`](https://github.com/rap2hpoutre/fast-excel)
  - Duplicate handling by SKU
  - Import summary modal (created/updated/failed rows + new categories)
- **Low-Stock Dashboard Widget**
- **Invoices** with tax, discount, shipping, TDS, and reverse charge support
- **Audit Logs & Product Versioning**
- **Image Uploads & Gallery Manager**
- **Settings** via `.env` and UI

---

## 🛠 Tech Stack
- PHP 8.x, **Laravel 10/11**
- MySQL 8.x  
- Composer (for dependencies)  
- **FastExcel** for import/export  

---

## ⚡ Quick Start

```bash
# 1. Clone the repo
git clone https://github.com/pankuat12/admin.git
cd admin

# 2. Install dependencies
composer install

# 3. Copy env file
cp .env.example .env
php artisan key:generate

# 4. Import SQL (no migrations)
# Import docs/sql/app_schema_and_seed.sql into your MySQL database (check in project root)

# 5. Configure .env with DB credentials
# Example:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=admin
DB_USERNAME=root
DB_PASSWORD=


# 6. Run
php artisan serve


🎥 Demo (Screenshots)
/root/demo/
