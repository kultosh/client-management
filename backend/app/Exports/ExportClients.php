<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportClients implements FromQuery, WithHeadings, ShouldAutoSize, WithChunkReading
{
    protected string $type;

    public function __construct(string $type = 'all')
    {
        $this->type = $type;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return Client::filterByType($this->type)->select('company_name', 'email', 'phone_number');
    }

    public function headings(): array
    {
        return ['Company Name', 'Email', 'Phone Number'];
    }

    public function chunkSize(): int
    {
        return config('excel.exports.chunk_size', 1000);
    }
}
