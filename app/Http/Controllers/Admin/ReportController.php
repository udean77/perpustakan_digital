<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Tampilkan daftar laporan
    public function index()
    {
        $reports = Report::with(['user', 'reportable'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.reports.index', compact('reports'));
    }

    // Tampilkan detail laporan
    public function show($id)
    {
        $report = Report::with(['user', 'reportable'])->findOrFail($id);
        return view('admin.reports.show', compact('report'));
    }

    // Update status laporan
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,process,resolved',
        ]);

        $report = Report::findOrFail($id);
        $report->status = $request->status;
        $report->save();

        return redirect()->route('admin.reports.show', $id)
            ->with('success', 'Status laporan berhasil diupdate.');
    }

    // Hapus laporan
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}
