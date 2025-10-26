<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ClientRepositoryInterface
{
    public function getClients(Request $request);
    public function importClients($file);
    public function importStatus($importId);
    public function exportClients($type);
}