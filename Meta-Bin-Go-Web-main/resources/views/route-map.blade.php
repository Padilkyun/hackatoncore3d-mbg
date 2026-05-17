<x-app-layout>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <style>
        .leaflet-routing-container { display: none !important; }
    </style>

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

                <div class="bg-white px-6 py-2 rounded-2xl shadow-lg flex items-center space-x-3 border border-gray-100 pointer-events-auto">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                    </div>
                    <span class="font-bold text-[#062C1B] text-sm">Admin</span>
                </div>
            </div>

            <div class="absolute inset-0 pointer-events-none z-10">
                <div class="absolute top-28 right-[480px] bg-white px-4 py-2 rounded-full shadow-md border border-gray-100 flex items-center space-x-2 pointer-events-auto">
                    <div class="w-3 h-3 bg-black rounded-full"></div>
                    <span class="text-xs font-semibold text-gray-700">Starting Point</span>
                </div>

                <div class="absolute bottom-12 left-12 bg-white px-4 py-2 rounded-full shadow-md border border-gray-100 flex items-center space-x-2 pointer-events-auto">
                    <div class="w-3 h-3 bg-black rounded-full"></div>
                    <span class="text-xs font-semibold text-gray-700">Destination</span>
                </div>
            </div>

            <div class="flex justify-end items-end w-full h-full">
                
                <div class="bg-white/95 backdrop-blur-md p-6 rounded-[35px] shadow-2xl border border-gray-100 w-[420px] max-h-[85vh] flex flex-col pointer-events-auto">
                    <h3 class="text-xl font-bold text-black mb-4">Riwayat Status Bin</h3>
                    
                    <div class="flex-1 overflow-y-auto pr-2 space-y-3" style="max-height: calc(85vh - 140px);">
                        @php
                            $routeList = $route ?? [];
                            // Map IDs to names if available
                            $binNames = collect($bins)->pluck('name', 'id')->toArray();
                            $displayRoutes = array_map(function($id) use ($binNames) {
                                return $binNames[$id] ?? $id;
                            }, $routeList);
                        @endphp

                        @foreach ($displayRoutes as $index => $item)
                            <div class="relative flex items-center space-x-4 bg-gray-50/70 p-3 rounded-2xl border border-gray-100/50">
                                <div class="relative flex flex-col items-center">
                                    <div class="w-6 h-6 rounded-full bg-black flex items-center justify-center text-[10px] font-bold text-white z-10">
                                        @if($index == 0)
                                            <div class="w-2 h-2 bg-white rounded-full"></div>
                                        @elseif($index == count($routes) - 1)
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                        @else
                                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                        @endif
                                    </div>
                                    @if (!$loop->last)
                                        <div class="absolute top-6 w-0.5 h-10 border-l-2 border-dashed border-gray-400 z-0"></div>
                                    @endif
                                </div>
                                
                                <div class="flex-1 bg-white px-4 py-2.5 rounded-xl border border-gray-100 shadow-sm">
                                    <span class="text-xs font-semibold text-gray-800">{{ $item }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 my-4"></div>

                    <div class="text-center py-2 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-2xl font-bold text-black">1 jam 15 min <span class="text-lg font-normal text-gray-500">(10,2 km)</span></p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Set titik tengah pandangan kamera peta awal (sekitar kota Padang)
            var map = L.map('map', {
                zoomControl: false 
            }).setView([-0.9250, 100.3950], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Koordinat simulasi rute jalan dari utara ke barat Kota Padang
            // Sesuai daftar: Batipuh Panjang, Pasar Ambacang, Lubuk Lintah, Ampang, Andalas, Simpang Haru, Berok Nipah
            // Koordinat dinamis dari Python Backend
            var routeWaypoints = [
                @foreach($bins as $bin)
                    @if(in_array($bin['id'], $route ?? []))
                        L.latLng({{ $bin['lat'] }}, {{ $bin['long'] }}),
                    @endif
                @endforeach
            ];
            
            // If empty, use a fallback
            if(routeWaypoints.length == 0) {
                routeWaypoints = [L.latLng(-0.9525, 100.3625)];
            }

            // Custom Marker Merah untuk setiap titik stop bin sampah
            var redBinMarker = L.divIcon({
                className: 'custom-bin-icon',
                html: "<div class='w-4 h-4 bg-red-600 rounded-full border-2 border-white shadow-lg flex items-center justify-center'><div class='w-1 h-1 bg-white rounded-full'></div></div>",
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });

            // Inisialisasi Jalur Garis Routing (Garis Hitam Tebal sesuai gambar mockup)
            var control = L.Routing.control({
                waypoints: routeWaypoints,
                lineOptions: {
                    styles: [{ color: '#1B1B18', opacity: 0.9, weight: 4.5 }]
                },
                createMarker: function(i, waypoint, n) {
                    // Generate titik pin merah pada setiap koordinat rute
                    return L.marker(waypoint.latLng, { icon: redBinMarker });
                },
                routeWhileDragging: false,
                addWaypoints: false
            }).addTo(map);
        });
    </script>
</x-app-layout>