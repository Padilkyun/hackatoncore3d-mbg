<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="relative w-full h-screen font-['Poppins'] overflow-hidden bg-[#F8F9F5]">
        
        <div id="map" class="absolute inset-0 w-full h-full z-0"></div>

        <div class="absolute inset-0 z-10 pointer-events-none flex flex-col justify-between p-8">
            
            <div class="flex justify-between items-start w-full">
                <div class="bg-white px-6 py-3 rounded-full shadow-lg flex items-center space-x-3 w-96 border border-gray-100 pointer-events-auto">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Telusuri Lokasi" class="w-full bg-transparent border-none outline-none focus:outline-none focus:ring-0 text-sm text-gray-700" />
                </div>

                <div class="bg-white px-6 py-2.5 rounded-2xl shadow-lg flex items-center space-x-3 border border-gray-100 pointer-events-auto">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                    <span class="font-bold text-[#062C1B] text-sm">Admin</span>
                </div>
            </div>

            <div class="flex justify-end items-end w-full h-full mt-4">
                
                <div class="w-[450px] max-h-[85vh] flex flex-col space-y-4 pointer-events-auto">
                    
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-white p-4 rounded-[24px] shadow-xl border border-gray-100/50 flex flex-col justify-between">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-[#1E6B36]">Normal</span>
                                <svg class="w-4 h-4 text-[#1E6B36]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h11m4 0h3m-9 6h6m-12-12h8"></path></svg>
                            </div>
                            <div class="mt-2">
                                <span class="text-3xl font-extrabold text-black">21</span>
                                <p class="text-[10px] text-gray-400 font-medium">Locations</p>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-[24px] shadow-xl border border-gray-100/50 flex flex-col justify-between">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-[#B56304]">Kurang Baik</span>
                                <svg class="w-4 h-4 text-[#B56304]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h11m4 0h3m-9 6h6m-12-12h8"></path></svg>
                            </div>
                            <div class="mt-2">
                                <span class="text-3xl font-extrabold text-black">6</span>
                                <p class="text-[10px] text-gray-400 font-medium">Locations</p>
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-[24px] shadow-xl border border-gray-100/50 flex flex-col justify-between">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-[#C0261C]">Buruk</span>
                                <svg class="w-4 h-4 text-[#C0261C]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12h11m4 0h3m-9 6h6m-12-12h8"></path></svg>
                            </div>
                            <div class="mt-2">
                                <span class="text-3xl font-extrabold text-black">2</span>
                                <p class="text-[10px] text-gray-400 font-medium">Locations</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-[32px] shadow-2xl border border-gray-100 flex flex-col overflow-hidden">
                        <h3 class="text-xl font-bold text-black mb-4">Kualitas Udara</h3>
                        
                        <div class="overflow-y-auto pr-1 flex-1 max-h-[48vh] space-y-3">
                            
                            @php
                                // Simulasi data sesuai layout tabel mockup kamu
                                $airData = [
                                    ['name' => 'Bin Simpang Haru', 'status' => 'Kurang Baik', 'color' => 'text-[#B56304]', 'address' => 'Jl. Hiligoo No.67, Kampung Pondok, Padang Barat, Padang City, West Sumatra'],
                                    ['name' => 'Bin Pondok', 'status' => 'Normal', 'color' => 'text-[#1E6B36]', 'address' => 'Jl. Hiligoo No.67, Kampung Pondok, Padang Barat, Padang City, West Sumatra'],
                                    ['name' => 'Bin Simpang Haru', 'status' => 'Buruk', 'color' => 'text-[#C0261C]', 'address' => 'Jl. Hiligoo No.67, Kampung Pondok, Padang Barat, Padang City, West Sumatra'],
                                    ['name' => 'Bin Pondok', 'status' => 'Normal', 'color' => 'text-[#1E6B36]', 'address' => 'Jl. Hiligoo No.67, Kampung Pondok, Padang Barat, Padang City, West Sumatra'],
                                    ['name' => 'Bin Simpang Haru', 'status' => 'Kurang Baik', 'color' => 'text-[#B56304]', 'address' => 'Jl. Hiligoo No.67, Kampung Pondok, Padang Barat, Padang City, West Sumatra'],
                                    ['name' => 'Bin Pondok', 'status' => 'Normal', 'color' => 'text-[#1E6B36]', 'address' => 'Jl. Hiligoo No.67, Kampung Pondok, Padang Barat, Padang City, West Sumatra'],
                                    ['name' => 'Bin Simpang Haru', 'status' => 'Buruk', 'color' => 'text-[#C0261C]', 'address' => 'Jl. Hiligoo No.67, Kampung Pondok, Padang Barat, Padang City, West Sumatra'],
                                ];
                            @endphp

                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-[#062C1B] text-white text-[11px] font-bold tracking-wider rounded-lg">
                                        <th class="p-3 rounded-l-xl">Nama Bin</th>
                                        <th class="p-3">Status</th>
                                        <th class="p-3">Lokasi</th>
                                        <th class="p-3 rounded-r-xl text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-[11px]">
                                    @foreach ($airData as $row)
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="p-3 font-semibold text-gray-800 whitespace-nowrap">{{ $row['name'] }}</td>
                                            <td class="p-3 font-bold {{ $row['color'] }}">{{ $row['status'] }}</td>
                                            <td class="p-3 text-gray-400 font-medium max-w-[140px] truncate" title="{{ $row['address'] }}">{{ $row['address'] }}</td>
                                            <td class="p-3 text-center">
                                                <button class="bg-[#5CB85C] hover:bg-[#4cae4c] text-white font-bold px-3 py-1 rounded-full text-[10px] transition shadow-sm">
                                                    Lihat
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi peta berpusat pada area koordinat Kota Padang
            var map = L.map('map', {
                zoomControl: false 
            }).setView([-0.9260, 100.3900], 15);

            // Menggunakan Tile OpenStreetMap standar bersih
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Custom Marker Pin Hijau untuk Titik Sensor Normal
            var greenIcon = L.divIcon({
                className: 'custom-icon',
                html: "<div class='w-5 h-5 bg-[#22C55E] rounded-full border-2 border-white shadow-xl flex items-center justify-center'><div class='w-1.5 h-1.5 bg-white rounded-full'></div></div>",
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            // Custom Marker Pin Merah untuk Titik Sensor Buruk / Kurang Baik
            var redIcon = L.divIcon({
                className: 'custom-icon',
                html: "<div class='w-5 h-5 bg-[#EF4444] rounded-full border-2 border-white shadow-xl flex items-center justify-center'><div class='w-1.5 h-1.5 bg-white rounded-full'></div></div>",
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            // --- TAMBAHAN RADIAL AIR QUALITY CIRCLE (Sesuai mockup gelembung warna di peta) ---
            
            // 1. Zona Hijau (Normal) - Sebelah Kiri Bawah
            L.marker([-0.9290, 100.3800], {icon: greenIcon}).addTo(map);
            L.circle([-0.9290, 100.3800], {
                color: '#22C55E',
                fillColor: '#22C55E',
                fillOpacity: 0.35,
                radius: 130,
                stroke: false
            }).addTo(map);

            // 2. Zona Hijau (Normal) - Tengah Atas
            L.marker([-0.9270, 100.3920], {icon: greenIcon}).addTo(map);
            L.circle([-0.9270, 100.3920], {
                color: '#22C55E',
                fillColor: '#22C55E',
                fillOpacity: 0.35,
                radius: 140,
                stroke: false
            }).addTo(map);

            // 3. Zona Oranye/Merah (Polusi Kurang Baik/Buruk) - Atas Tengah
            L.marker([-0.9220, 100.3880], {icon: redIcon}).addTo(map);
            L.circle([-0.9220, 100.3880], {
                color: '#EA580C',
                fillColor: '#EA580C',
                fillOpacity: 0.35,
                radius: 125,
                stroke: false
            }).addTo(map);

            // 4. Zona Oranye/Merah (Polusi Buruk) - Kanan Bawah
            L.marker([-0.9320, 100.3980], {icon: redIcon}).addTo(map);
            L.circle([-0.9320, 100.3980], {
                color: '#EA580C',
                fillColor: '#EA580C',
                fillOpacity: 0.35,
                radius: 120,
                stroke: false
            }).addTo(map);
        });
    </script>
</x-app-layout>