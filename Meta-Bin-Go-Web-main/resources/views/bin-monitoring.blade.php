<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <div class="relative w-full h-screen font-['Poppins'] overflow-hidden bg-[#F8F9F5]">

        <div id="map" class="absolute inset-0 w-full h-full z-0"></div>

        <div class="absolute inset-0 z-10 pointer-events-none flex flex-col justify-between p-8">

            <div class="flex justify-between items-center w-full">
                <div class="bg-white px-6 py-3 rounded-full shadow-lg flex items-center space-x-3 w-96 border border-gray-100 pointer-events-auto">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="Telusuri Lokasi" class="w-full bg-transparent border-none outline-none focus:outline-none focus:ring-0 text-sm text-gray-700" />
                </div>

                <div class="bg-white px-6 py-2 rounded-2xl shadow-lg flex items-center space-x-3 border border-gray-100 pointer-events-auto">
                    <div class="w-8 h-8 bg-[#062C1B] rounded-full flex items-center justify-center text-white text-xs font-bold">
                        A
                    </div>
                    <span class="font-bold text-[#062C1B] text-sm">Admin</span>
                </div>
            </div>

            <div class="flex justify-end items-end w-full h-full mt-4">

                <div class="bg-white/95 backdrop-blur-md p-6 rounded-[35px] shadow-2xl border border-gray-100 w-[450px] max-h-[80vh] overflow-y-auto pointer-events-auto flex flex-col">
                    <h3 class="text-xl font-bold text-[#062C1B] mb-4">Riwayat Status Bin</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-[#062C1B] text-white text-[11px] uppercase tracking-wider">
                                    <th class="p-3 rounded-l-xl">Nama Bin</th>
                                    <th class="p-3 text-center">Kapasitas</th>
                                    <th class="p-3">Lokasi</th>
                                    <th class="p-3 text-center rounded-r-xl">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs">
                                @foreach($bins as $bin)
                                <tr class="border-b border-gray-100">
                                    <td class="p-3 font-bold text-gray-800">{{ $bin['name'] }}</td>
                                    <td class="p-3 text-center font-extrabold {{ $bin['organic'] >= 80 ? 'text-red-600' : ($bin['organic'] >= 50 ? 'text-amber-500' : 'text-emerald-600') }} text-sm">
                                        {{ $bin['organic'] }}%
                                    </td>
                                    <td class="p-3 text-gray-400 text-[10px] max-w-[120px] leading-tight">Lat: {{ $bin['lat'] }}, Long: {{ $bin['long'] }}</td>
                                    <td class="p-3">
                                        <div class="flex space-x-1 justify-center">
                                            <button class="bg-red-600 text-white px-3 py-1 rounded-full text-[9px] font-semibold hover:bg-red-700 transition">Lihat</button>
                                            <button class="bg-[#062C1B] {{ $bin['organic'] < 80 ? 'opacity-40 cursor-not-allowed' : '' }} text-white px-3 py-1 rounded-full text-[9px] font-semibold hover:bg-black transition" {{ $bin['organic'] < 80 ? 'disabled' : '' }}>Jemput</button>
                                        </div>
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

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Set koordinat peta awal ke daerah Padang Barat
        var map = L.map('map', {
            zoomControl: false 
        }).setView([-0.9535, 100.3615], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // =========================================================================
        // FUNGSI DINAMIS UNTUK MEMBUAT PIN MARKER DENGAN INDIKATOR WARNA PERSENTASE
        // =========================================================================
        function createCustomPin(percentage) {
            let colorClass = 'text-emerald-500'; // Default < 50% (Hijau)
            let animationClass = '';

            if (percentage >= 80) {
                colorClass = 'text-red-600';     // >= 80% (Merah Penuh)
                animationClass = 'animate-bounce'; // Efek membal khusus pin penuh
            } else if (percentage >= 50) {
                colorClass = 'text-amber-500';   // 50% - 79% (Kuning / Amber)
            }

            return L.divIcon({
                className: 'custom-pin-icon',
                html: `
                    <div class="flex flex-col items-center drop-shadow-[0_10px_8px_rgba(0,0,0,0.3)] ${animationClass}" style="animation-duration: 2s;">
                        <svg class="w-8 h-8 ${colorClass}" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        <div class="w-2 h-1 bg-black/20 rounded-full blur-[1px] -mt-1"></div>
                    </div>
                `,
                iconSize: [32, 40],
                iconAnchor: [16, 36]
            });
        }

        // Tambahkan pin objek marker secara dinamis dari PHP
        @foreach($bins as $bin)
        L.marker([{{ $bin['lat'] }}, {{ $bin['long'] }}], {icon: createCustomPin({{ $bin['organic'] }})}).addTo(map)
            .bindPopup('<b>{{ $bin['name'] }}</b><br>Kapasitas: <span style="color:{{ $bin['organic'] >= 80 ? 'red' : ($bin['organic'] >= 50 ? 'orange' : 'green') }}; font-weight:bold;">{{ $bin['organic'] }}%</span>');
        @endforeach
    });
    </script>
</x-app-layout>