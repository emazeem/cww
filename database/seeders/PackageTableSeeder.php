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
            ['id'=>1,'title'=> 'Basic','price'=>'149','is_recurring'=>1],
            ['id'=>2,'title'=> 'Premium','price'=>'199','is_recurring'=>1],
            ['id'=>3,'title'=> 'One Time','price'=>'45','is_recurring'=>0],
        ];
        foreach($data as $datum) {
            Package::create($datum);
        }
    }
}
