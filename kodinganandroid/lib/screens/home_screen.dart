import 'package:flutter/material.dart';
import 'package:meta_bin_go/theme.dart';
import 'package:meta_bin_go/screens/history_screen.dart';
import 'package:meta_bin_go/screens/leaderboard_screen.dart';
import 'package:meta_bin_go/services/api_service.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final ApiService _apiService = ApiService();
  bool _isLoading = false;
  Map<String, dynamic> _stats = {
    'total_users': 0,
    'total_waste': 0,
    'active_bins': 0,
    'full_bins': 0,
    'reward_today': 0
  };

  @override
  void initState() {
    super.initState();
    _fetchStats();
  }

  Future<void> _fetchStats() async {
    try {
      final stats = await _apiService.getDashboardStats();
      setState(() => _stats = stats);
    } catch (e) {
      debugPrint("Error fetching stats: $e");
    }
  }

  Future<void> _handleRefresh() async {
    setState(() => _isLoading = true);
    await _apiService.getUserInfo();
    await _fetchStats();
    if (mounted) setState(() => _isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    final user = ApiService.currentUser;
    final String name = user?['username'] ?? 'Guest';
    final int points = user?['points'] ?? 0;

    return Scaffold(
      backgroundColor: const Color(0xFFF5F6F8),
      body: RefreshIndicator(
        onRefresh: _handleRefresh,
        color: AppTheme.primaryGreen,
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: Column(
            children: [
              // Top Profile Section
              Padding(
                padding: const EdgeInsets.only(top: 60, left: 24, right: 24, bottom: 20),
                child: Row(
                  children: [
                    CircleAvatar(
                      radius: 24,
                      backgroundColor: Colors.grey,
                      backgroundImage: user?['profile_picture'] != null 
                        ? NetworkImage(user!['profile_picture']) 
                        : null,
                      child: user?['profile_picture'] == null 
                        ? const Icon(Icons.person, color: Colors.white, size: 30)
                        : null,
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Good Morning!',
                            style: TextStyle(color: AppTheme.textSecondary, fontSize: 12),
                          ),
                          Text(
                            name,
                            style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 16,
                                ),
                          ),
                        ],
                      ),
                    ),
                    IconButton(
                      icon: const Icon(Icons.notifications_none, size: 28),
                      onPressed: () {},
                    )
                  ],
                ),
              ),
              
              // Total Points Card
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 24),
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: AppTheme.backgroundDark,
                  borderRadius: BorderRadius.circular(24),
                  image: const DecorationImage(
                    image: AssetImage('assets/images/bg_pointcard_home.png'),
                    fit: BoxFit.cover,
                  ),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Total Points',
                          style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600),
                        ),
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.baseline,
                          textBaseline: TextBaseline.alphabetic,
                          children: [
                            Text(
                              points.toString(),
                              style: Theme.of(context).textTheme.displayLarge?.copyWith(
                                    fontSize: 48,
                                    height: 1.0,
                                  ),
                            ),
                            const SizedBox(width: 4),
                            const Text(
                              'Point',
                              style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600),
                            ),
                          ],
                        ),
                      ],
                    ),
                    const SizedBox(height: 20),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Status',
                          style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w600),
                        ),
                        const SizedBox(width: 8),
                        Text(
                          user != null ? 'Account Verified' : 'Guest Mode',
                          style: const TextStyle(color: Colors.white70, fontSize: 10),
                          textAlign: TextAlign.right,
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    LinearProgressIndicator(
                      value: (points % 1000) / 1000,
                      backgroundColor: Colors.white24,
                      color: const Color(0xFFC7F000), // Yellowish green
                      minHeight: 8,
                      borderRadius: BorderRadius.circular(4),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
              
              // 4 Grid Cards
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                child: Row(
                  children: [
                    Expanded(child: _buildStatCard('Total\nUser', _stats['total_users'].toString(), 'Users', Icons.people_outline)),
                    const SizedBox(width: 16),
                    Expanded(child: _buildStatCard('Total\nSampah', _stats['total_waste'].toString(), 'Items', Icons.delete_outline)),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                child: Row(
                  children: [
                    Expanded(child: _buildStatCard('Active\nBins', _stats['active_bins'].toString(), 'Units', Icons.eco_outlined)),
                    const SizedBox(width: 16),
                    Expanded(child: _buildStatCard('Voucher\nHari Ini', _stats['reward_today'].toString(), 'Claimed', Icons.receipt_long)),
                  ],
                ),
              ),
              const SizedBox(height: 32),
              
              // Top Contributors Section
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 24),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 5)),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Padding(
                      padding: const EdgeInsets.all(16.0),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text('Top Contributors', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                          TextButton(
                            onPressed: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(builder: (context) => const LeaderboardScreen()),
                              );
                            },
                            child: const Text('View All', style: TextStyle(color: AppTheme.primaryGreen, fontSize: 12)),
                          ),
                        ],
                      ),
                    ),
                    const Divider(height: 1, color: Color(0xFFEEEEEE)),
                    if (_stats['top_users'] != null)
                      ...(_stats['top_users'] as List).map((u) => _buildContributorItem(
                        u['username'], 
                        u['points'].toString(), 
                        u['profile_picture']
                      )),
                    if (_stats['top_users'] == null || (_stats['top_users'] as List).isEmpty)
                      const Padding(
                        padding: EdgeInsets.all(16.0),
                        child: Text('No contributors yet', style: TextStyle(color: Colors.grey)),
                      ),
                    const SizedBox(height: 12),
                  ],
                ),
              ),
              
              // Padding for Bottom Nav
              const SizedBox(height: 100),
            ],
          ),
        ),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerFloat,
      floatingActionButton: Padding(
        padding: const EdgeInsets.only(bottom: 80.0), // Angkat tombol di atas navbar
        child: FloatingActionButton.extended(
          onPressed: () => _showCameraPreviewDialog(context),
          backgroundColor: AppTheme.primaryGreen,
          icon: const Icon(Icons.camera_alt, color: Colors.white),
          label: const Text('Buang Sampah', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        ),
      ),
    );
  }

  void _showCameraPreviewDialog(BuildContext context) {
    // Ganti dengan IP ESP32-CAM Anda (harus satu jaringan WiFi dengan HP)
    String espCamIp = "http://10.162.1.205"; 
    
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (BuildContext context) {
        return StreamBuilder(
          stream: Stream.periodic(const Duration(milliseconds: 1500)), // Diperbesar agar ESP32 sempat mengirim gambar penuh
          builder: (context, snapshot) {
            String imageUrl = "$espCamIp/cam-hi.jpg?t=${DateTime.now().millisecondsSinceEpoch}";
            
            return AlertDialog(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
              title: const Text('Preview Kamera', textAlign: TextAlign.center, style: TextStyle(fontWeight: FontWeight.bold)),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(12),
                    child: Container(
                      width: 300,
                      height: 240,
                      color: Colors.black12,
                      child: Image.network(
                        imageUrl,
                        fit: BoxFit.cover,
                        gaplessPlayback: true, // Mencegah kedip saat gambar di-refresh
                        errorBuilder: (context, error, stackTrace) {
                          return const Center(
                            child: Padding(
                              padding: EdgeInsets.all(16.0),
                              child: Text(
                                'Kamera tidak terhubung.\nPastikan HP dan ESP32 di WiFi yang sama.', 
                                textAlign: TextAlign.center,
                                style: TextStyle(color: Colors.red, fontSize: 12),
                              ),
                            ),
                          );
                        },
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text('Siapkan sampah Anda di depan kamera, lalu klik Mulai Scan.', textAlign: TextAlign.center, style: TextStyle(fontSize: 12, color: Colors.grey)),
                ],
              ),
              actionsAlignment: MainAxisAlignment.spaceBetween,
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text('Batal', style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold)),
                ),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppTheme.primaryGreen,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(30)),
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                  ),
                  onPressed: () async {
                    Navigator.pop(context);
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Mengirim perintah ke ESP32...')),
                    );
                    
                    String? currentUsername = ApiService.currentUser?['username'];
                    bool success = await _apiService.triggerBin(6, username: currentUsername); // Default bin ID
                    
                    if (success) {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Proses Scan Sampah Dimulai!')),
                      );
                      
                      // Polling for result (Max 15 seconds)
                      int retries = 15;
                      while (retries > 0) {
                        await Future.delayed(const Duration(seconds: 1));
                        String? result = await _apiService.checkResult(6);
                        if (result != null) {
                          if (context.mounted) {
                            showDialog(
                              context: context,
                              builder: (context) => AlertDialog(
                                title: const Text('Yeay! Berhasil! 🎉', textAlign: TextAlign.center, style: TextStyle(color: AppTheme.primaryGreen)),
                                content: Text('Sampah Anda terdeteksi sebagai $result.\n\nAnda mendapatkan +10 Poin!', textAlign: TextAlign.center),
                                actions: [
                                  TextButton(
                                    onPressed: () {
                                      Navigator.pop(context);
                                      _apiService.getUserInfo().then((_) => setState((){})); // Refresh points
                                    },
                                    child: const Text('Tutup', style: TextStyle(color: AppTheme.primaryGreen)),
                                  )
                                ],
                              )
                            );
                          }
                          break;
                        }
                        retries--;
                      }
                      
                      if (retries == 0 && context.mounted) {
                         ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(content: Text('Waktu tunggu habis. Pastikan alat terhubung.')),
                        );
                      }
                      
                    } else {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Gagal terhubung ke ESP32.')),
                      );
                    }
                  },
                  child: const Text('Mulai Scan', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                ),
              ],
            );
          },
        );
      },
    );
  }

  Widget _buildStatCard(String title, String value, String unit, IconData icon) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 5)),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(title, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
              Icon(icon, size: 20),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            crossAxisAlignment: CrossAxisAlignment.baseline,
            textBaseline: TextBaseline.alphabetic,
            children: [
              Text(
                value,
                style: const TextStyle(fontSize: 32, fontWeight: FontWeight.bold, height: 1.0),
              ),
              const SizedBox(width: 4),
              Text(unit, style: const TextStyle(fontSize: 10, color: Colors.grey)),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildContributorItem(String username, String points, String? profileUrl) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      child: Row(
        children: [
          CircleAvatar(
            radius: 18,
            backgroundColor: AppTheme.primaryGreen.withOpacity(0.1),
            backgroundImage: profileUrl != null ? NetworkImage(profileUrl) : null,
            child: profileUrl == null 
              ? const Icon(Icons.person, size: 20, color: AppTheme.primaryGreen) 
              : null,
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              username,
              style: const TextStyle(fontWeight: FontWeight.w500, fontSize: 14),
            ),
          ),
          Text(
            '$points Pts',
            style: const TextStyle(color: AppTheme.primaryGreen, fontWeight: FontWeight.bold),
          ),
        ],
      ),
    );
  }
}
