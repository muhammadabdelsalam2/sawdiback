<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {

            /**
             * UUID Primary Key
             */
            $table->uuid('id')
                ->primary()
                ->comment('Primary UUID for supplier');

            /**
             * Supplier name (company or individual)
             */
            $table->string('name')
                ->index()
                ->comment('Supplier full name / company name');

            /**
             * Contact email
             */
            $table->string('email')
                ->nullable()
                ->index()
                ->comment('Supplier email address');

            /**
             * Contact phone
             */
            $table->string('phone')
                ->nullable()
                ->index()
                ->comment('Supplier phone number');

            /**
             * Physical address
             */
            $table->text('address')
                ->nullable()
                ->comment('Supplier full address');

            /**
             * Active / inactive status
             */
            $table->boolean('is_active')
                ->default(true)
                ->index()
                ->comment('Supplier status: active or inactive');

            /**
             * Laravel timestamps
             */
            $table->timestamps();

            /**
             * Soft delete support (ERP safe delete)
             */
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
