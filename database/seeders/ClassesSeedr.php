<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ClassesSeedr extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Classes::factory()
        ->count(10)
        ->sequence(fn ($sequence) => ['name' =>'Class'. $sequence->index + 1])
        ->has(
            Section::factory()
            ->count(5)
            ->state(
                new Sequence(
                ['name' => 'Sections A'],
                ['name' => 'Sections B'],
                )
            )
            ->has(
                Student::factory()
                ->count(20)
                ->state(
                    function(array $attributes, Section $section){
                        return [
                            'class_id' => $section->class_id,
                        ];
                    }
                )
            )
        )
        ->create();
    }
}
