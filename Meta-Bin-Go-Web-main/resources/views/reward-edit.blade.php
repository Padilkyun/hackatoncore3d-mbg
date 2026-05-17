<x-app-layout>
    <div class="w-full min-h-screen font-['Poppins'] bg-[#F8F9F5] p-8">
        
        <div class="flex items-center space-x-4 mb-8">
            <a href="{{ route('reward-management') }}" class="bg-white p-3 rounded-xl shadow-sm text-gray-500 hover:text-[#062C1B] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <p class="text-xs text-gray-400">Kelola Hadiah Pengguna</p>
                <h1 class="text-2xl font-bold text-[#062C1B]">Edit Reward</h1>
            </div>
        </div>

        <div class="bg-white rounded-[30px] shadow-xl border border-gray-100 p-8 max-w-4xl">
            <form action="{{ route('reward-management.update', $reward->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex flex-col space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Nama Reward</label>
                        <input type="text" name="nama_reward" value="{{ $reward->nama_reward }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#062C1B]/20 focus:border-[#062C1B] transition" required>
                    </div>

                    <div class="flex flex-col space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Syarat Poin</label>
                        <input type="number" name="syarat_point" value="{{ $reward->syarat_point }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#062C1B]/20 focus:border-[#062C1B] transition" required>
                    </div>

                    <div class="flex flex-col space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Kuota (Stok)</label>
                        <input type="number" name="kuota" value="{{ $reward->kuota }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#062C1B]/20 focus:border-[#062C1B] transition" required>
                    </div>

                    <div class="flex flex-col space-y-2">
                        <label class="text-sm font-semibold text-gray-700">Status</label>
                        <select name="status" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#062C1B]/20 focus:border-[#062C1B] transition">
                            <option value="Active" {{ $reward->status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ $reward->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col space-y-2">
                    <label class="text-sm font-semibold text-gray-700">Foto Reward</label>
                    <div class="flex items-center space-x-4 p-4 border border-dashed border-gray-200 rounded-xl">
                        <div class="w-16 h-10 bg-gray-50 border border-gray-100 rounded-md flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('storage/' . $reward->foto_reward) }}" alt="Current Image" class="object-contain max-h-full">
                        </div>
                        <input type="file" name="foto_reward" 
                               class="flex-1 text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-[#062C1B]/10 file:text-[#062C1B] hover:file:bg-[#062C1B]/20 transition">
                    </div>
                    <span class="text-[11px] text-gray-400">*Biarkan kosong jika tidak ingin mengubah foto.</span>
                </div>

                <div class="flex flex-col space-y-2">
                    <label class="text-sm font-semibold text-gray-700">Keterangan</label>
                    <textarea name="keterangan" rows="4" 
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-[#062C1B]/20 focus:border-[#062C1B] transition" required>{{ $reward->keterangan }}</textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('reward-management') }}" class="px-6 py-3 rounded-xl bg-gray-100 text-gray-600 font-semibold text-sm hover:bg-gray-200 transition">Batal</a>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-[#062C1B] text-white font-semibold text-sm hover:bg-opacity-90 shadow-lg shadow-[#062C1B]/20 transition">Perbarui Reward</button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
