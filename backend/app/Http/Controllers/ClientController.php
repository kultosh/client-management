<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientRepositoryInterface;
use App\Traits\RequestResponseTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Validators\ValidationException;
use Throwable;

class ClientController extends Controller
{
    use RequestResponseTrait;
    
    protected $clients;

    public function __construct(ClientRepositoryInterface $clients)
    {
        $this->clients = $clients;
    }
    
    public function index(Request $request)
    {
        try {
            $clients = $this->clients->getClients($request);
            return $this->successJsonResponse('Clients fetched successfully.', $clients);
        } catch (Throwable $error) {
            return $this->exceptionJsonResponse($error, 'clients');
        }
    }

    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:csv,xlsx,txt|max:10240',
            ]);
            
            $importData = $this->clients->importClients($request->file('file'));
            
            $message = "Import queued successfully. File is being processed in the background. Please check the status page using ID: {$importData['import_id']}";
            return $this->successJsonResponse($message, $importData);

        } catch (ValidationException $e) {
            $failures = $e->failures();
            return $this->errorJsonResponse('File validation failed before queueing.', ['failures' => $failures], 422);
        } catch (\Throwable $error) {
            return $this->exceptionJsonResponse($error, 'clients');
        }
    }

    public function getImportStatus(string $importId)
    {
        try {
            $result = $this->clients->importStatus($importId);
            return $this->successJsonResponse('Import status retrieved successfully.', $result);

        } catch (Throwable $error) {
            return $this->exceptionJsonResponse($error, 'clients');
        }
    }
}
