<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('client_name', 255);
            $table->decimal('total_price', 7,2)->nullable();
            $table->date('due_date')->nullable();
            $table->text('description');
            $table->enum('status', ['paid', 'rejected', 'not_paid'])->default('not_paid');
            $table->boolean('active')->default(false);
            $table->text('response_status')->nullable();
            $table->string('token')->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
