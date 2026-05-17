import 'package:flutter/material.dart';
import 'package:meta_bin_go/theme.dart';
import 'package:meta_bin_go/services/api_service.dart';
import 'package:meta_bin_go/widgets/custom_feedback.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final TextEditingController _usernameController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  File? _image;
  bool _isLoading = false;
  final ApiService _apiService = ApiService();

  Future<void> _pickImage() async {
    final ImagePicker picker = ImagePicker();
    final XFile? image = await picker.pickImage(source: ImageSource.camera);
    
    if (image != null) {
      setState(() {
        _image = File(image.path);
      });
    }
  }

  Future<void> _handleRegister() async {
    if (_image == null) {
      CustomFeedback.showErrorSnackBar(context, 'Please take a face photo for registration');
      return;
    }

    setState(() => _isLoading = true);
    try {
      await _apiService.register(
        _usernameController.text,
        _passwordController.text,
        _image!,
      );
      if (mounted) {
        CustomFeedback.showSuccessSnackBar(context, 'Registration successful! Please login.');
        Navigator.pop(context);
      }
    } catch (e) {
      if (mounted) {
        CustomFeedback.showErrorSnackBar(context, e.toString());
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.surfaceWhite,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: AppTheme.primaryGreen),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(32.0),
        child: Column(
          children: [
            Text(
              'Register',
              style: Theme.of(context).textTheme.displayMedium?.copyWith(
                    color: AppTheme.primaryGreen,
                    fontSize: 32,
                    fontWeight: FontWeight.w800,
                  ),
            ),
            const SizedBox(height: 40),
            
            // Photo Picker
            GestureDetector(
              onTap: _pickImage,
              child: Container(
                width: 150,
                height: 150,
                decoration: BoxDecoration(
                  color: Colors.grey[200],
                  borderRadius: BorderRadius.circular(75),
                  border: Border.all(color: AppTheme.primaryGreen, width: 2),
                ),
                child: _image != null
                    ? ClipRRect(
                        borderRadius: BorderRadius.circular(75),
                        child: Image.file(_image!, fit: BoxFit.cover),
                      )
                    : const Icon(Icons.add_a_photo, size: 50, color: AppTheme.primaryGreen),
              ),
            ),
            const Text('Tap to take face photo', style: TextStyle(color: Colors.grey)),
            const SizedBox(height: 40),

            TextField(
              controller: _usernameController,
              decoration: InputDecoration(
                hintText: 'Username',
                prefixIcon: const Icon(Icons.person_outline),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(20)),
              ),
            ),
            const SizedBox(height: 20),
            TextField(
              controller: _passwordController,
              obscureText: true,
              decoration: InputDecoration(
                hintText: 'Password',
                prefixIcon: const Icon(Icons.key_outlined),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(20)),
              ),
            ),
            const SizedBox(height: 40),

            SizedBox(
              width: double.infinity,
              height: 56,
              child: ElevatedButton(
                onPressed: _isLoading ? null : _handleRegister,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppTheme.primaryGreen,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                ),
                child: _isLoading 
                  ? const CircularProgressIndicator(color: Colors.white)
                  : const Text('Register', style: TextStyle(fontSize: 18, color: Colors.white)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
