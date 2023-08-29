<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            ['id'=>1,'title'=> '','price'=>'144','is_recurring'=>1],
            ['id'=>2,'title'=> '','price'=>'199','is_recurring'=>1],
            ['id'=>3,'title'=> '','price'=>'44','is_recurring'=>0],
        ];
        foreach($data as $datum) {
            Package::create($datum);
        }
    }
}
