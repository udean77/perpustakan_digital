<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // Tampilkan daftar laporan
    public function index()
    {
        $userId = auth()->id();

        // Ambil laporan buku yang buku-nya milik seller yang login
        $reports = Report::where('reportable_type', 'App\Models\Book')
            ->get()
            ->filter(function ($report) use ($userId) {
                return $report->reportable && $report->reportable->user_id == $userId;
            });

        return view('seller.reports.index', compact('reports'));
    }



    // Tampilkan detail laporan
    public function show($id)
    {
        $report = Report::with('reportable', 'user')->findOrFail($id);

        // Cek kalau laporan milik seller yang sedang login, kalau perlu
        if ($report->reportable->user_id !== auth()->id()) {
            abort(403);
        }

        return view('seller.reports.show', compact('report'));
    }

    // Update status laporan
    public function updateStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        // Cek akses
        if (!$report->reportable || $report->reportable->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        $request->validate([
            'status' => 'required|in:pending,process,resolved',
        ]);

        $report->status = $request->status;
        $report->save();

        return redirect()->route('seller.reports.show', $report->id)
                         ->with('success', 'Status laporan berhasil diperbarui.');
    }

    // Hapus laporan
    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        // Cek akses
        if (!$report->reportable || $report->reportable->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        $report->delete();

        return redirect()->route('seller.reports.index')
                         ->with('success', 'Laporan berhasil dihapus.');
    }
}
