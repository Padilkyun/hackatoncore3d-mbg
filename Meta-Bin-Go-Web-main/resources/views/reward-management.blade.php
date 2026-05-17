<x-app-layout>
    <div class="flex h-full bg-[#F8F9F5] font-['Poppins'] overflow-hidden">
        <main class="flex-1 h-full overflow-hidden p-4 md:p-6">
            <div class="flex flex-col lg:flex-row justify-between items-start gap-4 mb-6">
                <div>
                    <p class="text-gray-500 text-sm">Kelola Hadiah Pengguna</p>
                    <h1 class="text-3xl md:text-4xl font-bold text-black mt-1">Reward Management</h1>
                </div>
                <div class="bg-white px-5 py-2 rounded-2xl flex items-center space-x-3 border border-gray-100 shadow-sm">
                    <div class="w-8 h-8 bg-[#062C1B] rounded-full flex items-center justify-center text-white">
                        <span class="text-sm font-bold">A</span>
                    </div>
                    <span class="font-bold text-[#062C1B]">Admin</span>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
                <section class="bg-white rounded-[32px] border border-gray-100 shadow-[0_10px_30px_rgba(0,0,0,0.08)] p-6 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Active Vouchers</p>
                        <p class="text-4xl font-bold text-[#1B1B18] mt-2">{{ number_format($activeVouchersCount) }}</p>
                        <p class="text-xs text-gray-500">Vouchers</p>
                    </div>
                    <div class="w-16 h-16 rounded-3xl bg-[#E8F7EA] flex items-center justify-center text-[#062C1B] text-2xl font-bold">%</div>
                </section>
                <section class="bg-white rounded-[32px] border border-gray-100 shadow-[0_10px_30px_rgba(0,0,0,0.08)] p-6 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Points Redeemed</p>
                        <p class="text-4xl font-bold text-[#1B1B18] mt-2">{{ number_format($pointsRedeemed / 1000, 1) }}K</p>
                        <p class="text-xs text-gray-500">Points</p>
                    </div>
                    <div class="w-16 h-16 rounded-3xl bg-[#F2F9F9] flex items-center justify-center text-[#062C1B] text-2xl font-bold">★</div>
                </section>
            </div>

            <section class="bg-white rounded-[32px] border border-gray-100 shadow-[0_10px_30px_rgba(0,0,0,0.08)] p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                    <h2 class="text-xl font-bold text-[#1B1B18]">Reward Catalog</h2>
                    <a href="{{ route('reward-management.create') }}" class="inline-flex items-center gap-2 rounded-full border border-[#062C1B] px-4 py-2 text-[#062C1B] font-semibold hover:bg-[#062C1B] hover:text-white transition">
                        <span class="text-xl">+</span>
                        Tambah Reward
                    </a>
                </div>

                <div class="overflow-hidden rounded-[28px] border border-gray-200">
                    <table class="min-w-full text-left text-sm text-gray-700">
                        <thead class="bg-[#062C1B] text-white">
                            <tr>
                                <th class="px-4 py-4">Foto Reward</th>
                                <th class="px-4 py-4">Nama Reward</th>
                                <th class="px-4 py-4">Keterangan</th>
                                <th class="px-4 py-4">Syarat Point</th>
                                <th class="px-4 py-4">Kuota</th>
                                <th class="px-4 py-4">Status</th>
                                <th class="px-4 py-4">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach ($rewards as $reward)
                                <tr class="border-b border-gray-100">
                                    <td class="px-4 py-4">
                                        <div class="w-16 h-10 bg-gray-50 border border-gray-100 rounded-md flex items-center justify-center overflow-hidden">
                                            <img src="{{ asset('storage/' . $reward->foto_reward) }}" alt="{{ $reward->nama_reward }}" class="object-contain max-h-full">
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 font-semibold text-[#1B1B18]">{{ $reward->nama_reward }}</td>
                                    <td class="px-4 py-4 text-gray-500 text-xs truncate max-w-[200px]">{{ $reward->keterangan }}</td>
                                    <td class="px-4 py-4 font-semibold">{{ number_format($reward->syarat_point) }} Points</td>
                                    <td class="px-4 py-4">
                                        <div class="text-xs text-gray-500 mb-2">{{ $reward->kuota }} Tersedia</div>
                                        <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                                            <div class="h-full rounded-full bg-[#062C1B]" style="width: 100%"></div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $reward->status == 'Active' ? 'bg-[#D5F6DD] text-[#1E6B36]' : 'bg-red-100 text-red-600' }}">
                                            {{ $reward->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 space-x-2">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('reward-management.edit', $reward->id) }}" class="px-4 py-2 rounded-full bg-[#062C1B] text-white text-[11px]">Edit</a>
                                            <form action="{{ route('reward-management.destroy', $reward->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus reward ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 rounded-full bg-[#F25E5E] text-white text-[11px]">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if($rewards->isEmpty())
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 italic">
                                        Belum ada reward yang ditambahkan.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</x-app-layout>
