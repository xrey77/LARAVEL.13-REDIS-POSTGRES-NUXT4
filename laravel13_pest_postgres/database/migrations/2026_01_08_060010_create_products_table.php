<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });              

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();            
            $table->text('descriptions')->unique();            
            $table->integer('qty')->default(0);
            $table->string('unit');
            $table->decimal('costprice', 10, 2)->default(0);
            $table->decimal('sellprice', 10, 2)->default(0);
            $table->decimal('saleprice', 10, 2)->default(0);
            $table->string('productpicture')->nullable();
            $table->integer('alertstocks')->default(0);
            $table->integer('criticalstocks')->default(0);
            $table->timestamps();
        });        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');        
    }
};
