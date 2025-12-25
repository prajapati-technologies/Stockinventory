# Queue Worker Setup Guide

## Problem
Email reports are not being sent because the queue worker is not running. Jobs are being queued but not processed.

## Solution

### For Development/Testing (Manual Start)

Run this command in a terminal (keep it running):
```bash
cd /var/www/html/store-management
php artisan queue:work database --tries=2 --timeout=600 --max-time=3600
```

Or use the provided script:
```bash
./start-queue-worker.sh
```

**Important:** Keep this terminal/process running. If you close it, the queue worker will stop.

### For Production (Recommended - Supervisor)

1. **Install Supervisor** (if not installed):
```bash
sudo apt-get update
sudo apt-get install supervisor
```

2. **Create Supervisor Config**:
```bash
sudo nano /etc/supervisor/conf.d/store-management-queue.conf
```

3. **Add this configuration**:
```ini
[program:store-management-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/store-management/artisan queue:work database --sleep=3 --tries=2 --timeout=600 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/html/store-management/storage/logs/queue-worker.log
stopwaitsecs=3600
```

**Note:** Update the paths (`/var/www/html/store-management`) and user (`www-data`) according to your server setup.

4. **Start Supervisor**:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start store-management-queue-worker:*
```

5. **Check Status**:
```bash
sudo supervisorctl status store-management-queue-worker:*
```

### Verify Queue Worker is Running

```bash
# Check if process is running
ps aux | grep "queue:work"

# Check queue status
php artisan queue:work --help

# Check pending jobs
php artisan tinker
>>> \DB::table('jobs')->count();
```

### Troubleshooting

**If emails are still not sending:**

1. **Check if queue worker is running:**
```bash
ps aux | grep "queue:work"
```

2. **Check failed jobs:**
```bash
php artisan queue:failed
```

3. **Retry failed jobs:**
```bash
php artisan queue:retry all
```

4. **Clear old jobs:**
```bash
php artisan queue:flush
php artisan queue:clear
```

5. **Check logs:**
```bash
tail -f storage/logs/queue-worker.log
tail -f storage/logs/laravel.log
```

6. **Restart queue worker:**
```bash
# If using supervisor
sudo supervisorctl restart store-management-queue-worker:*

# If running manually, stop and restart
```

### Important Notes

- Queue worker **must be running** for emails to be sent
- For large datasets (5000+ records), jobs are automatically queued
- Queue worker processes jobs in the background
- If queue worker stops, jobs will remain in queue until worker is restarted

