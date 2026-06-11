<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->json('variant')->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('price', 10, 2);
            $table->timestamps();
            $table->index(['user_id', 'product_id']);
            $table->index('session_id');
        });
    }
    public function down(): void { Schema::dropIfExists('carts'); }
};
