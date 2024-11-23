<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection,WithMapping,WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct(public Collection $records){}
    public function collection()
    {
        // return Student::all();
        return $this->records;
    }
    public function map($student): array
    {
        return [
            $student->name,
            $student->email,
            $student->class->name,
            $student->section->name,
        ];
    }
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Class Name',
            'Section Name',
        ];
    }
}
