"""
Pattern backup and version control system for bot detection patterns.
"""
import os
import json
import hashlib
from datetime import datetime
from django.conf import settings

class PatternVersionManager:
    """Manages versioning and backups of bot detection patterns"""

    def __init__(self):
        self.backup_dir = "core/Bots/blockeds/backups"
        self.version_file = f"{self.backup_dir}/versions.json"
        os.makedirs(self.backup_dir, exist_ok=True)

    def create_backup(self, pattern_files=None):
        """Create a backup of current pattern files"""
        if pattern_files is None:
            pattern_files = [
                'core/Bots/blockeds/bot_patterns.py',
                'core/Bots/blockeds/user_agents.py',
                'core/Bots/blockeds/hostnames.py',
                'core/Bots/blockeds/shit_isps.py',
                'core/Bots/blockeds/blocked_referer.py',
                'core/Bots/blockeds/bad_names.py'
            ]

        # Generate version hash
        version_hash = self._generate_version_hash(pattern_files)

        # Create timestamped backup directory
        timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
        backup_path = f"{self.backup_dir}/{timestamp}_{version_hash[:8]}"

        os.makedirs(backup_path, exist_ok=True)

        # Copy pattern files to backup
        for file_path in pattern_files:
            if os.path.exists(file_path):
                import shutil
                shutil.copy2(file_path, f"{backup_path}/{os.path.basename(file_path)}")

        # Save version info
        self._save_version_info(version_hash, timestamp, pattern_files)

        return version_hash, backup_path

    def _generate_version_hash(self, pattern_files):
        """Generate a hash of all pattern files for versioning"""
        content = ""

        for file_path in pattern_files:
            if os.path.exists(file_path):
                with open(file_path, 'r', encoding='utf-8') as f:
                    content += f.read()

        return hashlib.md5(content.encode()).hexdigest()

    def _save_version_info(self, version_hash, timestamp, pattern_files):
        """Save version information to JSON file"""
        version_info = {
            'version_hash': version_hash,
            'timestamp': timestamp,
            'pattern_files': pattern_files,
            'description': 'Automated backup before pattern update'
        }

        # Load existing versions
        versions = {}
        if os.path.exists(self.version_file):
            with open(self.version_file, 'r') as f:
                versions = json.load(f)

        # Add new version
        versions[version_hash] = version_info

        # Keep only last 10 versions to save space
        if len(versions) > 10:
            oldest_versions = sorted(versions.keys())[:-10]
            for old_version in oldest_versions:
                del versions[old_version]

        # Save updated versions
        with open(self.version_file, 'w') as f:
            json.dump(versions, f, indent=2)

    def get_available_versions(self):
        """Get list of available pattern versions for rollback"""
        if not os.path.exists(self.version_file):
            return {}

        with open(self.version_file, 'r') as f:
            return json.load(f)

    def rollback_to_version(self, version_hash):
        """Rollback patterns to a specific version"""
        versions = self.get_available_versions()

        if version_hash not in versions:
            raise ValueError(f"Version {version_hash} not found")

        version_info = versions[version_hash]
        backup_path = f"{self.backup_dir}/{version_info['timestamp']}_{version_hash[:8]}"

        if not os.path.exists(backup_path):
            raise ValueError(f"Backup directory {backup_path} not found")

        # Restore pattern files from backup
        for file_name in version_info['pattern_files']:
            backup_file = f"{backup_path}/{os.path.basename(file_name)}"
            if os.path.exists(backup_file):
                import shutil
                shutil.copy2(backup_file, file_name)

        # Clear compiled patterns cache
        from core.middleware.block_ips_middleware import _COMPILED_PATTERNS
        _COMPILED_PATTERNS.clear()

        return f"Successfully rolled back to version {version_hash}"

# Global instance
pattern_manager = PatternVersionManager()
