<?php

use Illuminate\Database\Seeder;
use App\Models\Drug;

class DrugsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
Drug::insert([
    [
        'code' => 'drug1',
        'name' => 'Изиклин'
    ],
    [
        'code' => 'drug2',
        'name' => 'Фортранс'
    ]
]);
    }
}
