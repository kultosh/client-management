<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface ClientRepositoryInterface
{
    public function getClients(Request $request);
    public function importClients($file);
    public function importStatus($importId);
    public function exportClients($type);
    public function updateClient(array $data, int $id);
    public function deleteClient(int $id);
}