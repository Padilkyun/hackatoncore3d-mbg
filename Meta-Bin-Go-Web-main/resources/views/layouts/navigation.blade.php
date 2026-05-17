<div class="flex flex-col relative h-screen sticky top-0 overflow-y-auto" style="width: 271px; background-image: url('{{ asset('image/navbar.png') }}'); background-size: cover; background-position: center;">

    <div class="absolute inset-0 bg-[#062C1B] opacity-10"></div>

    <div class="relative z-10 flex flex-col h-full text-white">

        <div class="p-8 text-center px-4">
            <h1 class="text-2xl font-bold tracking-tight text-white" style="font-family: 'Poppins', sans-serif;">
                Meta Bin Go
            </h1>
        </div>

        <nav class="flex-1 px-4 space-y-2">

    <a href="{{ route('dashboard') }}" 
       class="flex items-center space-x-3 p-3 rounded-xl transition group 
       {{ request()->routeIs('dashboard') ? 'bg-white text-[#062C1B] font-semibold shadow-lg' : 'text-gray-300 hover:bg-white/10' }}">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
        </svg>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('bin-monitoring') }}" class="flex items-center space-x-3 p-3 rounded-xl transition group {{ request()->routeIs('bin-monitoring') ? 'bg-white text-[#062C1B] font-semibold shadow-lg' : 'text-gray-300 hover:bg-white/10' }}">
        <svg class="w-6 h-6 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        <span>Bin Monitoring</span>
    </a>

    <a href="{{ route('route-map') }}" class="flex items-center space-x-3 p-3 rounded-xl transition group {{ request()->routeIs('route-map') ? 'bg-white text-[#062C1B] font-semibold shadow-lg' : 'text-gray-300 hover:bg-white/10' }}">
        <svg class="w-6 h-6 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
        <span>Route Map</span>
    </a>

    <a href="{{ route('air-monitoring') }}" class="flex items-center space-x-3 p-3 rounded-xl transition group {{ request()->routeIs('air-monitoring') ? 'bg-white text-[#062C1B] font-semibold shadow-lg' : 'text-gray-300 hover:bg-white/10' }}">
        <svg class="w-6 h-6 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
        <span>Air Monitoring</span>
    </a>

    <a href="{{ route('reward-management') }}" class="flex items-center space-x-3 p-3 rounded-xl transition group {{ request()->routeIs('reward-management') ? 'bg-white text-[#062C1B] font-semibold shadow-lg' : 'text-gray-300 hover:bg-white/10' }}">
        <svg class="w-6 h-6 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
        <span>Reward Management</span>
    </a>
</nav>

        <div class="p-4 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 p-3 hover:bg-red-600/80 rounded-xl transition group">
                    <svg class="w-6 h-6 text-gray-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </div>
</div>
