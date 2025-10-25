<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientRepositoryInterface;
use App\Traits\RequestResponseTrait;
use Illuminate\Http\Request;
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
}
