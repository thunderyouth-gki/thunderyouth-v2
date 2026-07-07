<div x-data="{
    gettingLocation: false,
    handlePresence() {
        @if(auth()->guest())
            $dispatch('open-auth-modal', { view: 'login' });
            showNotification('Mohon Sign In terlebih dahulu untuk mencatat presensi!', 'error');
        @else
            this.gettingLocation = true;
            
            if (!navigator.geolocation) {
                showNotification('Geolokasi tidak didukung oleh browser Anda.', 'error');
                this.gettingLocation = false;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.gettingLocation = false;
                    $wire.verifyCoordinates(position.coords.latitude, position.coords.longitude);
                },
                (err) => {
                    this.gettingLocation = false;
                    let errorMsg = 'Terjadi kesalahan saat mengambil lokasi.';
                    switch(err.code) {
                        case err.PERMISSION_DENIED:
                            errorMsg = 'Akses lokasi ditolak. Harap izinkan akses lokasi di browser Anda.';
                            break;
                        case err.POSITION_UNAVAILABLE:
                            errorMsg = 'Informasi lokasi tidak tersedia.';
                            break;
                        case err.TIMEOUT:
                            errorMsg = 'Waktu permintaan lokasi habis.';
                            break;
                    }
                    showNotification(errorMsg, 'error');
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        @endif
    }
}" class="flex-1 flex">
    @if($verificationSuccess || $hasAttended)
        <button disabled class="flex-1 bg-emerald-500 text-white py-3 rounded-xl font-semibold transition shadow-soft flex items-center justify-center gap-2 cursor-default w-full">
            <i class="fa-solid fa-check mr-1"></i> Kehadiran Tersimpan
        </button>
    @elseif($isFinished)
        <button disabled class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-400 py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2 cursor-not-allowed w-full border border-slate-200 dark:border-slate-700">
            <i class="fa-solid fa-ban mr-1"></i> Ibadah Selesai
        </button>
    @elseif(!$isLive)
        <button disabled class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-400 py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2 cursor-not-allowed w-full border border-slate-200 dark:border-slate-700">
            <i class="fa-regular fa-clock mr-1"></i> Belum Dimulai
        </button>
    @else
        <button @click="handlePresence" :disabled="gettingLocation || $wire.isVerifying" class="flex-1 bg-primary hover:bg-primary/80 text-white py-3 rounded-xl font-semibold transition shadow-soft hover:shadow-glow flex items-center justify-center gap-2 w-full disabled:opacity-70 disabled:cursor-not-allowed">
            <template x-if="gettingLocation || $wire.isVerifying">
                <span>
                    <i class="fa-solid fa-spinner fa-spin mr-1"></i> Memeriksa Lokasi GPS...
                </span>
            </template>
            <template x-if="!gettingLocation && !$wire.isVerifying">
                <span>
                    <i class="fa-solid fa-location-crosshairs animate-pulse text-secondary"></i> Catat Kehadiran Saya
                </span>
            </template>
        </button>
    @endif

    <flux:modal name="otp-modal-{{ $serviceId }}" class="w-full max-w-md p-6">
        <div class="space-y-6">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-500/20 text-purple-500 mb-4">
                    <i class="fa-solid fa-unlock-keyhole text-xl"></i>
                </div>
                <h3 class="text-xl font-bold font-heading text-text">Verifikasi OTP Kehadiran</h3>
                <p class="text-sm text-textlight mt-2 leading-relaxed">Silakan masukkan 6-digit kode OTP yang tampil di layar proyektor untuk mencatat kehadiran Anda.</p>
            </div>
            
            <form wire:submit.prevent="submitOtp" class="space-y-4">
                <input type="text" wire:model="otp" maxlength="6" class="w-full text-center text-3xl font-black tracking-[0.5em] bg-white dark:bg-zinc-900 border border-purple-300 dark:border-purple-600 rounded-xl py-4 text-purple-900 dark:text-purple-100 focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition uppercase" placeholder="------" required>
                
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="$dispatch('modal-close', {name: 'otp-modal-{{ $serviceId }}'})" class="flex-1 bg-surface border border-accent text-text hover:bg-slate-50 dark:hover:bg-accent/50 py-3 rounded-xl font-semibold transition">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-xl font-bold transition shadow-md shadow-purple-600/20">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>
