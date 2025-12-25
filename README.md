# Store Management System

A comprehensive Laravel-based store management system with role-based access control, customer management, stock tracking, and sales management.

## Features

### Admin Features
- Complete dashboard with statistics
- Create and manage stores (individual entry and Excel upload)
- Create and manage supervisors (individual entry and Excel upload)
- Manage districts and mandals
- Extend store validity periods
- Reset user passwords
- View expiring store subscriptions

### Store Manager Features
- Dashboard with store information and validity status
- Search customers by document number
- Add new customer data with automatic stock calculation
- Capture and upload document photos (auto-compressed to 100KB)
- Create sales transactions
- View sales history
- See customer purchase history across all stores

### Supervisor Features
- Dashboard with mandal-level statistics
- View and manage stores in their mandal
- Generate sales reports by store and date range
- View detailed customer purchase history
- Modify customer stock allocations
- Add customer data (individual and Excel upload)
- Upload bulk customer data via Excel

## Technology Stack

- **Framework:** Laravel 12
- **Frontend:** Tailwind CSS (CDN - No build step required)
- **Authentication:** Laravel with Spatie Permissions
- **Database:** MySQL/PostgreSQL/SQLite
- **Image Processing:** Intervention Image
- **Excel Import/Export:** Maatwebsite Excel
- **Mobile-Responsive:** Fully optimized for mobile devices
- **No Node.js Required:** Uses CDN for styling

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL/PostgreSQL database

**Note:** Node.js/npm NOT required - this app uses CDN-based Tailwind CSS!

### Step 1: Clone and Install Dependencies

```bash
cd /var/www/html/store-management
composer install
```

**Note:** This application uses CDN-based Tailwind CSS, so Node.js/npm is NOT required.

### Step 2: Environment Setup

```bash
# Create .env file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 3: Configure Database

Edit `.env` file and update database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=store_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 4: Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate

# Seed database with roles, districts, and admin user
php artisan db:seed
```

### Step 5: Create Storage Link

```bash
# Create symbolic link for document uploads
php artisan storage:link
```

### Step 6: Start the Application

```bash
# Development
php artisan serve

# Or use the composer script
composer run dev
```

## Default Credentials

After seeding, you can login with:

**Admin Account:**
- Phone: `9999999999`
- Password: `admin123`

**Store Managers & Supervisors:**
- Phone: (as created by admin)
- Default Password: `guest` (must change on first login)

## Stock Calculation Rules

The system automatically calculates stock allocation based on land extent:

| Land Extent | Stock Allotted |
|-------------|----------------|
| Up to 1.10 acres | 3 bags |
| 1.10 to 1.20 acres | 4 bags |
| 1.21 to 2.10 acres | 5 bags |
| 2.10 to 3.00 acres | 6 bags |
| Above 3 acres | (Integer part) × 2 |

**Example:** 
- Land = 3.2 acres → Stock = 3 × 2 = 6 bags
- Land = 4.1 acres → Stock = 4 × 2 = 8 bags

## Excel Upload Format

### Stores Excel Format
Columns: `name`, `phone_number`, `district_id`, `mandal_id`, `validity_months`

Example:
```csv
name,phone_number,district_id,mandal_id,validity_months
Store Name,9876543210,1,1,6
```

### Supervisors Excel Format
Columns: `name`, `phone_number`, `district_id`, `mandal_id`

Example:
```csv
name,phone_number,district_id,mandal_id
Supervisor Name,9876543211,1,1
```

### Customers Excel Format
Columns: `document_number`, `district_id`, `mandal_id`, `total_land`

Example:
```csv
document_number,district_id,mandal_id,total_land
DOC123456,1,1,2.5
```

## User Roles & Permissions

### Admin
- Full system access
- Manage stores, supervisors, districts, and mandals
- View all reports and analytics

### Store Manager
- View own store information
- Search and add customers
- Create sales transactions
- View sales history

### Supervisor
- View stores in assigned mandal
- Generate sales reports
- Modify customer stock allocations
- Add customers via form or Excel

## Security Features

- Phone number-based authentication
- Role-based access control (RBAC)
- Forced password change on first login
- Store validity expiration checking
- Secure image upload with compression
- CSRF protection
- SQL injection prevention

## Mobile Responsive Design

The entire application is optimized for mobile devices with:
- Touch-friendly interfaces
- Responsive grid layouts
- Mobile-optimized forms
- Easy navigation on small screens

## Deployment to AWS

