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
    @if($verificationSuccess)
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
</div>
