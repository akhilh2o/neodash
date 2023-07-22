<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Akhilesh\Neodash\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run()
    {
        Faq::truncate();
        Faq::factory(15)->create();
    }
}
