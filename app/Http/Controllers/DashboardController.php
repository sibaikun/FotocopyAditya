<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = now()->toDateString();

        $todayQuery = Transaction::whereDate('date', $today);
        $omzetToday = (clone $todayQuery)->sum('total');
        $countToday = (clone $todayQuery)->count();

        $omzetMonth = Transaction::whereBetween('date', [
            now()->startOfMonth()->toDateString(), $today,
        ])->sum('total');

        $lowStock = Product::where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(10)
            ->get();

        $latest = Transaction::with('user')->latest('id')->limit(5)->get();

        return view('dashboard', compact('omzetToday', 'countToday', 'omzetMonth', 'lowStock', 'latest'));
    }
}