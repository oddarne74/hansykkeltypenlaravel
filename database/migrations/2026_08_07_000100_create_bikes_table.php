<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bikes', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('brand');
            $t->string('model');
            $t->string('type');
            $t->unsignedInteger('price');
            $t->string('status')->default('Til salgs');
            $t->string('size');
            $t->string('rider_height')->nullable();
            $t->string('wheel_size')->nullable();
            $t->string('gears')->nullable();
            $t->string('frame')->nullable();
            $t->string('brakes')->nullable();
            $t->string('color')->nullable();
            $t->string('year')->nullable();
            $t->text('description');
            $t->text('condition_notes')->nullable();
            $t->boolean('featured')->default(false);
            $t->timestamp('published_at')->nullable()->index();
            $t->timestamps();
        });
        Schema::create('bike_images', function (Blueprint $t) {
            $t->id();
            $t->foreignId('bike_id')->constrained()->cascadeOnDelete();
            $t->string('path');
            $t->string('alt');
            $t->string('stage')->default('after');
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->timestamps();
        });
        Schema::create('bike_work_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('bike_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->text('description')->nullable();
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bike_work_items');
        Schema::dropIfExists('bike_images');
        Schema::dropIfExists('bikes');
    }
};
