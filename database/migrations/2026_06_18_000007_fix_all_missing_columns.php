<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_invoice_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_invoice_lines', 'description')) {
                $table->text('description')->nullable()->after('item_id');
            }
            if (!Schema::hasColumn('sales_invoice_lines', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('sales_invoice_lines', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->after('tax_amount');
            }
            if (!Schema::hasColumn('sales_invoice_lines', 'warehouse_id')) {
                $table->foreignId('warehouse_id')->nullable()->after('total')->constrained('warehouses')->nullOnDelete();
            }
            if (!Schema::hasColumn('sales_invoice_lines', 'serial_numbers')) {
                $table->json('serial_numbers')->nullable()->after('warehouse_id');
            }
        });

        Schema::table('purchase_invoice_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_invoice_lines', 'description')) {
                $table->text('description')->nullable()->after('item_id');
            }
            if (!Schema::hasColumn('purchase_invoice_lines', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('purchase_invoice_lines', 'unit_cost')) {
                $table->decimal('unit_cost', 15, 2)->after('unit_price');
            }
            if (!Schema::hasColumn('purchase_invoice_lines', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->after('tax_amount');
            }
            if (!Schema::hasColumn('purchase_invoice_lines', 'warehouse_id')) {
                $table->foreignId('warehouse_id')->nullable()->after('total')->constrained('warehouses')->nullOnDelete();
            }
            if (!Schema::hasColumn('purchase_invoice_lines', 'serial_numbers')) {
                $table->json('serial_numbers')->nullable()->after('warehouse_id');
            }
            if (!Schema::hasColumn('purchase_invoice_lines', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('serial_numbers');
            }
        });

        Schema::table('item_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('item_categories', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name_en');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name_en');
            }
        });

        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name_en');
            }
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entries', 'journal_id')) {
                $table->foreignId('journal_id')->nullable()->after('reference')->constrained('journals')->nullOnDelete();
            }
            if (!Schema::hasColumn('journal_entries', 'total_debit')) {
                $table->decimal('total_debit', 15, 2)->default(0)->after('type');
            }
            if (!Schema::hasColumn('journal_entries', 'total_credit')) {
                $table->decimal('total_credit', 15, 2)->default(0)->after('total_debit');
            }
            if (!Schema::hasColumn('journal_entries', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('total_credit')->constrained('users')->nullOnDelete();
            }
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            if (!Schema::hasColumn('chart_of_accounts', 'balance')) {
                $table->decimal('balance', 15, 2)->default(0)->after('current_balance');
            }
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'shipping_cost')) {
                $table->decimal('shipping_cost', 15, 2)->default(0)->after('total');
            }
            if (!Schema::hasColumn('purchase_orders', 'currency_id')) {
                $table->foreignId('currency_id')->nullable()->after('shipping_cost')->constrained('currencies')->nullOnDelete();
            }
            if (!Schema::hasColumn('purchase_orders', 'exchange_rate')) {
                $table->decimal('exchange_rate', 15, 6)->default(1)->after('currency_id');
            }
            if (!Schema::hasColumn('purchase_orders', 'payment_term_id')) {
                $table->foreignId('payment_term_id')->nullable()->after('exchange_rate')->constrained('payment_terms')->nullOnDelete();
            }
            if (!Schema::hasColumn('purchase_orders', 'receipt_status')) {
                $table->string('receipt_status', 20)->nullable()->after('status');
            }
            if (!Schema::hasColumn('purchase_orders', 'invoice_status')) {
                $table->string('invoice_status', 20)->nullable()->after('receipt_status');
            }
            if (!Schema::hasColumn('purchase_orders', 'supplier_invoice_number')) {
                $table->string('supplier_invoice_number', 100)->nullable()->after('invoice_status');
            }
            if (!Schema::hasColumn('purchase_orders', 'reference')) {
                $table->string('reference', 100)->nullable()->after('supplier_invoice_number');
            }
            if (!Schema::hasColumn('purchase_orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('reference');
            }
            if (!Schema::hasColumn('purchase_orders', 'cancelled_reason')) {
                $table->text('cancelled_reason')->nullable()->after('cancelled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_invoice_lines', function (Blueprint $table) {
            $table->dropColumn(['description', 'tax_rate', 'subtotal', 'serial_numbers']);
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');
        });

        Schema::table('purchase_invoice_lines', function (Blueprint $table) {
            $table->dropColumn(['description', 'tax_rate', 'unit_cost', 'subtotal', 'serial_numbers', 'expiry_date']);
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn('warehouse_id');
        });

        Schema::table('item_categories', function (Blueprint $table) {
            $table->dropColumn('name_ar');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('name_ar');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('name_ar');
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['journal_id']);
            $table->dropColumn(['journal_id', 'total_debit', 'total_credit']);
            $table->dropForeign(['created_by']);
            $table->dropColumn('created_by');
        });

        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropColumn('balance');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropForeign(['payment_term_id']);
            $table->dropColumn([
                'shipping_cost', 'currency_id', 'exchange_rate', 'payment_term_id',
                'receipt_status', 'invoice_status', 'supplier_invoice_number',
                'reference', 'cancelled_at', 'cancelled_reason',
            ]);
        });
    }
};
