<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    protected $pythonBackendUrl;

    public function __construct()
    {
        $this->pythonBackendUrl = env('PYTHON_BACKEND_URL', 'http://localhost:8000');
    }

    public function index()
    {
        try {
            $response = Http::get($this->pythonBackendUrl . '/admin/dashboard');
            $data = $response->successful() ? $response->json() : ['bins' => [], 'recommended_route' => [], 'stats' => []];
            
            return view('dashboard', [
                'bins' => $data['bins'] ?? [],
                'route' => $data['recommended_route'] ?? [],
                'stats' => $data['stats'] ?? [
                    'total_users' => 0,
                    'total_waste' => 0,
                    'active_bins' => 0,
                    'full_bins' => 0
                ]
            ]);
        } catch (\Exception $e) {
            return view('dashboard', ['error' => 'Could not connect to AI Server', 'bins' => [], 'route' => [], 'stats' => []]);
        }
    }

    public function binMonitoring()
    {
        $response = Http::get($this->pythonBackendUrl . '/admin/dashboard');
        $data = $response->json();
        return view('bin-monitoring', ['bins' => $data['bins'] ?? []]);
    }

    public function routeMap()
    {
        $response = Http::get($this->pythonBackendUrl . '/admin/dashboard');
        $data = $response->json();
        return view('route-map', ['route' => $data['recommended_route'] ?? [], 'bins' => $data['bins'] ?? []]);
    }

    public function airMonitoring()
    {
        $response = Http::get($this->pythonBackendUrl . '/admin/dashboard');
        $data = $response->json();
        return view('air-monitoring', ['pollution' => $data['pollution_map'] ?? []]);
    }
}
