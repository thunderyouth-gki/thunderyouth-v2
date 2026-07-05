<!-- DEMO CONTROLLER (Sangat Membantu untuk Evaluasi Skenario) -->
<div x-data="{ 
        minimize: true,
        simulateScan() {
            if ({{ auth()->guest() ? 'true' : 'false' }}) {
                showNotification('Silakan Sign In terlebih dahulu untuk melakukan scanning!', 'error');
                return;
            }
            showNotification('Menjalankan Scanner QR / NFC...', 'success');
            setTimeout(() => {
                showNotification('Sukses! Kehadiran otomatis dicatat melalui QR/NFC (KF06)', 'success');
            }, 1000);
        }
    }" 
    class="fixed bottom-4 left-4 z-45 transition-all duration-300 ease-in-out">
    
    <!-- Minimized Floating Button -->
    <button x-show="minimize" @click="minimize = false" class="bg-primary hover:bg-primary/90 text-white w-12 h-12 rounded-full shadow-2xl flex items-center justify-center border-2 border-white/20 transition transform hover:scale-105 active:scale-95" style="display: none;">
        <i class="fa-solid fa-flask text-lg text-secondary animate-pulse"></i>
    </button>

    <!-- Full Panel Sandbox -->
    <div x-show="!minimize" class="bg-surface/95 backdrop-blur-md rounded-2xl p-4 shadow-2xl border-2 border-primary/20 max-w-xs text-xs">
        <div class="flex items-center justify-between border-b border-accent pb-2 mb-3">
            <span class="font-bold text-primary flex items-center gap-1.5"><i class="fa-solid fa-flask"></i> Sandbox Demo</span>
            <div class="flex items-center gap-1.5">
                <span class="bg-primary/10 text-primary font-bold px-2 py-0.5 rounded text-[9px]">Interactive</span>
                <button @click="minimize = true" class="text-textlight hover:text-red-500 transition p-1" title="Sembunyikan Panel">
                    <i class="fa-solid fa-minus text-sm"></i>
                </button>
            </div>
        </div>
        <p class="text-textlight mb-3 leading-relaxed">Status koneksi DB simulasi Alpine.js & Laravel Blade.</p>
        
        <div class="space-y-2">
            <div class="border-t border-accent pt-2">
                <label class="block font-bold mb-1">Skenario Presensi Otomatis (KF06):</label>
                <button @click="simulateScan()" class="w-full bg-secondary text-primary font-extrabold py-1.5 rounded shadow-soft hover:bg-yellow-400 transition flex items-center justify-center gap-1">
                    <i class="fa-solid fa-qrcode"></i> Scan QR/NFC Simulator
                </button>
            </div>
        </div>
    </div>
</div>
