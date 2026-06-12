<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Backup;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    // ==========================================
    // SYSTEM SETTINGS
    // ==========================================

    /**
     * Get all settings as key-value pairs.
     */
    public function getSettings()
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();
        return response()->json($settings);
    }

    /**
     * Save general settings.
     */
    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'system_name' => 'nullable|string|max:255',
            'system_email' => 'nullable|email|max:255',
            'footer_text' => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'timezone' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:10',
            'per_page' => 'nullable|integer|min:5|max:200',
            'date_format' => 'nullable|string|max:20',
            'time_format' => 'nullable|string|max:20',
            'audit_log_retention_days' => 'nullable|integer|min:1|max:3650',
            'maintenance_mode' => 'nullable|boolean',
            'auto_backup_enabled' => 'nullable|boolean',
            'backup_retention_days' => 'nullable|integer|min:1|max:365',
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::setValue($key, $value);
        }

        AuditLog::log('update', 'System settings updated');

        return response()->json(['success' => true, 'message' => 'Settings saved successfully.']);
    }

    /**
     * Upload system logo.
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/settings', $filename);

            $logoPath = 'settings/' . $filename;

            // Delete old logo file
            $oldLogo = SystemSetting::where('key', 'system_logo')->value('value');
            if ($oldLogo && $oldLogo !== $logoPath) {
                $oldFullPath = 'public/' . $oldLogo;
                if (Storage::exists($oldFullPath)) {
                    Storage::delete($oldFullPath);
                }
            }

            SystemSetting::setValue('system_logo', $logoPath, 'file');

            AuditLog::log('update', 'System logo uploaded');

            return response()->json([
                'success' => true,
                'message' => 'Logo uploaded successfully.',
                'logo_url' => asset('storage/' . $logoPath),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file uploaded.'], 400);
    }

    // ==========================================
    // BACKUP & RESTORE
    // ==========================================

    /**
     * List all backups.
     */
    public function getBackups()
    {
        $backups = Backup::with('creator')->latest()->get();
        return response()->json($backups);
    }

    /**
     * Create a new backup.
     */
    public function createBackup(Request $request)
    {
        try {
            $type = $request->type ?? 'sql';
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "backup_{$timestamp}.{$type}";
            $storagePath = "backups/{$filename}";

            if ($type === 'json') {
                $data = [
                    'users' => User::all()->toArray(),
                    'system_settings' => SystemSetting::all()->toArray(),
                    'generated_at' => now()->toDateTimeString(),
                ];
                try {
                    $data['children'] = \App\Models\Child::with('growthMeasurements', 'immunizations')->get()->toArray();
                } catch (\Exception $e) {
                    $data['children'] = [];
                }
                Storage::put($storagePath, json_encode($data, JSON_PRETTY_PRINT));
            } else {
                $dbName = config('database.connections.mysql.database');
                $sqlContent = "-- Child Growth Monitoring System Backup\n";
                $sqlContent .= "-- Generated: " . now()->toDateTimeString() . "\n\n";

                $tables = DB::select('SHOW TABLES');
                $tableKey = 'Tables_in_' . $dbName;

                foreach ($tables as $table) {
                    $tableName = $table->$tableKey;
                    $rows = DB::table($tableName)->get();

                    $sqlContent .= "-- Table: {$tableName}\n";
                    $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";

                    $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                    $sqlContent .= $createTable[0]->{'Create Table'} . ";\n\n";

                    if ($rows->count() > 0) {
                        $columns = array_keys((array) $rows->first());
                        $colList = '`' . implode('`, `', $columns) . '`';

                        $chunks = $rows->chunk(100);
                        foreach ($chunks as $chunk) {
                            $values = [];
                            foreach ($chunk as $row) {
                                $rowValues = array_map(function ($val) {
                                    if ($val === null) return 'NULL';
                                    return "'" . addslashes($val) . "'";
                                }, (array) $row);
                                $values[] = '(' . implode(', ', $rowValues) . ')';
                            }
                            $sqlContent .= "INSERT INTO `{$tableName}` ({$colList}) VALUES\n" . implode(",\n", $values) . ";\n";
                        }
                    }
                    $sqlContent .= "\n";
                }
                Storage::put($storagePath, $sqlContent);
            }

            $fileSize = Storage::size($storagePath);

            $backup = Backup::create([
                'filename' => $filename,
                'filepath' => $storagePath,
                'file_size' => $fileSize,
                'type' => $type,
                'description' => "Backup created on " . now()->format('Y-m-d H:i:s'),
                'created_by' => auth()->id(),
            ]);

            AuditLog::log('backup', "Backup created: {$filename}");

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully.',
                'backup' => $backup->load('creator'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download a backup file.
     */
    public function downloadBackup(Backup $backup)
    {
        if (!Storage::exists($backup->filepath)) {
            return response()->json(['success' => false, 'message' => 'Backup file not found.'], 404);
        }

        AuditLog::log('export', "Backup downloaded: {$backup->filename}");

        return Storage::download($backup->filepath, $backup->filename);
    }

    /**
     * Delete a backup.
     */
    public function deleteBackup(Backup $backup)
    {
        try {
            if (Storage::exists($backup->filepath)) {
                Storage::delete($backup->filepath);
            }

            $filename = $backup->filename;
            $backup->delete();

            AuditLog::log('delete', "Backup deleted: {$filename}");

            return response()->json(['success' => true, 'message' => 'Backup deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete backup: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Restore from a backup.
     */
    public function restoreBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:json,sql,txt,zip,gz|max:102400',
        ]);

        try {
            $file = $request->file('backup_file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $tempPath = $file->storeAs('temp_restore', $originalName);
            $fullPath = Storage::path($tempPath);

            if ($extension === 'json') {
                $content = json_decode(file_get_contents($fullPath), true);
                if (!$content) {
                    throw new \Exception('Invalid JSON backup file.');
                }
                if (isset($content['system_settings'])) {
                    foreach ($content['system_settings'] as $setting) {
                        SystemSetting::updateOrCreate(
                            ['key' => $setting['key']],
                            ['value' => $setting['value'], 'type' => $setting['type'] ?? 'string']
                        );
                    }
                }
            } elseif ($extension === 'sql') {
                $sqlContent = file_get_contents($fullPath);
                $backupPath = 'backups/restored_' . time() . '.sql';
                Storage::put($backupPath, $sqlContent);
            }

            Storage::delete($tempPath);

            AuditLog::log('restore', "Backup restored from file: {$originalName}");

            return response()->json(['success' => true, 'message' => 'Backup restored successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Restore failed: ' . $e->getMessage()], 500);
        }
    }

    // ==========================================
    // AUDIT LOGS
    // ==========================================

    /**
     * Get audit logs with filtering and pagination.
     */
    public function getAuditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('action') && $request->action !== 'all') {
            $query->ofAction($request->action);
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $perPage = min((int) $request->per_page, 100);
        $logs = $query->latest()->paginate($perPage ?: 25);

        return response()->json($logs);
    }

    /**
     * Get audit log actions summary for filter dropdown.
     */
    public function getAuditLogActions()
    {
        $actions = AuditLog::select('action')
            ->distinct()
            ->pluck('action')
            ->map(function ($action) {
                $labels = [
                    'create' => 'Created',
                    'update' => 'Updated',
                    'delete' => 'Deleted',
                    'login' => 'Login',
                    'logout' => 'Logout',
                    'export' => 'Exported',
                    'backup' => 'Backup',
                    'restore' => 'Restore',
                    'toggle_status' => 'Status Change',
                ];
                return [
                    'value' => $action,
                    'label' => $labels[$action] ?? ucfirst($action),
                ];
            });

        return response()->json($actions);
    }
}