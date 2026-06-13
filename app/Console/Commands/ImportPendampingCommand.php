<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPendampingImportJob;
use App\Models\PendampingImport;
use App\Services\PendampingImportService;
use Illuminate\Console\Command;

class ImportPendampingCommand extends Command
{
    protected $signature = 'pendamping:import
                            {file : Path to CSV/XLSX file}
                            {--queue : Dispatch to queue worker instead of running synchronously}
                            {--chunk=150 : Rows per batch when running synchronously}';

    protected $description = 'Import pendamping data from CSV or Excel file';

    public function handle(PendampingImportService $service): int
    {
        $path = $this->argument('file');
        if (! is_file($path)) {
            $path = base_path($path);
        }

        if (! is_file($path)) {
            $this->error("File not found: {$this->argument('file')}");

            return self::FAILURE;
        }

        try {
            $import = $service->prepareFromPath($path, basename($path));
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Import ID: {$import->id}");
        $this->info("Total rows: {$import->total_rows}");

        if ($this->option('queue')) {
            ProcessPendampingImportJob::dispatch($import->id);
            $this->info('Import queued. Run: php artisan queue:work');

            return self::SUCCESS;
        }

        $chunk = (int) $this->option('chunk');
        $bar = $this->output->createProgressBar($import->total_rows);
        $bar->start();

        $offset = 0;
        while ($offset < $import->total_rows) {
            $service->processChunk($import, $offset, $chunk);
            $import->refresh();
            $bar->setProgress($import->processed_rows);
            $offset += $chunk;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info($import->message ?? 'Import selesai.');
        $this->info("Imported: {$import->imported_count}, Skipped: {$import->skipped_count}");

        return self::SUCCESS;
    }
}
