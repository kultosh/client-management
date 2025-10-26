<?php

namespace App\Jobs;

use App\Models\ImportStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FinalizeImportStatusJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $importId;

    /**
     * Create a new job instance.
     */
    public function __construct($importId)
    {
        $this->importId = $importId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ImportStatus::where('import_id', $this->importId)->update([
            'status' => 'completed',
        ]);
    }
}
