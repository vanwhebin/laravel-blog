<?php
return [
	'name' => 'Laravel 博客',
    'title' => 'My Blog',
    'subtitle' => 'Laravel 5.8开发测试博客',
    'description' => '用于测试laravel5.8的开发',
	'author' => 'Wan Weibin',
	'page_image' => 'home-bg.jpg',
    'per_page' => 5,
    'rss_size' => 25,
    'uploads' => [
        'storage' => 'public',
        'webpath' => '/storage',
    ],
    'contact_email'=> env('MAIL_FROM_ADDRESS'),
];
