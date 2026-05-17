import 'package:flutter/material.dart';
import 'package:meta_bin_go/theme.dart';
import 'package:meta_bin_go/services/api_service.dart';

class LeaderboardScreen extends StatefulWidget {
  const LeaderboardScreen({super.key});

  @override
  State<LeaderboardScreen> createState() => _LeaderboardScreenState();
}

class _LeaderboardScreenState extends State<LeaderboardScreen> {
  final ApiService _apiService = ApiService();
  bool _isLoading = true;
  List<dynamic> _topUsers = [];

  @override
  void initState() {
    super.initState();
    _fetchLeaderboard();
  }

  Future<void> _fetchLeaderboard() async {
    try {
      final data = await _apiService.getDashboardStats();
      setState(() {
        _topUsers = data['top_users'] ?? [];
        _isLoading = false;
      });
    } catch (e) {
      if (mounted) {
        setState(() => _isLoading = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to load leaderboard: $e')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.surfaceWhite,
      appBar: AppBar(
        title: const Text('Leaderboard', style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: AppTheme.primaryGreen,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: AppTheme.primaryGreen))
          : Column(
              children: [
                // Top 3 Podium (Conceptual)
                if (_topUsers.isNotEmpty) _buildPodium(),
                
                // List of users
                Expanded(
                  child: ListView.separated(
                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 20),
                    itemCount: _topUsers.length,
                    separatorBuilder: (context, index) => const Divider(height: 1),
                    itemBuilder: (context, index) {
                      final user = _topUsers[index];
                      return _buildLeaderboardItem(index + 1, user);
                    },
                  ),
                ),
              ],
            ),
    );
  }

  Widget _buildPodium() {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: const BoxDecoration(
        color: AppTheme.primaryGreen,
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(32),
          bottomRight: Radius.circular(32),
        ),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          if (_topUsers.length > 1) _buildPodiumItem(_topUsers[1], 2, 70),
          if (_topUsers.isNotEmpty) _buildPodiumItem(_topUsers[0], 1, 90),
          if (_topUsers.length > 2) _buildPodiumItem(_topUsers[2], 3, 70),
        ],
      ),
    );
  }

  Widget _buildPodiumItem(Map<String, dynamic> user, int rank, double size) {
    return Column(
      children: [
        Stack(
          alignment: Alignment.bottomCenter,
          children: [
            Container(
              width: size,
              height: size,
              margin: const EdgeInsets.only(bottom: 12),
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(color: Colors.white, width: rank == 1 ? 4 : 2),
                image: user['profile_picture'] != null
                    ? DecorationImage(image: NetworkImage(user['profile_picture']), fit: BoxFit.cover)
                    : null,
              ),
              child: user['profile_picture'] == null
                  ? Icon(Icons.person, color: Colors.white, size: size * 0.6)
                  : null,
            ),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
              decoration: BoxDecoration(
                color: rank == 1 ? const Color(0xFFFFD700) : Colors.white,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Text(
                '#$rank',
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 10),
              ),
            ),
          ],
        ),
        const SizedBox(height: 8),
        Text(
          user['username'],
          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 12),
        ),
        Text(
          '${user['points']} pts',
          style: const TextStyle(color: Colors.white70, fontSize: 10),
        ),
      ],
    );
  }

  Widget _buildLeaderboardItem(int rank, Map<String, dynamic> user) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 12),
      child: Row(
        children: [
          SizedBox(
            width: 30,
            child: Text(
              '$rank',
              style: TextStyle(
                fontWeight: FontWeight.bold,
                color: rank <= 3 ? AppTheme.primaryGreen : Colors.grey,
              ),
            ),
          ),
          CircleAvatar(
            radius: 20,
            backgroundImage: user['profile_picture'] != null ? NetworkImage(user['profile_picture']) : null,
            child: user['profile_picture'] == null ? const Icon(Icons.person) : null,
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Text(
              user['username'],
              style: const TextStyle(fontWeight: FontWeight.w600),
            ),
          ),
          Text(
            '${user['points']} pts',
            style: const TextStyle(color: AppTheme.primaryGreen, fontWeight: FontWeight.bold),
          ),
        ],
      ),
    );
  }
}
