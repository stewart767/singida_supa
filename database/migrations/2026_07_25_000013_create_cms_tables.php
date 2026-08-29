<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_news', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('image_path')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('cms_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('location')->nullable();
            $table->dateTime('event_date');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('cms_downloads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('General');
            $table->string('file_path');
            $table->integer('file_size_bytes')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image_path');
            $table->string('category')->default('Campus Life');
            $table->timestamps();
        });

        Schema::create('cms_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->default('General');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('cms_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_contacts');
        Schema::dropIfExists('cms_faqs');
        Schema::dropIfExists('cms_galleries');
        Schema::dropIfExists('cms_downloads');
        Schema::dropIfExists('cms_events');
        Schema::dropIfExists('cms_news');
        Schema::dropIfExists('cms_pages');
    }
};
