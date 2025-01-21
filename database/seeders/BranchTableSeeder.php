<?php
namespace Database\Seeders;
use App\Branch;
use Illuminate\Database\Seeder;

class BranchTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Branch::create([
            'name' => 'Head Office',
            'location' => 'Uttara House Buildings, Dhaka Bangladesh',
            'description' => ''
        ]);
        Branch::create([
            'name' => 'Rajshahi Branch',
            'location' => 'Padma Graden',
            'description' => ''
        ]);
        Branch::create([
            'name' => 'Sirajgonj Branch',
            'location' => 'S.S Road',
            'description' => ''
        ]);
        Branch::create([
            'name' => 'Bogra Branch',
            'location' => 'Jolershari Tala',
            'description' => ''
        ]);
    }
}