### Prerequisites
- AWS EC2 instance (Ubuntu 20.04 or higher)
- Domain name configured
- SSL certificate (Let's Encrypt recommended)

### Deployment Steps

1. **Setup EC2 Instance**
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 and extensions
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd

# Install MySQL
sudo apt install mysql-server

# Install Nginx
sudo apt install nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

2. **Clone and Setup Application**
```bash
cd /var/www/html
git clone <your-repo-url> store-management
cd store-management
composer install --optimize-autoloader --no-dev
```

3. **Configure Environment**
```bash
cp .env.example .env
php artisan key:generate
# Edit .env with production settings
```

4. **Setup Database**
```bash
mysql -u root -p
CREATE DATABASE store_management;
CREATE USER 'store_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON store_management.* TO 'store_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

php artisan migrate --force
php artisan db:seed --force
```

5. **Set Permissions**
```bash
sudo chown -R www-data:www-data /var/www/html/store-management
sudo chmod -R 755 /var/www/html/store-management
sudo chmod -R 775 /var/www/html/store-management/storage
sudo chmod -R 775 /var/www/html/store-management/bootstrap/cache
php artisan storage:link
```

6. **Configure Nginx**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/store-management/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

7. **Setup SSL (Let's Encrypt)**
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

8. **Optimize for Production**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

**Note:** No asset compilation needed - Tailwind CSS loads from CDN.

## Maintenance

### Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Backup Database
```bash
mysqldump -u store_user -p store_management > backup_$(date +%Y%m%d).sql
```

## Support

For issues or questions:
- Check the Laravel documentation: https://laravel.com/docs
- Review application logs: `storage/logs/laravel.log`

## License

This project is proprietary software developed for store management purposes.

---

**Version:** 1.0.0  
**Last Updated:** October 2025






// point 7: sub-admin dashboard
/var/www/html/store-management/app/Http/Controllers/SubAdmin/CustomerController.php
/var/www/html/store-management/resources/views/sub-admin/customers/search.blade.php
/var/www/html/store-management/resources/views/layouts/app.blade.php
/var/www/html/store-management/routes/web.php

// point 1: supervisor and store dashboard
/var/www/html/store-management/app/Http/Controllers/Supervisor/PurchaseHistoryController.php
/var/www/html/store-management/app/Http/Controllers/StoreManager/PurchaseHistoryController.php
/var/www/html/store-management/resources/views/supervisor/purchase-history/index.blade.php
/var/www/html/store-management/resources/views/store/purchase-history/index.blade.php
/var/www/html/store-management/resources/views/layouts/app.blade.php
/var/www/html/store-management/routes/web.php

// point 6: admin panel create stock allotment conditions
/var/www/html/store-management/database/migrations/2025_12_09_161956_create_stock_allotment_conditions_table.php
/var/www/html/store-management/app/Models/StockAllotmentCondition.php
/var/www/html/store-management/app/Http/Controllers/Admin/StockAllotmentConditionController.php
/var/www/html/store-management/routes/web.php
/var/www/html/store-management/resources/views/layouts/app.blade.php
/var/www/html/store-management/resources/views/admin/stock-allotment-conditions/create.blade.php
/var/www/html/store-management/resources/views/admin/stock-allotment-conditions/index.blade.php
/var/www/html/store-management/resources/views/admin/stock-allotment-conditions/edit.blade.php
/var/www/html/store-management/app/Models/Customer.php
/var/www/html/store-management/app/Http/Controllers/StoreManager/CustomerController.php
/var/www/html/store-management/app/Http/Controllers/Supervisor/ReportController.php
/var/www/html/store-management/app/Imports/CustomersImport.php
/var/www/html/store-management/database/migrations/2025_12_09_185948_remove_number_of_times_from_stock_allotment_conditions_table.php
/var/www/html/store-management/app/Http/Controllers/StoreManager/SaleController.php
/var/www/html/store-management/resources/views/store/sale/create.blade.php


// point 5: pagination
/var/www/html/store-management/app/Http/Controllers/Admin/StoreController.php
/var/www/html/store-management/app/Http/Controllers/Admin/SupervisorController.php
/var/www/html/store-management/app/Http/Controllers/Admin/SubAdminController.php
/var/www/html/store-management/app/Http/Controllers/Supervisor/ReportController.php
/var/www/html/store-management/app/Http/Controllers/SubAdmin/ReportController.php
/var/www/html/store-management/app/Http/Controllers/StoreManager/SaleController.php
/var/www/html/store-management/app/Http/Controllers/Admin/StockAllotmentConditionController.php

// point 4: supervisor report logic
/var/www/html/store-management/app/Http/Controllers/Supervisor/ReportController.php
/var/www/html/store-management/resources/views/supervisor/reports/index.blade.php

// point 2: sub-admin Add send report to mail
/var/www/html/store-management/app/Mail/ReportMail.php
/var/www/html/store-management/resources/views/emails/report.blade.php
/var/www/html/store-management/app/Http/Controllers/SubAdmin/ReportController.php
/var/www/html/store-management/resources/views/sub-admin/reports/index.blade.php
/var/www/html/store-management/routes/web.php


// point 3: Excel upload to add additional bags
/var/www/html/store-management/database/migrations/2025_12_12_153944_create_customer_additional_bags_table.php
/var/www/html/store-management/app/Models/CustomerAdditionalBag.php
/var/www/html/store-management/app/Imports/AdditionalBagsImport.php
/var/www/html/store-management/app/Http/Controllers/Supervisor/ReportController.php
/var/www/html/store-management/app/Models/Customer.php
/var/www/html/store-management/routes/web.php
/var/www/html/store-management/resources/views/supervisor/customers/index.blade.php
/var/www/html/store-management/app/Exports/AdditionalBagsTemplateExport.php
