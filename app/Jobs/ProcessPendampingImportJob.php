<?php

namespace App\Jobs;

use App\Models\PendampingImport;
use App\Services\PendampingImportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessPendampingImportJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 7200;

    public int $tries = 1;

    public function __construct(
        public string $importId
    ) {}

    public function handle(PendampingImportService $service): void
    {
        $import = PendampingImport::findOrFail($this->importId);

        try {
            $service->processAll($import);
        } catch (\Throwable $e) {
            Log::error('Pendamping import failed', [
                'import_id' => $this->importId,
                'error' => $e->getMessage(),
            ]);

            $import->update([
                'status' => 'failed',
                'message' => 'Import gagal: '.$e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }
}
