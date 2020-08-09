<?php

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class PostTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$tags = Tag::all()->pluck('tag')->all();

		DB::table('posts')->truncate();
		DB::table('post_tag_pivot')->truncate();
		factory(Post::class, 20)->create()->each(function ($post) use ($tags) {
			if (mt_rand(1, 100) <= 30) {
				return;
			}

			shuffle($tags);
			$postTags = [$tags[0]];

			if (mt_rand(1, 100) <= 30) {
				$postTags[] = $tags[0];
			}

			$post->syncTags($postTags);

		});
	}
}
