<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RestructurePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('subtitle')->default('')->nullable()->after('title');
            $table->renameColumn('content', 'content_raw');
            $table->text('content_html')->after('content');
            $table->string('page_image')->default('')->nullable()->after('content_html');
            $table->string('meta_description')->default('')->nullable()->after('page_image');
            $table->tinyInteger('is_draft')->default(0)->unsigned()->after('meta_description');
            $table->string('layout')->after('is_draft')->default('blog.layouts.post');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('layout');
            $table->dropColumn('content_html');
            $table->dropColumn('meta_description');
            $table->dropColumn('page_image');
            $table->dropColumn('is_draft');
            $table->dropColumn('subtitle');
            $table->renameColumn('content_raw', 'content');
        });
    }
}
