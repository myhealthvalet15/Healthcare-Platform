<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class ExportExcel implements FromCollection
{
    protected $sheetData;

    public function __construct(array $sheetData)
    {
        $this->sheetData = $sheetData;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Convert the array to a Laravel Collection
        return collect($this->sheetData);
    }
}
