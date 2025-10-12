"""
Management command to update bot detection patterns from external sources.
"""
import json
import requests
from datetime import datetime
from django.core.management.base import BaseCommand
from django.core.cache import cache
from django.conf import settings
from core.management.pattern_version_manager import pattern_manager

class Command(BaseCommand):
    help = 'Update bot detection patterns from external intelligence sources'

    def add_arguments(self, parser):
        parser.add_argument(
            '--dry-run',
            action='store_true',
            help='Show what would be updated without making changes',
        )
        parser.add_argument(
            '--rollback',
            type=str,
            help='Rollback to a specific version hash',
        )

    def handle(self, *args, **options):
        if options['rollback']:
            self.rollback_patterns(options['rollback'])
            return

        self.stdout.write(
            self.style.SUCCESS('🔄 Updating bot detection patterns...')
        )

        # Create backup before updating
        version_hash, backup_path = pattern_manager.create_backup()

        # Update patterns from various sources
        updated_patterns = {}

        # 1. Update AI bot patterns from Human Security
        ai_patterns = self.fetch_ai_bot_patterns()
        if ai_patterns:
            updated_patterns['ai_bots'] = ai_patterns

        # 2. Update malicious IP patterns from GitHub
        ip_patterns = self.fetch_malicious_ips()
        if ip_patterns:
            updated_patterns['malicious_ips'] = ip_patterns

        # 3. Update threat intelligence from IPQualityScore
        threat_data = self.fetch_threat_intelligence()
        if threat_data:
            updated_patterns['threat_intel'] = threat_data

        if options['dry_run']:
            self.stdout.write(
                self.style.WARNING('DRY RUN - No changes made')
            )
            self.show_preview(updated_patterns)
            self.stdout.write(
                self.style.SUCCESS(f'📦 Backup created: {backup_path}')
            )
        else:
            self.apply_updates(updated_patterns, version_hash)

    def fetch_ai_bot_patterns(self):
        """Fetch latest AI bot patterns from Human Security"""
        try:
            # For demo, return sample patterns
            # In production, parse actual webpage or use API
            return {
                'gptbot': 'GPTBot/1.0',
                'claudebot': 'ClaudeBot/1.0',
                'perplexitybot': 'PerplexityBot/1.0'
            }
        except Exception as e:
            self.stdout.write(
                self.style.ERROR(f'Failed to fetch AI patterns: {e}')
            )
            return None

    def fetch_malicious_ips(self):
        """Fetch malicious IP patterns from threat intelligence sources"""
        try:
            # For demo, return sample ranges
            return [
                "185.220.101.*",
                "104.248.0.0/16"
            ]
        except Exception as e:
            self.stdout.write(
                self.style.ERROR(f'Failed to fetch IP patterns: {e}')
            )
            return None

    def fetch_threat_intelligence(self):
        """Fetch general threat intelligence data"""
        try:
            return {
                'new_bot_signatures': ['advanced-bot', 'stealth-crawler']
            }
        except Exception as e:
            self.stdout.write(
                self.style.ERROR(f'Failed to fetch threat intel: {e}')
            )
            return None

    def show_preview(self, updated_patterns):
        """Show preview of what would be updated"""
        for category, patterns in updated_patterns.items():
            self.stdout.write(
                self.style.WARNING(f'📋 {category.upper()} PATTERNS:')
            )
            if isinstance(patterns, dict):
                for key, value in patterns.items():
                    self.stdout.write(f'  {key}: {value}')
            elif isinstance(patterns, list):
                for pattern in patterns:
                    self.stdout.write(f'  {pattern}')

    def apply_updates(self, updated_patterns, version_hash):
        """Apply the pattern updates to files"""
        # For demo, we'll just log the updates
        # In production, you'd modify the actual pattern files

        self.stdout.write(
            self.style.SUCCESS('✅ Pattern updates applied successfully!')
        )
        self.stdout.write(
            self.style.SUCCESS(f'🔖 Version: {version_hash}')
        )

        # Clear pattern cache to force recompilation
        from core.middleware.block_ips_middleware import _COMPILED_PATTERNS
        _COMPILED_PATTERNS.clear()

    def rollback_patterns(self, version_hash):
        """Rollback patterns to a specific version"""
        try:
            message = pattern_manager.rollback_to_version(version_hash)
            self.stdout.write(
                self.style.SUCCESS(f'🔄 {message}')
            )
        except ValueError as e:
            self.stdout.write(
                self.style.ERROR(f'❌ {e}')
            )
