<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Book;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $reports = Report::where('user_id', Auth::id())->with('reportable')->get();
        return view('user.reports.index', compact('reports'));
    }


    public function create()
    {
        $user = Auth::user();

        $products = Book::all(); // buku
        $sellers = User::where('role', 'penjual')->get(['id', 'nama']);
        $orders = Order::where('user_id', $user->id)->get(); // data orders user

        return view('user.reports.create', compact('products', 'sellers', 'orders'));
    }




    public function store(Request $request)
    {
        $request->validate([
            'reportable_type' => 'required|string|in:product,seller,order',
            'reportable_id' => 'required|integer',
            'reason' => 'required|string|max:1000',
        ]);

        $typeMap = [
            'product' => \App\Models\Book::class,
            'seller' => \App\Models\User::class,
            'order' => \App\Models\Order::class,
        ];

        $reportableType = $typeMap[$request->reportable_type] ?? null;

        if (!$reportableType || !$reportableType::find($request->reportable_id)) {
            return back()->withErrors(['reportable_id' => 'Item tidak valid.'])->withInput();
        }

        Report::create([
            'user_id' => Auth::id(),
            'reportable_type' => $reportableType,
            'reportable_id' => $request->reportable_id,
            'reason' => $request->reason,
        ]);

        return redirect()->route('user.reports.index')->with('success', 'Laporan berhasil dikirim.');
    }

}
