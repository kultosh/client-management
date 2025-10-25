<?php

namespace App\Repositories;

use App\Interfaces\ClientRepositoryInterface;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientRepository implements ClientRepositoryInterface
{
    public function getClients(Request $request)
    {
        $query = Client::query();

        if ($request->has('filter') && $request->filter === 'duplicates') {
            $query->where('is_duplicate', true);
        } elseif ($request->has('filter') && $request->filter === 'unique') {
            $query->where('is_duplicate', false);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('id', 'desc')->paginate(20);
    }
}