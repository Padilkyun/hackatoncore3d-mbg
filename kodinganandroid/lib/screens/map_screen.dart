import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:meta_bin_go/services/api_service.dart';
import 'package:meta_bin_go/theme.dart';

class MapScreen extends StatefulWidget {
  const MapScreen({super.key});

  @override
  State<MapScreen> createState() => _MapScreenState();
}

class _MapScreenState extends State<MapScreen> {
  final ApiService _apiService = ApiService();
  List<dynamic> _bins = [];
  bool _isLoading = true;
  dynamic _selectedBin;

  @override
  void initState() {
    super.initState();
    _loadBins();
  }

  Future<void> _loadBins() async {
    try {
      final bins = await _apiService.fetchBins();
      setState(() {
        _bins = bins;
        _isLoading = false;
        if (_bins.isNotEmpty) _selectedBin = _bins.first;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      print("Error loading bins: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Stack(
        children: [
          // Interactive Map
          _isLoading 
            ? const Center(child: CircularProgressIndicator())
            : FlutterMap(
                options: MapOptions(
                  initialCenter: _bins.isNotEmpty 
                      ? LatLng(_bins.first['lat'], _bins.first['lng'])
                      : const LatLng(-0.9471, 100.3695),
                  initialZoom: 15.0,
                ),
                children: [
                  TileLayer(
                    urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                    userAgentPackageName: 'com.example.meta_bin_go',
                  ),
                  MarkerLayer(
                    markers: _bins.map((bin) {
                      bool isSelected = _selectedBin?['id'] == bin['id'];
                      return Marker(
                        point: LatLng(bin['lat'], bin['lng']),
                        width: 60,
                        height: 60,
                        child: GestureDetector(
                          onTap: () => setState(() => _selectedBin = bin),
                          child: _buildMapPin(
                            isSelected: isSelected, 
                            organic: bin['organic'],
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                ],
              ),

          // Search Bar
          Positioned(
            top: 60,
            left: 20,
            right: 20,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 20),
              height: 56,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(30),
                boxShadow: [
                  BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 10, offset: const Offset(0, 5)),
                ],
              ),
              child: Row(
                children: [
                  const Icon(Icons.search, color: Colors.black54),
                  const SizedBox(width: 12),
                  Expanded(
                    child: TextField(
                      decoration: InputDecoration(
                        hintText: 'Telusuri Lokasi Bin',
                        hintStyle: TextStyle(color: Colors.grey.shade400),
                        border: InputBorder.none,
                      ),
                    ),
                  ),
                  IconButton(
                    icon: const Icon(Icons.refresh, color: AppTheme.primaryGreen),
                    onPressed: _loadBins,
                  )
                ],
              ),
            ),
          ),

          // Bottom Info Card (Selected Bin)
          if (_selectedBin != null)
            Positioned(
              left: 20,
              right: 20,
              bottom: 120,
              child: _buildBinDetailCard(_selectedBin),
            ),
        ],
      ),
    );
  }

  Widget _buildMapPin({required bool isSelected, required dynamic organic}) {
    Color color = AppTheme.primaryGreen;
    if (organic >= 80) color = Colors.red;
    else if (organic >= 50) color = Colors.orange;

    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(4),
          decoration: BoxDecoration(
            color: isSelected ? color : Colors.white,
            shape: BoxShape.circle,
            boxShadow: [
              BoxShadow(color: Colors.black.withOpacity(0.2), blurRadius: 5),
            ],
          ),
          child: Icon(
            Icons.delete,
            color: isSelected ? Colors.white : color,
            size: 24,
          ),
        ),
        if (isSelected)
          Container(
            margin: const EdgeInsets.only(top: 2),
            width: 4,
            height: 4,
            decoration: const BoxDecoration(color: Colors.black26, shape: BoxShape.circle),
          )
      ],
    );
  }

  Widget _buildBinDetailCard(dynamic bin) {
    double organic = double.tryParse(bin['organic'].toString()) ?? 0.0;
    int mq135 = int.tryParse(bin['mq135'].toString()) ?? 0;
    
    String airStatus = "Air: Normal";
    Color airColor = AppTheme.primaryGreen;
    if (mq135 >= 700) {
      airStatus = "Air: Buruk";
      airColor = Colors.red;
    } else if (mq135 >= 400) {
      airStatus = "Air: Warning";
      airColor = Colors.orange;
    }
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.1), blurRadius: 15, offset: const Offset(0, 5)),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              color: AppTheme.primaryGreen.withOpacity(0.1),
              borderRadius: BorderRadius.circular(16),
            ),
            child: const Icon(Icons.delete_outline, color: AppTheme.primaryGreen, size: 40),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(bin['name'], style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                const SizedBox(height: 8),
                Row(
                  children: [
                    _buildTag(
                      organic >= 80 ? 'Penuh' : 'Tersedia', 
                      organic >= 80 ? Colors.red : AppTheme.primaryGreen
                    ),
                    const SizedBox(width: 8),
                    _buildTag(airStatus, airColor),
                  ],
                ),
                const SizedBox(height: 8),
                Text(
                  "Lat: ${bin['lat']}, Lng: ${bin['lng']}",
                  style: const TextStyle(color: Colors.grey, fontSize: 10),
                ),
              ],
            ),
          ),
          Column(
            children: [
              Text(
                "${organic.toStringAsFixed(0)}%",
                style: TextStyle(
                  fontSize: 24, 
                  fontWeight: FontWeight.bold,
                  color: organic >= 80 ? Colors.red : AppTheme.primaryGreen
                ),
              ),
              const Text("Kapasitas", style: TextStyle(fontSize: 10, color: Colors.grey)),
            ],
          )
        ],
      ),
    );
  }

  Widget _buildTag(String text, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(color: color.withOpacity(0.1), borderRadius: BorderRadius.circular(8)),
      child: Text(
        text, 
        style: TextStyle(color: color, fontSize: 10, fontWeight: FontWeight.bold),
      ),
    );
  }
}
