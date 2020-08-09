<?php

use Illuminate\Database\Seeder;
use App\Models\Tag;


class TagTableSeeder extends Seeder
{
	public function run()
	{
		DB::table('tags')->truncate();
		factory(Tag::class)->create();
	}
}