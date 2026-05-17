<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RewardManagementController extends Controller
{
    public function index()
    {
        $rewards = Reward::all();
        $activeVouchersCount = Reward::where('status', 'Active')->sum('kuota');
        $pointsRedeemed = 452300; // Placeholder

        return view('reward-management', compact('rewards', 'activeVouchersCount', 'pointsRedeemed'));
    }

    public function create()
    {
        return view('reward-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_reward' => 'required|string|max:255',
            'syarat_point' => 'required|integer',
            'kuota' => 'required|integer',
            'foto_reward' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'keterangan' => 'required|string',
        ]);

        $path = $request->file('foto_reward')->store('rewards', 'public');

        Reward::create([
            'nama_reward' => $request->nama_reward,
            'syarat_point' => $request->syarat_point,
            'kuota' => $request->kuota,
            'foto_reward' => $path,
            'keterangan' => $request->keterangan,
            'status' => 'Active',
        ]);

        return redirect()->route('reward-management')->with('success', 'Reward created successfully.');
    }

    public function edit($id)
    {
        $reward = Reward::findOrFail($id);
        return view('reward-edit', compact('reward'));
    }

    public function update(Request $request, $id)
    {
        $reward = Reward::findOrFail($id);
        
        $request->validate([
            'nama_reward' => 'required|string|max:255',
            'syarat_point' => 'required|integer',
            'kuota' => 'required|integer',
            'foto_reward' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'keterangan' => 'required|string',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = $request->only(['nama_reward', 'syarat_point', 'kuota', 'keterangan', 'status']);

        if ($request->hasFile('foto_reward')) {
            if ($reward->foto_reward) {
                Storage::disk('public')->delete($reward->foto_reward);
            }
            $data['foto_reward'] = $request->file('foto_reward')->store('rewards', 'public');
        }

        $reward->update($data);

        return redirect()->route('reward-management')->with('success', 'Reward updated successfully.');
    }

    public function destroy($id)
    {
        $reward = Reward::findOrFail($id);
        if ($reward->foto_reward) {
            Storage::disk('public')->delete($reward->foto_reward);
        }
        $reward->delete();

        return redirect()->route('reward-management')->with('success', 'Reward deleted successfully.');
    }
}
