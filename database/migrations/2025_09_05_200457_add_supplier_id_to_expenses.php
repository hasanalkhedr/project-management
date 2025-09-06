<?php

use App\Models\Expense;
use App\Models\Supplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('supplier_id')
                ->nullable()
                ->after('supplier')
                ->constrained('suppliers')
                ->nullOnDelete();
        });
        // Get all unique supplier names from expenses
        $supplierNames = DB::table('expenses')
            ->whereNotNull('supplier')
            ->where('supplier', '!=', '')
            ->distinct()
            ->pluck('supplier');

        foreach ($supplierNames as $name) {
            // Create supplier record if it doesn't exist
            $supplier = Supplier::firstOrCreate(
                ['name' => $name],
                ['is_active' => true]
            );

            // Update expenses with the new supplier_id
            DB::table('expenses')
                ->where('supplier', $name)
                ->update(['supplier_id' => $supplier->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $expenses = Expense::whereNotNull('supplier_id')->get();

            foreach ($expenses as $expense) {
                if ($expense->supplier) {
                    DB::table('expenses')
                        ->where('id', $expense->id)
                        ->update([
                            'supplier' => $expense->supplier->name
                        ]);
                }
            }

            // Set all supplier_id to null
            DB::table('expenses')->update(['supplier_id' => null]);
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }
};
