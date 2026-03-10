<?php

return [
    'navigation' => 'Database',
    'cloud_backup_enabled' => 'Enable',
    'cloud_storage' => 'Cloud storage',
    'client_id' => 'Client ID',
    'client_secret' => 'Client secret',
    'redirect_uri' => 'Redirect URI',
    'type' => 'Type',
    'version' => 'Version',
    'path' => 'Path',
    'hidden_in_demo' => 'Path is hidden in demo mode',
    'backup_enabled' => 'Enabled',
    'size' => 'Size',
    'period' => 'Period',
    'backup_time' => 'Backup time',
    'backup_max' => 'Max. backup',
    'file_not_exist' => 'File does not exist',
    'file_deleted' => 'File has been deleted',
    'file_not_deleted' => 'File not deleted',
    'cloud_backup_disabled' => 'Cloud backup is disabled',
    'cloud_backup_is_disabled' => 'Enable cloud backup to automatically store your database backups in the cloud.',
    'upload_to_cloud' => 'Upload to cloud',
    'file_created' => 'File has been created',
    'file_not_created' => 'File not created',
    'auto_backup_is_disabled' => 'Auto-backup is disabled',
    'auto_backup_disabled_reason' => 'Auto-backup is disabled for a reason.',
    'cloud_backup' => 'Cloud Backup',
    'cloud_backup_section_description' => 'When automatic and cloud backups are enabled, each completed backup will be uploaded to the connected cloud storage.',
    'backup_updated' => 'Updated',
    'backup_not_updated' => 'Failed to update',
    'failed_to_run_sql' => 'Failed to run SQLite backup command.',
    'backup' => 'Backup',
    'period_daily' => 'Daily',
    'cloud_storage_dropbox' => 'Dropbox',
    'not_supported' => 'Database is not supported',
    'not_supported_reason' => 'Database driver :driver does not support auto-backup.',

    'dropbox' => [
        'doc_hint' => '[Create a new :driver app](https://www.dropbox.com/developers/apps)',
        'no_package_title' => 'Dropbox is not available',
        'no_package_description' => 'Please install Dropbox package using command: composer require socialiteproviders/dropbox spatie/flysystem-dropbox',
    ],

    'auto_backup' => [
        'label' => 'Auto-backup',
        'section_description' => 'Configure automatic database backups and optional cloud uploads. You can schedule daily backups and connect to cloud storage like Dropbox.',
        'not_available' => 'Database backup is not available',
        'backed_up' => 'Database has been backed up to :path',
    ],

    'file' => [
        'label' => 'Files',
        'name' => 'Name',
    ],

    'btn' => [
        'backup_now' => 'Backup now',
        'download' => 'Download',
        'authorize' => 'Authorize :driver',
        'revoke' => 'Revoke :name',
    ],
];
