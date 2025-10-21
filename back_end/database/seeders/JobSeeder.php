<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Job;


class JobSeeder extends Seeder
{
   

    
    public function run(): void
    {
         Job::factory(10)->create();
    }
}
