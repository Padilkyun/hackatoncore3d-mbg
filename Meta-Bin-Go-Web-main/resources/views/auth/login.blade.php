<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-[#04160E] p-4 font-['Poppins'] select-none">
        <div class="flex flex-col md:flex-row w-full max-w-5xl bg-white rounded-[40px] overflow-hidden shadow-[0_25px_60px_rgba(0,0,0,0.4)]">
            
            <div class="md:w-1/2 relative min-h-[350px] md:min-h-[550px] bg-[#062C1B]">
                <img src="{{ asset('image/bg.png') }}" alt="Background Cover" class="absolute inset-0 w-full h-full object-cover" />
                
                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                
                <div class="absolute bottom-12 left-12 z-10 flex items-center space-x-5"> <img src="{{ asset('image/logo.png') }}" alt="Logo Meta Bin Go" class="w-30 h-30 object-contain">
                    <div class="text-white font-['Poppins']">
                        <h2 class="text-2xl font-bold leading-tight tracking-wide">Meta</h2>
                        <h2 class="text-2xl font-bold leading-tight tracking-wide">Bin</h2>
                        <h2 class="text-2xl font-bold leading-tight tracking-wide">Go</h2>
                    </div>
                </div>
            </div>

            <div class="md:w-1/2 p-8 sm:p-12 md:p-16 bg-[#FAF9F5] flex flex-col justify-center">
                <div class="w-full max-w-sm mx-auto">
                    <div class="text-center mb-10">
                        <h1 class="text-4xl font-extrabold text-[#062C1B] tracking-tight">Login</h1>
                        <p class="text-xs text-gray-400 mt-2 font-medium tracking-wide">Let's get back to work</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        
                        <div class="flex flex-col space-y-1.5">
                            <div class="relative flex items-center">
                                <span class="absolute left-4 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </span>
                                <input type="email" name="email" placeholder="E-mail" 
                                       class="w-full pl-12 pr-4 py-3.5 bg-transparent border border-gray-400 rounded-2xl text-sm text-[#062C1B] placeholder-gray-400 focus:outline-none focus:border-[#062C1B] focus:ring-1 focus:ring-[#062C1B] transition font-medium" required>
                            </div>
                        </div>

                        <div class="flex flex-col space-y-1.5">
                            <div class="relative flex items-center">
                                <span class="absolute left-4 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a3 3 0 01-3 3m0 0H3.75a3 3 0 01-3-3m0 0a3 3 0 013-3h12m0 0v6"></path>
                                    </svg>
                                </span>
                                <input type="password" id="password" name="password" placeholder="Password" 
                                       class="w-full pl-12 pr-12 py-3.5 bg-transparent border border-gray-400 rounded-2xl text-sm text-[#062C1B] placeholder-gray-400 focus:outline-none focus:border-[#062C1B] focus:ring-1 focus:ring-[#062C1B] transition font-medium" required>
                                
                                <button type="button" onclick="togglePasswordVisibility()" class="absolute right-4 text-gray-500 hover:text-[#062C1B] focus:outline-none">
                                    <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.644M21.964 12.322a1.012 1.012 0 010-.644M12 18.75c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"></path>
                                        <circle cx="12" cy="11" r="3" stroke-linecap="round" stroke-linejoin="round"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="text-left">
                            <a href="#" class="text-[11px] font-bold text-[#062C1B] hover:underline tracking-wide">Forgot your password?</a>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full py-3.5 rounded-2xl bg-[#062C1B] text-white text-sm font-bold tracking-wide shadow-md shadow-[#062C1B]/20 hover:bg-[#0c422a] transition duration-200">
                                Login
                            </button>
                        </div>

                        <p class="text-center text-[11px] text-gray-400 font-medium tracking-wide pt-1">
                            Don't have an account? <a href="{{ route('register') }}" class="text-[#062C1B] font-bold hover:underline">Register</a>
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.98 8.223A10.477 10.477 0 001.934 11.66a1.012 1.012 0 000 .644m21.943-3.418a10.483 10.483 0 00-2.253-3.125m-4.217-1.745a9.716 9.716 0 00-4.162-.962c-5.385 0-9.75 4.365-9.75 9.75 0 1.258.238 2.46.671 3.567m16.592-1.393L3 3m4.008 4.008a3 3 0 114.243 4.243m4.243 4.243a3 3 0 01-4.243-4.243" />`;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.036 12.322a1.012 1.012 0 010-.644M21.964 12.322a1.012 1.012 0 010-.644M12 18.75c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z" /><circle cx="12" cy="11" r="3" stroke-linecap="round" stroke-linejoin="round"></circle>`;
            }
        }
    </script>
</x-guest-layout>