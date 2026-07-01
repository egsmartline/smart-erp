<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trade_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->enum('type', ['import', 'export']);
            $table->string('operation_number');
            $table->date('date');
            $table->enum('status', ['draft', 'confirmed', 'shipped', 'cleared', 'completed', 'cancelled'])->default('draft');

            $table->unsignedBigInteger('party_id')->nullable();
            $table->string('party_name')->nullable();
            $table->string('party_type')->nullable();

            $table->string('country')->nullable();
            $table->string('port_of_loading')->nullable();
            $table->string('port_of_discharge')->nullable();
            $table->string('incoterm')->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies');
            $table->decimal('exchange_rate', 10, 4)->default(1);
            $table->decimal('total_value', 15, 2)->default(0);

            $table->string('shipping_method')->nullable();
            $table->string('container_number')->nullable();
            $table->string('vessel_name')->nullable();
            $table->string('bill_of_lading_number')->nullable();
            $table->date('etd_date')->nullable();
            $table->date('eta_date')->nullable();

            $table->string('lc_number')->nullable();
            $table->string('lc_issuing_bank')->nullable();
            $table->string('lc_beneficiary_bank')->nullable();
            $table->enum('lc_type', ['sight', 'deferred', 'standby'])->nullable();
            $table->decimal('lc_amount', 15, 2)->nullable();
            $table->date('lc_issue_date')->nullable();
            $table->date('lc_expiry_date')->nullable();

            $table->decimal('customs_value', 15, 2)->nullable();
            $table->decimal('customs_duty_amount', 15, 2)->nullable();
            $table->decimal('shipping_cost', 15, 2)->nullable();
            $table->decimal('insurance_cost', 15, 2)->nullable();
            $table->decimal('inspection_cost', 15, 2)->nullable();
            $table->decimal('other_costs', 15, 2)->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['tenant_id', 'operation_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('trade_operations');
    }
};