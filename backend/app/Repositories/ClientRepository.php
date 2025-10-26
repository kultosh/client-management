<?php

namespace App\Repositories;

use App\Exports\ExportClients;
use App\Imports\ImportClients;
use App\Interfaces\ClientRepositoryInterface;
use App\Jobs\FinalizeImportStatusJob;
use App\Models\Client;
use App\Models\ImportFailure;
use App\Models\ImportStatus;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class ClientRepository implements ClientRepositoryInterface
{
    public function getClients(Request $request)
    {
        $query = Client::query()->filterByType($request->filter);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('id', 'desc')->paginate(20);
    }

    public function importClients($file)
    {
        $importId = (string) Str::uuid();
        
        ImportStatus::Create([
            'import_id' => $importId,
            'status' => 'queued'
        ]);

        Excel::queueImport(new ImportClients($importId), $file)->chain([
            new FinalizeImportStatusJob($importId),
        ]);
        
        return [
            'message' => 'Import queued successfully.', 
            'import_id' => $importId,
            'check_failures_url' => route('clients.import.status', $importId)
        ];

    }

    public function importStatus($importId)
    {
        $status = ImportStatus::where('import_id', $importId)->first();
        $failures = ImportFailure::where('import_id', $importId)->get();

        return [
            'status' => $status ? $status->status : 'unknown',
            'failures' => $failures,
            'summary' => [
                'total_failures' => $failures->count(),
                'import_id' => $importId
            ]
        ];
    }

    public function exportClients($type)
    {
        $filename = 'clients_' . $type . '_' . now()->format('Y_m_d_H_i_s') . '.csv';
        return Excel::download(new ExportClients($type), $filename);
    }
}