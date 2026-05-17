import 'package:flutter/material.dart';
import 'package:meta_bin_go/theme.dart';
import 'package:meta_bin_go/services/api_service.dart';
import 'package:meta_bin_go/widgets/custom_feedback.dart';

class RewardScreen extends StatefulWidget {
  const RewardScreen({super.key});

  @override
  State<RewardScreen> createState() => _RewardScreenState();
}

class _RewardScreenState extends State<RewardScreen> {
  String _selectedFilter = 'Semua';
  final List<String> _filters = ['Semua', 'Transportasi', 'Belanja'];
  final ApiService _apiService = ApiService();
  List<dynamic> _rewards = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchRewards();
  }

  Future<void> _fetchRewards() async {
    try {
      final rewards = await _apiService.getRewards();
      setState(() {
        _rewards = rewards;
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      if (mounted) {
        CustomFeedback.showErrorSnackBar(context, 'Error loading rewards: $e');
      }
    }
  }

  Future<void> _handleRedeem(int rewardId) async {
    try {
      final result = await _apiService.claimReward(rewardId);
      if (mounted) {
        CustomFeedback.showSuccessSnackBar(context, result['message']);
        Navigator.pop(context); // Close modal
        setState(() {}); // Refresh points in UI
        _fetchRewards(); // Refresh quotas
      }
    } catch (e) {
      if (mounted) {
        CustomFeedback.showErrorSnackBar(context, e.toString());
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final int userPoints = ApiService.currentUser?['points'] ?? 0;

    return Scaffold(
      backgroundColor: const Color(0xFFF5F6F8),
      body: Stack(
        children: [
          // Background Header
          Container(
            height: 250,
            decoration: const BoxDecoration(
              image: DecorationImage(
                image: AssetImage('assets/images/bg.png'),
                fit: BoxFit.cover,
                alignment: Alignment.topCenter,
              ),
            ),
            padding: const EdgeInsets.only(top: 60, left: 24),
            child: const Align(
              alignment: Alignment.topLeft,
              child: Text(
                'Reward',
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
          
          // Content
          SafeArea(
            child: Column(
              children: [
                const SizedBox(height: 60),
                // Total Points Card
                Container(
                  margin: const EdgeInsets.symmetric(horizontal: 24),
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(24),
                    boxShadow: [
                      BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10, offset: const Offset(0, 5)),
                    ],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text(
                            'Total Points',
                            style: TextStyle(fontWeight: FontWeight.w600),
                          ),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.baseline,
                            textBaseline: TextBaseline.alphabetic,
                            children: [
                              Text(
                                userPoints.toString(),
                                style: Theme.of(context).textTheme.displayLarge?.copyWith(
                                      color: Colors.black,
                                      fontSize: 48,
                                      height: 1.0,
                                    ),
                              ),
                              const SizedBox(width: 4),
                              const Text(
                                'Point',
                                style: TextStyle(fontWeight: FontWeight.bold),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 24),
                
                // White Container for list
                Expanded(
                  child: Container(
                    width: double.infinity,
                    decoration: const BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.only(topLeft: Radius.circular(40), topRight: Radius.circular(40)),
                    ),
                    child: RefreshIndicator(
                      onRefresh: _fetchRewards,
                      color: AppTheme.primaryGreen,
                      child: _isLoading 
                        ? const Center(child: CircularProgressIndicator())
                        : Column(
                            children: [
                              const SizedBox(height: 24),
                              // Filter Pills
                              SingleChildScrollView(
                                scrollDirection: Axis.horizontal,
                                padding: const EdgeInsets.symmetric(horizontal: 24),
                                child: Row(
                                  children: _filters.map((filter) {
                                    final isSelected = filter == _selectedFilter;
                                    return GestureDetector(
                                      onTap: () {
                                        setState(() {
                                          _selectedFilter = filter;
                                        });
                                      },
                                      child: Container(
                                        margin: const EdgeInsets.only(right: 12),
                                        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                                        decoration: BoxDecoration(
                                          color: isSelected ? Colors.black : Colors.white,
                                          borderRadius: BorderRadius.circular(20),
                                          border: Border.all(
                                            color: isSelected ? Colors.black : Colors.grey.shade300,
                                          ),
                                        ),
                                        child: Text(
                                          filter,
                                          style: TextStyle(
                                            color: isSelected ? Colors.white : Colors.black,
                                            fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                                          ),
                                        ),
                                      ),
                                    );
                                  }).toList(),
                                ),
                              ),
                              const SizedBox(height: 20),
                              
                              // Voucher List
                              Expanded(
                                child: _rewards.isEmpty 
                                  ? const Center(child: Text("No rewards available"))
                                  : ListView.builder(
                                      physics: const AlwaysScrollableScrollPhysics(),
                                      padding: const EdgeInsets.symmetric(horizontal: 24),
                                      itemCount: _rewards.where((r) => _selectedFilter == 'Semua' || r['category'] == _selectedFilter).length,
                                      itemBuilder: (context, index) {
                                        final filteredRewards = _rewards.where((r) => _selectedFilter == 'Semua' || r['category'] == _selectedFilter).toList();
                                        final reward = filteredRewards[index];
                                        return Padding(
                                          padding: const EdgeInsets.only(bottom: 16),
                                          child: _buildVoucherCard(
                                            reward['id'],
                                            reward['name'],
                                            reward['description'],
                                            reward['points'].toString(),
                                            reward['image'] ?? 'https://via.placeholder.com/150',
                                          ),
                                        );
                                      },
                                    ),
                              ),
                              const SizedBox(height: 100),
                            ],
                          ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildVoucherCard(int id, String title, String subtitle, String points, String imageUrl) {
    return Container(
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
          // Image
          ClipRRect(
            borderRadius: const BorderRadius.only(topLeft: Radius.circular(20), topRight: Radius.circular(20)),
            child: Image.network(
              imageUrl,
              height: 140,
              width: double.infinity,
              fit: BoxFit.cover,
              errorBuilder: (context, error, stackTrace) => Container(height: 140, color: Colors.grey[200], child: const Icon(Icons.image)),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                const SizedBox(height: 4),
                Text(subtitle, style: const TextStyle(color: Colors.grey, fontSize: 12)),
                const SizedBox(height: 16),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.baseline,
                      textBaseline: TextBaseline.alphabetic,
                      children: [
                        Text(points, style: const TextStyle(color: AppTheme.primaryGreen, fontWeight: FontWeight.bold, fontSize: 24)),
                        const SizedBox(width: 4),
                        const Text('Points', style: TextStyle(color: Colors.grey, fontSize: 10)),
                      ],
                    ),
                    ElevatedButton(
                      onPressed: () {
                        _showRewardModal(context, id, title, subtitle, points, imageUrl);
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppTheme.primaryGreen,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                      ),
                      child: const Text('Reedem', style: TextStyle(color: Colors.white)),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  void _showRewardModal(BuildContext context, int id, String title, String subtitle, String points, String imageUrl) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        return Container(
          margin: const EdgeInsets.only(top: 100),
          decoration: const BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.only(topLeft: Radius.circular(40), topRight: Radius.circular(40)),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ClipRRect(
                borderRadius: const BorderRadius.only(topLeft: Radius.circular(40), topRight: Radius.circular(40)),
                child: Image.network(
                  imageUrl,
                  height: 200,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  errorBuilder: (context, error, stackTrace) => Container(height: 200, color: Colors.grey[200], child: const Icon(Icons.image)),
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 20)),
                    const SizedBox(height: 8),
                    Text(subtitle, style: const TextStyle(color: Colors.grey, fontSize: 14)),
                    const SizedBox(height: 40),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.baseline,
                          textBaseline: TextBaseline.alphabetic,
                          children: [
                            Text(points, style: const TextStyle(color: AppTheme.primaryGreen, fontWeight: FontWeight.bold, fontSize: 32)),
                            const SizedBox(width: 4),
                            const Text('Points', style: TextStyle(color: Colors.grey, fontSize: 12)),
                          ],
                        ),
                        ElevatedButton(
                          onPressed: () => _handleRedeem(id),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: AppTheme.primaryGreen,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                          ),
                          child: const Text('Reedem', style: TextStyle(color: Colors.white, fontSize: 16)),
                        ),
                      ],
                    ),
                    const SizedBox(height: 40),
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}
