<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class BackupController extends Controller
{
    public function backup()
    {
        $conn = config('database.connections.mysql');
        $db   = $conn['database'];
        $host = $conn['host'] ?? '127.0.0.1';
        $port = $conn['port'] ?? '3306';
        $user = $conn['username'];
        $pass = $conn['password'];

        $dir = storage_path('app/backups');
        if (!is_dir($dir)) { mkdir($dir, 0755, true); }

        $file = 'backup-' . now()->format('Ymd_His') . '.sql';
        $path = $dir . DIRECTORY_SEPARATOR . $file;

        // mysqldump --result-file para evitar redirección de shell
        $cmd = [
            'mysqldump',
            "--host={$host}",
            "--port={$port}",
            "--user={$user}",
            "--password={$pass}",
            '--routines',
            '--events',
            '--single-transaction',
            '--quick',
            $db,
            "--result-file={$path}",
        ];

        $process = new Process($cmd);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json([
                'message' => 'Error al generar respaldo',
                'error'   => $process->getErrorOutput(),
            ], 500);
        }

        return response()->json([
            'message' => 'Respaldo generado',
            'filename' => $file,
            'stored_at' => 'storage/app/backups/' . $file,
        ], 201);
    }

    public function restore(Request $request)
    {
        $data = Validator::make($request->all(), [
            'filename' => 'required|string'
        ])->validate();

        // Sanitizar nombre (evitar rutas arbitrarias)
        $filename = basename($data['filename']);
        if (!Str::endsWith($filename, '.sql')) {
            return response()->json(['message' => 'Archivo inválido (se espera .sql)'], 422);
        }

        $path = storage_path('app/backups/'.$filename);
        if (!is_file($path)) {
            return response()->json(['message' => 'Respaldo no encontrado'], 404);
        }

        $conn = config('database.connections.mysql');
        $db   = $conn['database'];
        $host = $conn['host'] ?? '127.0.0.1';
        $port = $conn['port'] ?? '3306';
        $user = $conn['username'];
        $pass = $conn['password'];

        // mysql -h ... -P ... -u ... -p... db -e "source /ruta/archivo.sql"
        $cmd = [
            'mysql',
            "--host={$host}",
            "--port={$port}",
            "--user={$user}",
            "--password={$pass}",
            $db,
            '-e',
            "source {$path}",
        ];

        $process = new Process($cmd);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json([
                'message' => 'Error al restaurar respaldo',
                'error'   => $process->getErrorOutput(),
            ], 500);
        }

        return response()->json([
            'message'  => 'Restauración completada',
            'filename' => $filename,
        ], 200);
    }

    public function last()
    {
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            return response()->json(['message' => 'No existen respaldos'], 404);
        }

        $files = glob($dir . DIRECTORY_SEPARATOR . '*.sql');
        if (!$files || count($files) === 0) {
            return response()->json(['message' => 'No hay respaldos disponibles'], 404);
        }

        // archivo más reciente por mtime
        $latest = array_reduce($files, function ($a, $b) {
            return filemtime($a) >= filemtime($b) ? $a : $b;
        });

        $mtime = filemtime($latest);
        $size  = filesize($latest);

        return response()->json([
            'filename'       => basename($latest),
            'stored_at'      => 'storage/app/backups/' . basename($latest),
            'size_bytes'     => $size,
            'modified_at'    => Carbon::createFromTimestamp($mtime)->toIso8601String(),
            'modified_human' => Carbon::createFromTimestamp($mtime)->diffForHumans(),
        ], 200);
    }
}
