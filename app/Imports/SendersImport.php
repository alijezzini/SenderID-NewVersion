<?php

namespace App\Imports;
use App\Models\Sender;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SendersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function model(array $row)
    {
        return new Sender([
            'Senderid'     => $row['Senderid'],
            'Content'    => $row['Content'], 
            'Website'    => $row['Website'],
            'Note'    => $row['Note'],
            
        ]);
    }
}
