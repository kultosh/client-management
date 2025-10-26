<?php

namespace App\Imports;

use App\Models\Client;
use App\Models\ImportFailure;
use App\Models\ImportStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class ImportClients implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;

    private $importId;

    /**
     * Pass the unique ID from the repository to track this import's failures.
     */
    public function __construct(string $importId)
    {
        $this->importId = $importId;
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'nullable|string|max:20',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'company_name.required' => 'Company name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Must be a valid email address',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            ImportFailure::create([
                'import_id' => $this->importId,
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ]);
        }
    }

    public function model(array $row)
    {
        $existing = Client::where([
            'company_name' => $row['company_name'],
            'email' => $row['email'],
            'phone_number' => $row['phone_number'] ?? null
        ])->first();

        $isDuplicate = !is_null($existing);
        $duplicateGroupId = $isDuplicate ? ($existing->duplicate_group_id ?? Str::uuid()) : null;

        // Update existing record if found
        if ($existing && !$existing->is_duplicate) {
            $existing->update([
                'duplicate_group_id' => $duplicateGroupId
            ]);
        }

        return new Client([
            'company_name' => $row['company_name'],
            'email' => $row['email'],
            'phone_number' => $row['phone_number'] ?? null,
            'is_duplicate' => $isDuplicate,
            'duplicate_group_id' => $duplicateGroupId,
        ]);
    }

    public function failed(Throwable $exception)
    {
        ImportStatus::where('import_id', $this->importId)->update(['status' => 'failed']);
    }

    // Define chunk size
    public function chunkSize(): int
    {
        return 1000;
    }
}
