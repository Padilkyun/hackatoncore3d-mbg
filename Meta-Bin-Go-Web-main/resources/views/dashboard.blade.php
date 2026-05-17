<x-app-layout>
    <div class="flex h-full bg-[#F8F9F5] font-['Poppins'] overflow-hidden">
        <main class="flex-1 h-full overflow-hidden p-4 md:p-4">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-4">
                <div>
                    <p class="text-gray-500 text-sm">Welcome to Meta Bin Go Management System, <span class="font-bold text-[#062C1B]">Admin</span></p>
                    <h1 class="text-3xl md:text-4xl font-bold text-black mt-1">Dashboard</h1>
                </div>
                <div class="bg-white px-5 py-2 rounded-2xl flex items-center space-x-3 border border-gray-100" style="box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25);">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <x-heroicon-s-user class="w-5 h-5 text-gray-600" />
                    </div>
                    <span class="font-bold text-[#062C1B]">Admin</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">
                <div class="bg-white p-3 rounded-[15px] border border-gray-50 flex flex-col justify-between h-28" style="box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25);">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 font-semibold leading-tight text-xs md:text-sm ">Total<br>User</span>
                        <x-heroicon-s-user class="w-7 h-7 text-black" />
                    </div>
                    <div>
                        <span class="text-2xl md:text-3xl font-bold">{{ number_format($stats['total_users'] ?? 0) }}</span>
                        <span class="text-[10px] text-gray-400 ml-1">User</span>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-[15px] border border-gray-50 flex flex-col justify-between h-28" style="box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25);">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 font-semibold leading-tight text-xs md:text-sm">Total<br>Sampah</span>
                        <x-heroicon-s-archive-box class="w-7 h-7 text-black" />
                    </div>
                    <div>
                        <span class="text-2xl md:text-3xl font-bold">{{ number_format($stats['total_waste'] ?? 0) }}</span>
                        <span class="text-[10px] text-gray-400 ml-1">Items</span>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-[15px] border border-gray-50 flex flex-col justify-between h-28" style="box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25);">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 font-semibold leading-tight text-xs md:text-sm">Active Bin</span>
                        <x-heroicon-s-trash class="w-7 h-7 text-black" />
                    </div>
                    <div>
                        <span class="text-2xl md:text-3xl font-bold">{{ $stats['active_bins'] ?? 0 }}</span>
                        <span class="text-[10px] text-gray-400 ml-1">Bin</span>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-[15px] border border-gray-50 flex flex-col justify-between h-28" style="box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25);">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 font-semibold leading-tight text-xs md:text-sm">Full Bin</span>
                        <x-heroicon-s-trash class="w-7 h-7 text-red-600" />
                    </div>
                    <div>
                        <span class="text-2xl md:text-3xl font-bold text-red-600">{{ $stats['full_bins'] ?? 0 }}</span>
                        <span class="text-[10px] text-gray-400 ml-1">Bin</span>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-[15px] border border-gray-50 flex flex-col justify-between h-28" style="box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25);">
                    <div class="flex justify-between items-start">
                        <span class="text-gray-500 font-semibold leading-tight text-xs md:text-sm">Reward<br>Hari ini</span>
                        <x-heroicon-s-ticket class="w-7 h-7 text-black" />
                    </div>
                    <div>
                        <span class="text-2xl md:text-3xl font-bold">203</span>
                        <span class="text-[10px] text-gray-400 ml-1">Voucher</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-4">
                <div class="xl:col-span-2 bg-white p-5 rounded-[15px] border border-gray-100" style="box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25);">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg md:text-xl font-bold">Statistik Sampah</h3>
                        <span class="text-xs text-gray-500">Minggu ini</span>
                    </div>
                    <div class="bg-[#FFFFFF] rounded-[28px] p-3">
                        <div class="flex gap-3">
                            <div class="w-14 flex h-40 flex-col justify-between text-xs text-gray-500">
                                <span class="pt-1">1000</span>
                                <span>750</span>
                                <span>500</span>
                                <span>250</span>
                                <span class="pb-1">0</span>
                            </div>
                            <div class="flex-1">
                                <div class="relative h-40">
                                    <div class="absolute inset-x-0 top-[16.666%] border-t border-gray-200"></div>
                                    <div class="absolute inset-x-0 top-[33.333%] border-t border-gray-200"></div>
                                    <div class="absolute inset-x-0 top-[50%] border-t border-gray-200"></div>
                                    <div class="absolute inset-x-0 top-[66.666%] border-t border-gray-200"></div>
                                    <div class="absolute inset-x-0 top-[83.333%] border-t border-gray-200"></div>
                                    <div class="h-full grid grid-cols-7 gap-2 items-end">
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="flex items-end gap-0.5">
                                                <div class="w-10 h-20 bg-[#062C1B]"></div>
                                                <div class="w-10 h-24 bg-[#6CDD46]"></div>
                                            </div>
                                            <span class="text-[9px] text-gray-500">Mon</span>
                                        </div>
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="flex items-end gap-0.5">
                                                <div class="w-10 h-28 bg-[#062C1B]"></div>
                                                <div class="w-10 h-32 bg-[#6CDD46]"></div>
                                            </div>
                                            <span class="text-[9px] text-gray-500">Tues</span>
                                        </div>
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="flex items-end gap-0.5">
                                                <div class="w-10 h-16 bg-[#062C1B]"></div>
                                                <div class="w-10 h-12 bg-[#6CDD46]"></div>
                                            </div>
                                            <span class="text-[9px] text-gray-500">Wed</span>
                                        </div>
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="flex items-end gap-0.5">
                                                <div class="w-10 h-24 bg-[#062C1B]"></div>
                                                <div class="w-10 h-20 bg-[#6CDD46]"></div>
                                            </div>
                                            <span class="text-[9px] text-gray-500">Thurs</span>
                                        </div>
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="flex items-end gap-0.5">
                                                <div class="w-10 h-16 bg-[#062C1B]"></div>
                                                <div class="w-10 h-14 bg-[#6CDD46]"></div>
                                            </div>
                                            <span class="text-[9px] text-gray-500">Fri</span>
                                        </div>
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="flex items-end gap-0.5">
                                                <div class="w-10 h-28 bg-[#062C1B]"></div>
                                                <div class="w-10 h-28 bg-[#6CDD46]"></div>
                                            </div>
                                            <span class="text-[9px] text-gray-500">Sat</span>
                                        </div>
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="flex items-end gap-0.5">
                                                <div class="w-10 h-24 bg-[#062C1B]"></div>
                                                <div class="w-10 h-16 bg-[#6CDD46]"></div>
                                            </div>
                                            <span class="text-[9px] text-gray-500">Sun</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-center gap-6 mt-2 text-xs font-semibold text-gray-600">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#062C1B]"></span>
                                        <span>Organik</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#6CDD46]"></span>
                                        <span>Non organik</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-[15px] border border-gray-100" style="box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25);">
                    <h3 class="text-lg font-bold mb-4">Persentase Organik dan Non-Organik</h3>
                    <div class="flex flex-col lg:flex-row items-center gap-4">
                        <div class="relative w-40 h-40">
                            <svg viewBox="0 0 36 36" class="w-full h-full">
                                <defs>
                                    <linearGradient id="donutGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#0D2716" />
                                        <stop offset="100%" stop-color="#0F5B2B" />
                                    </linearGradient>
                                </defs>
                                <circle cx="18" cy="18" r="16" fill="none" class="stroke-gray-200" stroke-width="4" />
                                <circle cx="18" cy="18" r="16" fill="none" stroke="url(#donutGradient)" stroke-width="4" stroke-dasharray="82, 100" stroke-linecap="round" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-4xl font-black">82%</span>
                                <span class="text-[8px] uppercase text-gray-400">Non-Organik</span>
                            </div>
                        </div>
                        <div class="flex-1 space-y-4 w-full">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-4 rounded-full bg-gradient-to-r from-[#0D2716] to-[#0F5B2B]"></span>
                                    <div>
                                        <p class="text-sm font-semibold">Non-Organik</p>
                                        <p class="text-xs text-gray-500">(57.311 Items)</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">82%</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="w-10 h-4 rounded-full bg-gray-700 opacity-20"></span>
                                    <div>
                                        <p class="text-sm font-semibold">Organik</p>
                                        <p class="text-xs text-gray-500">(35.024 Items)</p>
                                    </div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">18%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
                <div class="xl:col-span-2 bg-white p-5 rounded-[15px] border border-gray-100" style="box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.25);">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg md:text-xl font-bold">Riwayat Status Bin</h3>
                        <span class="text-xs text-gray-500">Terbaru</span>
                    </div>
                    <div class="overflow-hidden rounded-[0px]">
                        <table class="w-full text-xs md:text-sm">
                            <thead class="bg-[#062C1B] text-white">
                                <tr>
                                    <th class="p-3 text-left text-xs md:text-sm">Nama Bin</th>
                                    <th class="p-3 text-left text-xs md:text-sm">Status</th>
                                    <th class="p-3 text-left text-xs md:text-sm">Lokasi</th>
                                    <th class="p-3 text-center text-xs md:text-sm">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="border-b border-gray-50">
                                    <td class="p-3 md:p-4 font-bold">Bin Simpang Haru</td>
                                    <td class="p-3 md:p-4 text-red-600 font-bold">Penuh</td>
                                    <td class="p-3 md:p-4 text-gray-400 text-[9px] md:text-[10px] max-w-[180px]">Jl. Hilligoo No.67, Kampung Pondok, Padang Barat, Padang City</td>
                                    <td class="p-3 md:p-4 flex justify-center space-x-2">
                                        <button class="bg-red-600 text-white px-3 py-1 rounded-full text-[10px]">Lihat</button>
                                        <button class="bg-[#062C1B] text-white px-3 py-1 rounded-full text-[10px]">Jemput</button>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-50">
                                    <td class="p-3 md:p-4 font-bold">Bin Pondok</td>
                                    <td class="p-3 md:p-4 text-emerald-600 font-bold">Belum Penuh</td>
                                    <td class="p-3 md:p-4 text-gray-400 text-[9px] md:text-[10px] max-w-[180px]">Jl. Hilligoo No.67, Kampung Pondok, Padang Barat, Padang City</td>
                                    <td class="p-3 md:p-4 flex justify-center space-x-2">
                                        <button class="bg-[#062C1B] text-white px-3 py-1 rounded-full text-[10px]">Lihat</button>
                                        <button class="bg-[#38EF7D] text-white px-3 py-1 rounded-full text-[10px]">Jemput</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-[#062C1B] to-[#0A4D2F] p-5 rounded-[15px] text-white">
                    <h3 class="text-lg md:text-xl font-bold mb-4">Top Contributors</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="font-bold">#1</span>
                                <div class="w-10 h-10 bg-white/20 rounded-full border border-white/30 flex items-center justify-center">
                                    <x-heroicon-s-user class="w-6 h-6 text-white" />
                                </div>
                                <span class="text-sm font-semibold">Fadhillah Rahmad Kurnia</span>
                            </div>
                            <span class="text-xs font-bold text-[#38EF7D]">5010 Points</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="font-bold">#2</span>
                                <div class="w-10 h-10 bg-white/20 rounded-full border border-white/30 flex items-center justify-center">
                                    <x-heroicon-s-user class="w-6 h-6 text-white" />
                                </div>
                                <span class="text-sm font-semibold">Hanaviz</span>
                            </div>
                            <span class="text-xs font-bold text-[#38EF7D]">4870 Points</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <span class="font-bold">#3</span>
                                <div class="w-10 h-10 bg-white/20 rounded-full border border-white/30 flex items-center justify-center">
                                    <x-heroicon-s-user class="w-6 h-6 text-white" />
                                </div>
                                <span class="text-sm font-semibold">Widia Khairunisa</span>
                            </div>
                            <span class="text-xs font-bold text-[#38EF7D]">4515 Points</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
