<?php

namespace App\Imports;

use App\Models\Spin_Invoice;

use DB;
use URL;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Redirect;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SpinImportInvoice implements ToModel, WithHeadingRow
//class SpinImportInvoice implements ToCollection, WithHeadingRow

{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $createdOn =  Carbon::now();
        $currentTime = Carbon::now(); 
        
        return new Spin_Invoice([
            'iyerbungalow'  => $row['iyer_bungalow'],
			'alanganallur'  => $row['alanganallur'],
			'palamedu'      => $row['palamedu'],
			'valasai'       => $row['valasai']
        ]);
    } 

    /**
    * @param Collection $collection
    */
    /*public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            echo $row['iyer_bungalow'] .'<br>'; exit;

        }
    }*/
}
