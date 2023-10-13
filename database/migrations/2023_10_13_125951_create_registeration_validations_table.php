<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registeration_validations', function (Blueprint $table) {
            $table->id();
            $table->string('verification_code');
            $table->boolean('is_verified_email')->default(0);
            $table->dateTime('expire_date')->default(Carbon::now()->addHours(24));
            $table->timestamp('account_verified_at')->nullable();
            $table->unsignedBigInteger('user_id');
            
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registeration_validations');
    }
};
