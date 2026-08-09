<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $t) {
            $t->id();
            $t->string('service_type')->default('Enkel service');
            $t->string('name');
            $t->string('email');
            $t->string('phone')->nullable();
            $t->text('message');
            $t->date('week_start');
            $t->boolean('wants_pickup')->default(false);
            $t->string('address')->nullable();
            $t->string('status')->default('Venter');
            $t->json('images')->nullable();
            $t->timestamps();

            $t->index(['week_start', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
