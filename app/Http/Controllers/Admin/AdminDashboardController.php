<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Store;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'bookCount' => Book::count(),
            'sellerCount' => User::where('role', 'penjual')->count(),
            'storeCount' => Store::count(),
        ]);
    }

    public function getServerStats()
    {
        $cpuLoad = 0;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            try {
                $process = new Process(['wmic', 'cpu', 'get', 'loadpercentage']);
                $process->run();
                if ($process->isSuccessful()) {
                    $output = $process->getOutput();
                    $lines = explode("\n", trim($output));
                    if (count($lines) > 1) {
                        $cpuLoad = trim($lines[1]);
                    }
                }
            } catch (\Exception $e) {
                // Silently fail or log error
            }
        } else {
            // For Linux/macOS
            $load = sys_getloadavg();
            $cpuLoad = $load[0] * 100; // Simplistic conversion
        }

        // GPU stats are highly dependent on the system and hardware.
        // nvidia-smi for NVIDIA GPUs is common but not universally available.
        // For now, we'll return a placeholder for GPU.
        $gpuLoad = rand(10, 40); // Placeholder for GPU load

        return response()->json([
            'cpu' => $cpuLoad,
            'gpu' => $gpuLoad, // Placeholder
        ]);
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $totalBooks = Book::count();
        $totalOrders = Order::count();
        // ...statistik lain

        return view('admin.dashboard', compact('totalUsers', 'totalBooks', 'totalOrders'));
    }
}
