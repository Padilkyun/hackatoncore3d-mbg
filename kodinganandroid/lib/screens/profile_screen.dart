import 'package:flutter/material.dart';
import 'package:meta_bin_go/theme.dart';
import 'package:meta_bin_go/services/api_service.dart';
import 'package:meta_bin_go/screens/login_screen.dart';
import 'package:meta_bin_go/screens/edit_profile_screen.dart';
import 'package:meta_bin_go/screens/purchase_history_screen.dart';
import 'package:meta_bin_go/screens/about_screen.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final user = ApiService.currentUser;
    final String name = user?['username'] ?? 'Guest';
    final int points = user?['points'] ?? 0;

    return Scaffold(
      backgroundColor: const Color(0xFFF5F6F8),
      body: Stack(
        children: [
          // Dark Background Top
          Container(
            height: 350,
            decoration: const BoxDecoration(
              image: DecorationImage(
                image: AssetImage('assets/images/bg.png'),
                fit: BoxFit.cover,
                alignment: Alignment.topCenter,
              ),
            ),
          ),
          
          // Content
          Column(
            children: [
              const SizedBox(height: 100),
              
              // Profile Picture
              Center(
                child: Stack(
                  children: [
                    Container(
                      width: 120,
                      height: 120,
                      decoration: const BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                      ),
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(60),
                        child: user?['profile_picture'] != null
                            ? Image.network(
                                user!['profile_picture'],
                                fit: BoxFit.cover,
                                errorBuilder: (context, error, stackTrace) => const Icon(Icons.person, size: 80, color: Colors.grey),
                              )
                            : const Icon(Icons.person, size: 80, color: Colors.grey),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),
              
              // Name
              Text(
                name,
                style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 24),
              
              // Stats Card
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 24),
                padding: const EdgeInsets.symmetric(vertical: 20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 5)),
                  ],
                ),
                child: Row(
                  children: [
                    Expanded(
                      child: Column(
                        children: [
                          const Text('Total Points Lifetime', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                          const SizedBox(height: 8),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            crossAxisAlignment: CrossAxisAlignment.baseline,
                            textBaseline: TextBaseline.alphabetic,
                            children: [
                              Text(
                                points.toString(),
                                style: Theme.of(context).textTheme.displayLarge?.copyWith(
                                      color: AppTheme.primaryGreen,
                                      fontSize: 32,
                                      height: 1.0,
                                    ),
                              ),
                              const SizedBox(width: 4),
                              const Text('Point', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                            ],
                          ),
                        ],
                      ),
                    ),
                    Container(width: 1, height: 50, color: Colors.grey.shade300),
                    Expanded(
                      child: Column(
                        children: [
                          const Text('Leaderboard', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                          const SizedBox(height: 8),
                          Text(
                            '#1',
                            style: Theme.of(context).textTheme.displayLarge?.copyWith(
                                  color: AppTheme.primaryGreen,
                                  fontSize: 32,
                                  height: 1.0,
                                ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
              
              // Settings List
              Expanded(
                child: Container(
                  width: double.infinity,
                  color: const Color(0xFFF9F9F9),
                  child: ListView(
                    padding: const EdgeInsets.all(0),
                    children: [
                      _buildSettingsItem(Icons.person_outline, 'Edit Profile', onTap: () {
                        Navigator.push(context, MaterialPageRoute(builder: (_) => const EditProfileScreen()));
                      }),
                      _buildSettingsItem(Icons.settings_outlined, 'Settings'),
                      _buildSettingsItem(Icons.receipt_long_outlined, 'Purchase History', onTap: () {
                        Navigator.push(context, MaterialPageRoute(builder: (_) => const PurchaseHistoryScreen()));
                      }),
                      _buildSettingsItem(Icons.info_outline, 'About Meta Bin Go', onTap: () {
                        Navigator.push(context, MaterialPageRoute(builder: (_) => const AboutScreen()));
                      }),
                      _buildSettingsItem(Icons.logout, 'Logout', color: Colors.red, onTap: () {
                        ApiService.currentUser = null;
                        Navigator.of(context).pushAndRemoveUntil(
                          MaterialPageRoute(builder: (_) => const LoginScreen()),
                          (route) => false,
                        );
                      }),
                      const SizedBox(height: 100), // padding for bottom nav
                    ],
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSettingsItem(IconData icon, String title, {Color color = AppTheme.primaryGreen, VoidCallback? onTap}) {
    return Container(
      decoration: const BoxDecoration(
        border: Border(bottom: BorderSide(color: Color(0xFFEEEEEE))),
      ),
      child: ListTile(
        onTap: onTap,
        contentPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 4),
        leading: Icon(icon, color: color),
        title: Text(
          title,
          style: TextStyle(fontWeight: FontWeight.w600, color: color),
        ),
      ),
    );
  }
}
