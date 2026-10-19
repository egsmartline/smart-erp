<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class TenantAwareController extends Controller
{
    protected function getTenantId(): int
    {
        return session('current_tenant_id') ?? auth()->user()->tenant_id;
    }

    protected function tenantQuery($model)
    {
        return $model::where('tenant_id', $this->getTenantId());
    }

    /**
     * The cost of one unit of an item as it sits in a given warehouse.
     * The weighted average is what the sub-ledger and the journal inventory
     * account are maintained at, so it wins whenever it is known; the item's
     * cost_price is only the seed for stock that was never purchased.
     */
    protected function effectiveUnitCost(int $itemId, int $warehouseId): float
    {
        $average = ItemWarehouse::query()
            ->where('tenant_id', $this->getTenantId())
            ->where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->value('average_cost');

        if ($average !== null && (float) $average > 0) {
            return (float) $average;
        }

        return (float) (Item::query()
            ->where('tenant_id', $this->getTenantId())
            ->where('id', $itemId)
            ->value('cost_price') ?? 0);
    }

    /**
     * Sequential document number that cannot collide.
     *
     * The unique key keeps soft-deleted rows in play while count() does not, so
     * the usual count()+1 breaks the first time a document is deleted. This
     * probes the real table until it finds a free number. Pass a null tenant
     * when the column is unique across the whole table.
     */
    protected function nextSequentialNumber(string $table, string $column, ?int $tenantId, string $prefix, string $dateFormat = 'Ymd'): string
    {
        $datePrefix = $prefix . '-' . now()->format($dateFormat) . '-';

        $scope = fn () => DB::table($table)
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId));

        $seq = (int) $scope()->count() + 1;

        do {
            $number = $datePrefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
            $seq++;
        } while ($scope()->where($column, $number)->exists());

        return $number;
    }

    /**
     * Sequential document number built from the highest sequence already used
     * for one prefix and year.
     *
     * Matching the prefix in SQL matters: max() over a bare column is
     * lexicographic, so an unrelated number in the same column (voided rows
     * carrying a "VER-" prefix, say) sorts above every padded sequence and
     * silently produces a number that is already taken.
     */
    protected function nextPrefixedNumber(string $table, string $column, ?int $tenantId, string $prefix, string $year): string
    {
        $fullPrefix = $prefix . '-' . $year . '-';

        $scope = fn () => DB::table($table)
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where($column, 'like', $fullPrefix . '%');

        $seq = 1;
        foreach ($scope()->pluck($column) as $number) {
            if (preg_match('/(\d+)$/', (string) $number, $matches)) {
                $seq = max($seq, (int) $matches[1] + 1);
            }
        }

        do {
            $candidate = $fullPrefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
            $seq++;
        } while ($scope()->where($column, $candidate)->exists());

        return $candidate;
    }
}
