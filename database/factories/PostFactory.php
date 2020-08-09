<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Post;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {

	$images = ['about-bg.jpg', 'contact-bg.jpg', 'home-bg.jpg', 'post-bg.jpg'];
	$title = $faker->sentence(mt_rand(3, 10));


    return [
        'title' => $faker->sentence(mt_rand(3, 10)),
	    'subtitle' => str_limit($faker->sentence(mt_rand(10, 20)), 252),
	    'page_image' => $images[mt_rand(0,3)],
	    'content_raw' => join("\n\n", $faker->paragraphs(mt_rand(3, 6))),
	    'meta_description' => "Meta for $title",
	    'is_draft' => false,
        'published_at' => $faker->dateTimeBetween('-1 month', '+3 days'),
    ];
});
