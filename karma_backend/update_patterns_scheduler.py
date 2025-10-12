#!/usr/bin/env python
"""
Automated bot pattern update scheduler.
Run this script weekly to keep bot detection patterns current.
"""
import os
import sys
import subprocess
import schedule
import time
from datetime import datetime

def update_patterns():
    """Update bot detection patterns"""
    print(f"🔄 [{datetime.now()}] Starting pattern updates...")

    # Change to Django project directory
    os.chdir(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

    try:
        # Run the Django management command
        result = subprocess.run([
            sys.executable, 'manage.py', 'update_bot_patterns'
        ], capture_output=True, text=True)

        if result.returncode == 0:
            print(f"✅ [{datetime.now()}] Pattern updates completed successfully")
            print(result.stdout)
        else:
            print(f"❌ [{datetime.now()}] Pattern updates failed")
            print(result.stderr)

    except Exception as e:
        print(f"💥 [{datetime.now()}] Error running pattern updates: {e}")

def main():
    """Main scheduler loop"""
    print("🚀 Starting automated bot pattern update scheduler...")

    # Schedule weekly updates (every Monday at 2 AM)
    schedule.every().monday.at("02:00").do(update_patterns)

    print("📅 Updates scheduled for every Monday at 2:00 AM")

    try:
        while True:
            schedule.run_pending()
            time.sleep(3600)  # Check every hour
    except KeyboardInterrupt:
        print("\n👋 Scheduler stopped by user")

if __name__ == "__main__":
    main()
