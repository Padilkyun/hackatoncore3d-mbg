import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppTheme {
  static const Color primaryGreen = Color(0xFF113c23); // Button color
  static const Color backgroundDark = Color(0xFF021509); // Dark leaf background
  static const Color surfaceWhite = Colors.white; // White containers
  static const Color textPrimary = Color(0xFF10281b); // Dark green text on white
  static const Color textSecondary = Color(0xFF757575); // Gray text
  static const Color accentColor = Color(0xFF0ea64b); // Green status etc

  static ThemeData get lightTheme {
    return ThemeData(
      primaryColor: primaryGreen,
      scaffoldBackgroundColor: backgroundDark,
      textTheme: GoogleFonts.poppinsTextTheme().copyWith(
        displayLarge: GoogleFonts.poppins(color: surfaceWhite, fontWeight: FontWeight.bold),
        displayMedium: GoogleFonts.poppins(color: surfaceWhite, fontWeight: FontWeight.bold),
        bodyLarge: GoogleFonts.poppins(color: textPrimary),
        bodyMedium: GoogleFonts.poppins(color: textPrimary),
      ),
      colorScheme: ColorScheme.light(
        primary: primaryGreen,
        secondary: accentColor,
        surface: surfaceWhite,
      ),
    );
  }
}
