<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ClientRepositoryInterface
{
    public function getClients(Request $request);
}