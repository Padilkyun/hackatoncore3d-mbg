import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;

class ApiService {
  final String baseUrl = "http://10.162.1.144:8000/api"; // Default for Android Emulator to localhost

  // Mock global state for user (In real app, use a proper state management)
  static Map<String, dynamic>? currentUser;

  Future<Map<String, dynamic>> register(String username, String password, File image) async {
    var request = http.MultipartRequest('POST', Uri.parse("$baseUrl/register/"));
    request.fields['username'] = username;
    request.fields['password'] = password;
    request.files.add(await http.MultipartFile.fromPath('image', image.path));
    
    var response = await request.send();
    var responseData = await response.stream.bytesToString();
    var result = json.decode(responseData);
    
    if (response.statusCode == 200) {
      currentUser = result['user'];
      return result;
    } else {
      throw Exception(result['message'] ?? 'Failed to register');
    }
  }

  Future<Map<String, dynamic>> login({String? username, String? password, File? image}) async {
    var request = http.MultipartRequest('POST', Uri.parse("$baseUrl/login/"));
    if (username != null) request.fields['username'] = username;
    if (password != null) request.fields['password'] = password;
    if (image != null) {
      request.files.add(await http.MultipartFile.fromPath('image', image.path));
    }
    
    var response = await request.send();
    var responseData = await response.stream.bytesToString();
    var result = json.decode(responseData);
    
    if (response.statusCode == 200) {
      currentUser = result['user'];
      return result;
    } else {
      throw Exception(result['message'] ?? 'Login failed');
    }
  }

  Future<List<dynamic>> getRewards() async {
    final response = await http.get(Uri.parse("$baseUrl/rewards/"));
    if (response.statusCode == 200) {
      var result = json.decode(response.body);
      return result['rewards'];
    } else {
      throw Exception('Failed to load rewards');
    }
  }

  Future<Map<String, dynamic>> claimReward(int rewardId) async {
    if (currentUser == null) throw Exception("Not logged in");
    
    final response = await http.post(
      Uri.parse("$baseUrl/claim-reward/"),
      body: {
        'username': currentUser!['username'],
        'reward_id': rewardId.toString(),
      },
    );
    
    var result = json.decode(response.body);
    if (response.statusCode == 200) {
      // Update local points
      currentUser!['points'] = result['new_points'];
      return result;
    } else {
      throw Exception(result['message'] ?? 'Failed to claim reward');
    }
  }

  Future<Map<String, dynamic>> processWaste(String imagePath, int binId) async {
    var request = http.MultipartRequest('POST', Uri.parse("$baseUrl/process-action/"));
    request.fields['bin_id'] = binId.toString();
    request.files.add(await http.MultipartFile.fromPath('image', imagePath));
    
    var response = await request.send();
    if (response.statusCode == 200) {
      var result = json.decode(await response.stream.bytesToString());
      if (currentUser != null && result['user'] == currentUser!['username']) {
        currentUser!['points'] = result['total_points'];
      }
      return result;
    } else {
      throw Exception('Failed to process waste');
    }
  }

  Future<List<dynamic>> getPurchaseHistory() async {
    if (currentUser == null) throw Exception("Not logged in");
    final response = await http.get(Uri.parse("$baseUrl/purchase-history/?username=${currentUser!['username']}"));
    if (response.statusCode == 200) {
      var result = json.decode(response.body);
      return result['history'];
    } else {
      throw Exception('Failed to load history');
    }
  }

  Future<Map<String, dynamic>> updateProfile({String? newUsername, File? image}) async {
    if (currentUser == null) throw Exception("Not logged in");
    var request = http.MultipartRequest('POST', Uri.parse("$baseUrl/update-profile/"));
    request.fields['username'] = currentUser!['username'];
    if (newUsername != null) request.fields['new_username'] = newUsername;
    if (image != null) {
      request.files.add(await http.MultipartFile.fromPath('image', image.path));
    }
    
    var response = await request.send();
    var responseData = await response.stream.bytesToString();
    var result = json.decode(responseData);
    
    if (response.statusCode == 200) {
      currentUser = result['user'];
      return result;
    } else {
      throw Exception(result['message'] ?? 'Update failed');
    }
  }

  Future<void> getUserInfo() async {
    if (currentUser == null) return;
    final response = await http.get(Uri.parse("$baseUrl/user-info/?username=${currentUser!['username']}"));
    if (response.statusCode == 200) {
      var result = json.decode(response.body);
      currentUser = result['user'];
    }
  }

  Future<List<dynamic>> fetchBins() async {
    final response = await http.get(Uri.parse("$baseUrl/bins/"));
    if (response.statusCode == 200) {
      var result = json.decode(response.body);
      return result['bins'];
    } else {
      throw Exception('Failed to fetch bins');
    }
  }

  Future<Map<String, dynamic>> getDashboardStats() async {
    final response = await http.get(Uri.parse("$baseUrl/dashboard-stats/"));
    if (response.statusCode == 200) {
      var result = json.decode(response.body);
      return result['stats'];
    } else {
      throw Exception('Failed to fetch dashboard stats');
    }
  }

  Future<bool> triggerBin(int binId, {String? username}) async {
    try {
      final response = await http.post(
        Uri.parse("$baseUrl/trigger-session/"),
        body: {
          'bin_id': binId.toString(),
          if (username != null) 'username': username,
        },
      );
      return response.statusCode == 200;
    } catch (e) {
      print('Trigger error: $e');
      return false;
    }
  }

  Future<String?> checkResult(int binId) async {
    try {
      final response = await http.get(Uri.parse("$baseUrl/check-result/?bin_id=$binId"));
      if (response.statusCode == 200) {
        var data = jsonDecode(response.body);
        if (data['status'] == 'success') {
          return data['result'];
        }
      }
    } catch (e) {
      print('Check result error: $e');
    }
    return null;
  }
}
