<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\StockMovement;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'period' => 'nullable|in:daily,weekly,monthly',
            'date'   => 'nullable|date',
        ]);

        $period = $request->input('period', 'daily');
        $date   = $request->filled('date') ? Carbon::parse($request->date) : now();

        [$start, $end] = match ($period) {
            'weekly'  => [$date->copy()->startOfWeek(), $date->copy()->endOfWeek()],
            'monthly' => [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()],
            default   => [$date->copy()->startOfDay(), $date->copy()->endOfDay()],
        };

        $label = match ($period) {
            'weekly'  => $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y'),
            'monthly' => $start->translatedFormat('F Y'),
            default   => $start->translatedFormat('l, d F Y'),
        };

        $from = $start->toDateString();
        $to   = $end->toDateString();

        $base = Transaction::whereBetween('date', [$from, $to]);

        $omzet = (clone $base)->sum('total');
        $count = (clone $base)->count();

        // pengeluaran = total belanja stok (barang masuk)
        $expenseQuery = StockMovement::where('type', 'in')->whereBetween('date', [$from, $to]);
        $expense      = (clone $expenseQuery)->sum('total_cost');
        // pengeluaran dipisah: barang dagangan vs bahan internal
        $expenseSplit = StockMovement::query()
            ->join('products', 'products.id', '=', 'stock_movements.product_id')
            ->where('stock_movements.type', 'in')
            ->whereBetween('stock_movements.date', [$from, $to])
            ->groupBy('products.is_for_sale')
            ->selectRaw('products.is_for_sale, SUM(stock_movements.total_cost) as total')
            ->pluck('total', 'is_for_sale');

        $expenseGoods    = (int) $expenseSplit->get(1, 0);
        $expenseSupplies = (int) $expenseSplit->get(0, 0);

        $byType = TransactionItem::query()
            ->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->whereBetween('transactions.date', [$from, $to])
            ->groupBy('transaction_items.item_type')
            ->selectRaw('transaction_items.item_type, SUM(transaction_items.subtotal) as total')
            ->pluck('total', 'item_type');

        // rincian per hari: pendapatan dan pengeluaran digabung per tanggal
        $income = (clone $base)
            ->selectRaw('date, COUNT(*) as count, SUM(total) as total')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $spent = (clone $expenseQuery)
            ->selectRaw('date, SUM(total_cost) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $rows = $income->keys()->merge($spent->keys())->unique()->sort()->values()
            ->map(fn ($d) => [
                'date'    => $d,
                'count'   => $income->get($d)?->count ?? 0,
                'income'  => (int) ($income->get($d)?->total ?? 0),
                'expense' => (int) $spent->get($d, 0),
            ]);

        $top = TransactionItem::query()
            ->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->whereBetween('transactions.date', [$from, $to])
            ->groupBy('transaction_items.item_name')
            ->selectRaw('transaction_items.item_name, SUM(transaction_items.base_qty) as qty, SUM(transaction_items.subtotal) as total')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('reports.index', [
            'period'      => $period,
            'date'        => $date->toDateString(),
            'label'       => $label,
            'omzet'       => $omzet,
            'expense'     => $expense,
            'expenseGoods'    => $expenseGoods,
            'expenseSupplies' => $expenseSupplies,
            'net'         => $omzet - $expense,
            'count'       => $count,
            'productSale' => $byType['product'] ?? 0,
            'serviceSale' => $byType['service'] ?? 0,
            'rows'        => $rows,
            'top'         => $top,
        ]);
    }
}