<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportClients implements FromCollection, WithHeadings, ShouldAutoSize, ShouldQueue
{
    protected string $type;

    public function __construct(string $type = 'all')
    {
        $this->type = $type;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Client::query();

        if ($this->type === 'duplicate') {
            $query->where('is_duplicate', true);
        } elseif ($this->type === 'unique') {
            $query->where('is_duplicate', false);
        }
        return $query->select('company_name', 'email', 'phone_number')->get();
    }

    public function headings(): array
    {
        return ['Company Name', 'Email', 'Phone Number'];
    }
}
