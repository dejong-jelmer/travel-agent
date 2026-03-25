<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        BlogPost::factory(8)
            ->published()
            ->withHeroImage()
            ->create();

        BlogPost::factory(2)
            ->draft()
            ->withHeroImage()
            ->create();
    }
}
