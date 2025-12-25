#!/bin/bash

# Start Queue Worker Script
# Run this script to start the queue worker for processing email reports

cd "$(dirname "$0")"

echo "Starting Queue Worker..."
echo "Press Ctrl+C to stop"

php artisan queue:work database --tries=2 --timeout=600 --max-time=3600

