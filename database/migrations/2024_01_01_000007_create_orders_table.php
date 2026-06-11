<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('shipping_charge', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->enum('status', ['pending','confirmed','processing','shipped','delivered','cancelled','refunded'])->default('pending');
            $table->enum('payment_status', ['pending','paid','failed','refunded'])->default('pending');
            $table->enum('payment_method', ['cod','stripe','sslcommerz'])->default('cod');
            $table->string('stripe_session_id')->nullable();
            $table->string('sslcommerz_tran_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->string('shipping_email');
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_state')->nullable();
            $table->string('shipping_zip')->nullable();
            $table->string('shipping_country')->default('Bangladesh');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};
