<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Everything the public portfolio at "/" renders.
 *
 * Single-value copy (hero text, about, contact, footer, SEO…) lives in
 * page_contents as key/value rows; repeatable blocks get their own table.
 * Projects are the parent of project_images / project_files / project_links.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('nav_items', function (Blueprint $table) {
            $table->id();
            $table->string('label', 40);
            $table->string('target', 200);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->string('value', 20);
            $table->string('label', 60);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title', 80);
            $table->string('icon', 60)->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('category', 40)->nullable();
            $table->unsignedTinyInteger('level')->default(80);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('role', 100);
            $table->string('company', 100);
            $table->string('period', 60);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('position', 100)->nullable();
            $table->text('quote');
            $table->string('avatar')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 30);
            $table->string('label', 60)->nullable();
            $table->string('url', 300);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('slug', 80)->unique();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // --- Parent ---
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20)->default('project'); // project | product
            $table->string('title', 150);
            $table->string('slug', 170)->unique();
            $table->string('summary', 300)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('client', 100)->nullable();
            $table->string('year', 10)->nullable();
            $table->string('tech_stack', 300)->nullable();
            $table->string('meta_title', 150)->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['is_published', 'sort_order']);
            $table->index(['is_published', 'is_featured']);
        });

        // --- Children ---
        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('thumb_path')->nullable();
            $table->string('caption', 150)->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['project_id', 'sort_order']);
        });

        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('original_name', 200);
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['project_id', 'sort_order']);
        });

        Schema::create('project_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('label', 60);
            $table->string('url', 300);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['project_id', 'sort_order']);
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150);
            $table->string('phone', 30)->nullable();
            $table->string('subject', 150)->nullable();
            $table->string('budget', 60)->nullable();
            $table->text('message');
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->boolean('mail_sent')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['read_at', 'created_at']);
        });
    }

    public function down(): void
    {
        foreach ([
            'contact_messages', 'project_links', 'project_files', 'project_images', 'projects',
            'project_categories', 'social_links', 'testimonials', 'experiences', 'skills',
            'services', 'stats', 'nav_items', 'page_contents',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
