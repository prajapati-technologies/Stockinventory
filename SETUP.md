# Quick Setup Guide

## For Development

1. **Install Dependencies**
   ```bash
   composer install
   ```
   
   **Note:** No npm/Node.js required - uses CDN for Tailwind CSS

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure Database** (Edit `.env`)
   ```
   DB_DATABASE=store_management
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Initialize Database**
   ```bash
   php artisan migrate
   php artisan db:seed
   php artisan storage:link
   ```

5. **Run the Application**
   ```bash
   php artisan serve
   ```

6. **Login**
   - URL: http://localhost:8000/login
   - Phone: 9999999999
   - Password: admin123

## Quick Notes

### Districts & Mandals
The seeder includes sample data for 5 districts with mandals from Andhra Pradesh. Admin can add more from the admin panel.

### User Creation
- **Stores**: Admin creates store accounts. Phone number is the username.
- **Supervisors**: Admin creates supervisor accounts for each mandal.
- Default password for all new users: `guest` (must change on first login)

### Stock Calculation
Automatic calculation based on land:
- ≤1.10 acres = 3 bags
- 1.10-1.20 = 4 bags
- 1.21-2.10 = 5 bags
- 2.10-3.00 = 6 bags
- >3.00 = floor(acres) × 2

### Document Photos
- Max size: 100KB (auto-compressed)
- Stored in: `storage/app/public/documents/`
- Accessible via: `/storage/documents/filename.jpg`

### Excel Upload Formats

**Stores:**
```
name | phone_number | district_id | mandal_id | validity_months
```

**Supervisors:**
```
name | phone_number | district_id | mandal_id
```

**Customers:**
```
document_number | district_id | mandal_id | total_land
```

## Troubleshooting

### Permission Issues
```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Clear All Caches
```bash
php artisan optimize:clear
```

### Reset Database
```bash
php artisan migrate:fresh --seed
```

## Production Checklist

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Configure proper database credentials
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set up SSL certificate
- [ ] Configure backup strategy
- [ ] Set up monitoring and logging

