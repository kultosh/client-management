<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientRepositoryInterface;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected $clients;

    public function __construct(ClientRepositoryInterface $clients)
    {
        $this->clients = $clients;
    }
    
    public function index(Request $request)
    {
        return response()->json($this->clients->getClients($request));
    }
}
