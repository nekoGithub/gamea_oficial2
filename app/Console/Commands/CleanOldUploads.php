<?php

namespace App\Console\Commands;

use App\Models\VersionUpload;
use Illuminate\Console\Command;

class CleanOldUploads extends Command
{
    protected $signature = 'uploads:clean {--days=7 : Días de antigüedad}';
    protected $description = 'Limpiar uploads completados antiguos y sus archivos temporales';

    public function handle()
    {
        $dias = $this->option('days');
        $fechaLimite = now()->subDays($dias);

        $this->info("Limpiando uploads completados de hace más de {$dias} días...");

        // Buscar uploads antiguos completados
        $uploadsAntiguos = VersionUpload::where('estado', 'completado')
            ->where('updated_at', '<', $fechaLimite)
            ->get();

        if ($uploadsAntiguos->isEmpty()) {
            $this->info('No hay uploads antiguos para limpiar');
            return Command::SUCCESS;
        }

        $this->info("Encontrados: {$uploadsAntiguos->count()} uploads antiguos");

        $eliminados = 0;
        $errores = 0;

        foreach ($uploadsAntiguos as $upload) {
            try {
                // Limpiar chunks si existen
                $chunkDir = storage_path("app/chunks/{$upload->chunk_identifier}");

                if (is_dir($chunkDir)) {
                    $files = glob("{$chunkDir}/*");
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                    rmdir($chunkDir);
                    $this->line("Chunks eliminados: {$upload->chunk_identifier}");
                }

                // Eliminar registro de la BD
                $upload->delete();
                $eliminados++;
            } catch (\Exception $e) {
                $this->error("Error con upload #{$upload->id}: " . $e->getMessage());
                $errores++;
            }
        }

        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("Uploads eliminados: {$eliminados}");
        if ($errores > 0) {
            $this->warn("Errores: {$errores}");
        }

        return Command::SUCCESS;
    }
}
