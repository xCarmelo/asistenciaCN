<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    public function index(Request $request)
    {
        $query = Backup::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('filename', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $backups = $query->orderBy('created_at', 'desc')->paginate(15);
        $totalSize = $this->getTotalBackupsSize();

        return view('backups.index', compact('backups', 'totalSize'));
    }

    public function create()
    {
        return view('backups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'custom_path' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        try {
            $backupInfo = $this->generateBackup($request->custom_path, $request->description);
            return redirect()->route('backups.index')
                ->with('success', 'Respaldo creado exitosamente: ' . $backupInfo['filename']);
        } catch (\Exception $e) {
            Log::error('Error al crear respaldo: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al crear respaldo: ' . $e->getMessage());
        }
    }

    private function generateBackup($customPath = null, $description = null)
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$database}_{$timestamp}.sql";

        // Determinar ruta destino
        if ($customPath && !empty($customPath)) {
            $basePath = rtrim($customPath, '/\\');
            if (!File::exists($basePath)) {
                File::makeDirectory($basePath, 0755, true);
            }
            if (!is_writable($basePath)) {
                throw new \Exception("La ruta no tiene permisos de escritura: {$basePath}");
            }
        } else {
            $basePath = storage_path('app/backups');
            if (!File::exists($basePath)) {
                File::makeDirectory($basePath, 0755, true);
            }
        }

        $filePath = $basePath . DIRECTORY_SEPARATOR . $filename;

        // Buscar mysqldump.exe
        $mysqldump = $this->findMysqldump();
        if (!$mysqldump) {
            throw new \Exception('No se encontró mysqldump.exe. Asegúrate de que MySQL esté instalado.');
        }

        // Crear archivo de respaldo EXCLUYENDO la tabla backups (para que sea inmutable)
        $command = sprintf(
            '"%s" --user=%s --password=%s --host=%s %s --ignore-table=%s.backups > "%s" 2>&1',
            $mysqldump,
            $username,
            $password,
            $host,
            $database,
            $database,
            $filePath
        );

        exec($command, $output, $returnCode);
        $outputStr = implode("\n", $output);

        if ($returnCode !== 0 || !File::exists($filePath) || File::size($filePath) === 0) {
            throw new \Exception("Error al generar respaldo. Código: {$returnCode}. Detalle: {$outputStr}");
        }

        $size = $this->formatBytes(File::size($filePath));

        $backup = Backup::create([
            'name'        => "Respaldo {$database} - {$timestamp}",
            'filename'    => $filename,
            'path'        => $filePath,
            'size'        => $size,
            'extension'   => 'sql',
            'description' => $description,
            'status'      => 'completed',
        ]);

        return ['filename' => $filename, 'backup' => $backup];
    }

    private function findMysqldump()
    {
        $possiblePaths = [
            'G:\\IIMPRIMIR\\xampp\\mysql\\bin\\mysqldump.exe',
            'G:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'D:\\xampp\\mysql\\bin\\mysqldump.exe',
        ];
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        $output = null;
        exec('where mysqldump 2>nul', $output, $code);
        if ($code === 0 && !empty($output)) {
            return $output[0];
        }
        return null;
    }

    public function restore($id)
    {
        try {
            $backup = Backup::findOrFail($id);
            if (!File::exists($backup->path)) {
                return redirect()->back()->with('error', 'El archivo de respaldo no existe.');
            }

            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            $mysql = $this->findMysql();
            if (!$mysql) {
                throw new \Exception('No se encontró mysql.exe.');
            }

            $command = sprintf(
                '"%s" --user=%s --password=%s --host=%s %s < "%s" 2>&1',
                $mysql,
                $username,
                $password,
                $host,
                $database,
                $backup->path
            );
            exec($command, $output, $returnCode);
            if ($returnCode !== 0) {
                throw new \Exception('Error al restaurar. Código: ' . $returnCode);
            }

            Log::info("Backup restaurado: {$backup->filename}");
            return redirect()->route('backups.index')->with('success', 'Respaldo restaurado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al restaurar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al restaurar: ' . $e->getMessage());
        }
    }

    private function findMysql()
    {
        $possiblePaths = [
            'G:\\IIMPRIMIR\\xampp\\mysql\\bin\\mysql.exe',
            'G:\\xampp\\mysql\\bin\\mysql.exe',
            'C:\\xampp\\mysql\\bin\\mysql.exe',
        ];
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        $output = null;
        exec('where mysql 2>nul', $output, $code);
        if ($code === 0 && !empty($output)) {
            return $output[0];
        }
        return null;
    }

    public function download($id)
    {
        $backup = Backup::findOrFail($id);
        if (!File::exists($backup->path)) {
            return redirect()->back()->with('error', 'El archivo no existe.');
        }
        return response()->download($backup->path, $backup->filename);
    }

    public function destroy($id)
    {
        try {
            $backup = Backup::findOrFail($id);
            if (File::exists($backup->path)) {
                File::delete($backup->path);
            }
            $backup->delete();
            return redirect()->route('backups.index')->with('success', 'Respaldo eliminado.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar.');
        }
    }

    public function importForm()
    {
        return view('backups.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,zip,json,csv,txt|max:102400',
        ]);

        $file = $request->file('backup_file');
        $originalName = $file->getClientOriginalName();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "imported_{$timestamp}_{$originalName}";

        $backupDir = storage_path('app/backups/imported');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        $filePath = $backupDir . DIRECTORY_SEPARATOR . $filename;
        $file->move($backupDir, $filename);

        $size = $this->formatBytes(File::size($filePath));

        Backup::create([
            'name'        => "Importado: {$originalName}",
            'filename'    => $filename,
            'path'        => $filePath,
            'size'        => $size,
            'extension'   => $file->getClientOriginalExtension(),
            'description' => "Importado el " . now()->format('d/m/Y H:i:s'),
            'status'      => 'completed',
        ]);

        return redirect()->route('backups.index')->with('success', 'Archivo importado correctamente.');
    }

    private function getTotalBackupsSize()
    {
        $backups = Backup::all();
        $totalBytes = 0;
        foreach ($backups as $backup) {
            $sizeStr = str_replace([' KB', ' MB', ' GB'], '', $backup->size);
            if (str_contains($backup->size, 'KB')) {
                $totalBytes += (float)$sizeStr * 1024;
            } elseif (str_contains($backup->size, 'MB')) {
                $totalBytes += (float)$sizeStr * 1048576;
            } elseif (str_contains($backup->size, 'GB')) {
                $totalBytes += (float)$sizeStr * 1073741824;
            } else {
                $totalBytes += (float)$sizeStr;
            }
        }
        return $this->formatBytes($totalBytes);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}