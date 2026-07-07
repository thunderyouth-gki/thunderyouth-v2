<div class="max-w-xl mx-auto mt-8">
    <div class="bg-surface rounded-3xl p-8 shadow-card border border-accent text-center relative overflow-hidden">
        <!-- Decorative Background Elements -->
        <div class="absolute -top-12 -right-12 w-32 h-32 bg-primary/5 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-secondary/5 rounded-full blur-2xl"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 text-primary mb-4">
                <i class="fa-solid fa-map-location-dot text-2xl"></i>
            </div>
            <h3 class="font-heading font-bold text-2xl text-text mb-2">Verifikasi Kehadiran</h3>
            <p class="text-textlight text-sm mb-6 max-w-sm mx-auto">Kami perlu memverifikasi lokasi Anda untuk mencatat kehadiran di kebaktian hari ini.</p>

            @if($verificationSuccess || $hasAttended)
                <div class="p-5 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl border border-emerald-200 dark:border-emerald-500/20">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-500 mb-3">
                        <i class="fa-solid fa-check text-xl"></i>
                    </div>
                    <h4 class="font-bold text-emerald-700 dark:text-emerald-400 mb-1">Sudah Absen!</h4>
                    <p class="text-xs text-emerald-600 dark:text-emerald-500">Terima kasih, kehadiran Anda telah terverifikasi untuk ibadah ini.</p>
                </div>
            @elseif($gpsValid)
                @if($errorMessage)
                    <div class="p-4 mb-6 bg-red-50 dark:bg-red-500/10 rounded-2xl border border-red-200 dark:border-red-500/20 text-left flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                        <p class="text-sm text-red-700 dark:text-red-400">{{ $errorMessage }}</p>
                    </div>
                @endif
                
                <div class="p-5 bg-purple-50 dark:bg-purple-500/10 rounded-2xl border border-purple-200 dark:border-purple-500/20">
                    <p class="text-sm text-purple-700 dark:text-purple-400 mb-4">Masukkan 6-digit kode OTP yang tertera di layar proyektor untuk mencatat kehadiran.</p>
                    
                    <form wire:submit.prevent="submitOtp" class="space-y-4">
                        <div>
                            <input type="text" wire:model="otp" maxlength="6" class="w-full text-center text-3xl font-black tracking-[0.5em] bg-white dark:bg-zinc-900 border border-purple-300 dark:border-purple-600 rounded-xl py-3 text-purple-900 dark:text-purple-100 focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition uppercase" placeholder="------" required>
                        </div>
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-xl font-bold transition shadow-md shadow-purple-600/20 flex items-center justify-center gap-2">
                            Konfirmasi Kehadiran
                        </button>
                    </form>
                </div>
            @else
                @if($errorMessage)
                    <div class="p-4 mb-6 bg-red-50 dark:bg-red-500/10 rounded-2xl border border-red-200 dark:border-red-500/20 text-left flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                        <p class="text-sm text-red-700 dark:text-red-400">{{ $errorMessage }}</p>
                    </div>
                @endif

                @if(!$serviceId)
                    <button disabled class="w-full bg-slate-100 text-slate-400 py-3 rounded-xl font-semibold cursor-not-allowed border border-slate-200 dark:border-slate-700">Tidak ada ibadah aktif</button>
                @elseif($isFinished)
                    <button disabled class="w-full bg-slate-100 dark:bg-slate-800 text-slate-400 py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2 cursor-not-allowed border border-slate-200 dark:border-slate-700">
                        <i class="fa-solid fa-ban mr-1"></i> Ibadah Selesai
                    </button>
                @elseif(!$isLive)
                    <button disabled class="w-full bg-slate-100 dark:bg-slate-800 text-slate-400 py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2 cursor-not-allowed border border-slate-200 dark:border-slate-700">
                        <i class="fa-regular fa-clock mr-1"></i> Belum Dimulai
                    </button>
                @else
                    <div x-data="{
                        gettingLocation: false,
                        error: null,
                        requestLocation() {
                            this.gettingLocation = true;
                            this.error = null;
                            
                            if (!navigator.geolocation) {
                                this.error = 'Geolokasi tidak didukung oleh browser Anda.';
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
                                    switch(err.code) {
                                        case err.PERMISSION_DENIED:
                                            this.error = 'Akses lokasi ditolak. Harap izinkan akses lokasi di browser Anda.';
                                            break;
                                        case err.POSITION_UNAVAILABLE:
                                            this.error = 'Informasi lokasi tidak tersedia.';
                                            break;
                                        case err.TIMEOUT:
                                            this.error = 'Waktu permintaan lokasi habis.';
                                            break;
                                        default:
                                            this.error = 'Terjadi kesalahan saat mengambil lokasi.';
                                            break;
                                    }
                                },
                                {
                                    enableHighAccuracy: true,
                                    timeout: 10000,
                                    maximumAge: 0
                                }
                            );
                        }
                    }">
                        <template x-if="error">
                            <div class="p-4 mb-4 bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 text-sm rounded-2xl border border-red-200 dark:border-red-500/20 text-left flex gap-3">
                                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                                <span x-text="error"></span>
                            </div>
                        </template>
                        
                        <button 
                            @click="requestLocation" 
                            :disabled="gettingLocation || $wire.isVerifying" 
                            class="w-full bg-primary hover:bg-primary/80 text-white py-4 rounded-2xl font-bold transition shadow-soft hover:shadow-glow flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                            <template x-if="gettingLocation || $wire.isVerifying">
                                <span><i class="fa-solid fa-spinner fa-spin mr-2"></i> Sedang Memverifikasi...</span>
                            </template>
                            <template x-if="!gettingLocation && !$wire.isVerifying">
                                <span><i class="fa-solid fa-location-crosshairs animate-pulse mr-2 text-secondary"></i> Ketuk untuk Verifikasi Lokasi</span>
                            </template>
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
