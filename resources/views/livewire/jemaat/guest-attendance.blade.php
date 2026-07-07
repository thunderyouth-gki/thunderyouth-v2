<div class="max-w-xl mx-auto mt-8">
    <div class="bg-surface rounded-3xl p-8 shadow-card border border-accent text-center relative overflow-hidden">
        <!-- Decorative Background Elements -->
        <div class="absolute -top-12 -right-12 w-32 h-32 bg-primary/5 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-12 -left-12 w-32 h-32 bg-secondary/5 rounded-full blur-2xl"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 text-primary mb-4">
                <i class="fa-solid fa-user-check text-2xl"></i>
            </div>
            <h3 class="font-heading font-bold text-2xl text-text mb-2">Pencatatan Kehadiran Tamu</h3>
            <p class="text-textlight text-sm mb-6 max-w-sm mx-auto">
                @if($gpsValid)
                    Verifikasi OTP berhasil.
                @else
                    Silakan masukkan 6 digit kode OTP yang ditampilkan di layar ibadah untuk mencatat kehadiran tamu.
                @endif
            </p>

            @if($verificationSuccess || $hasAttended)
                <div class="p-5 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl border border-emerald-200 dark:border-emerald-500/20">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-500 mb-3">
                        <i class="fa-solid fa-check text-xl"></i>
                    </div>
                    <h4 class="font-bold text-emerald-700 dark:text-emerald-400 mb-1">Sudah Absen!</h4>
                    <p class="text-xs text-emerald-600 dark:text-emerald-500">Terima kasih, kehadiran perangkat ini telah terverifikasi. Selamat beribadah!</p>
                </div>
            @elseif($gpsValid)
                @if($errorMessage)
                    <div class="p-4 mb-6 bg-red-50 dark:bg-red-500/10 rounded-2xl border border-red-200 dark:border-red-500/20 text-left flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                        <p class="text-sm text-red-700 dark:text-red-400">{{ $errorMessage }}</p>
                    </div>
                @endif
                
                <div class="p-5 bg-purple-50 dark:bg-purple-500/10 rounded-2xl border border-purple-200 dark:border-purple-500/20">
                    @if($showMatches)
                        <h4 class="font-bold text-purple-700 dark:text-purple-400 mb-2">Apakah nama Anda ada di bawah ini?</h4>
                        <p class="text-sm text-purple-600 dark:text-purple-500 mb-4">Kami menemukan nama yang mirip di database jemaat kami.</p>
                        
                        <div class="space-y-2 mb-4 max-h-48 overflow-y-auto">
                            @foreach($matchedMembers as $member)
                                <button wire:click="selectMember({{ $member->id }})" class="w-full bg-white dark:bg-zinc-800 border border-purple-200 dark:border-purple-500/30 hover:border-purple-500 hover:shadow-md py-3 px-4 rounded-xl text-left transition flex items-center justify-between group">
                                    <div>
                                        <div class="font-bold text-zinc-800 dark:text-zinc-200">{{ $member->name }}</div>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-purple-300 group-hover:text-purple-600 transition"></i>
                                </button>
                            @endforeach
                        </div>
                        
                        <button wire:click="notMyName" class="w-full bg-surface text-purple-600 border border-purple-200 dark:border-purple-500/30 hover:bg-purple-100 dark:hover:bg-purple-500/20 py-3 rounded-xl font-semibold transition text-sm">
                            Tidak ada nama saya di list ini
                        </button>
                    @else
                        <p class="text-sm text-purple-700 dark:text-purple-400 mb-4">Silakan masukkan nama Anda di bawah ini untuk dicatat sebagai tamu.</p>
                        
                        <form wire:submit.prevent="submitGuest" class="space-y-4">
                            <div class="text-left">
                                <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Nama Lengkap</label>
                                <input type="text" wire:model="guestName" class="w-full bg-white dark:bg-zinc-900 border border-purple-300 dark:border-purple-600 rounded-xl py-3 px-4 text-zinc-900 dark:text-zinc-100 focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition" placeholder="Masukkan nama Anda" required>
                                @error('guestName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-xl font-bold transition shadow-md shadow-purple-600/20 flex items-center justify-center gap-2">
                                Simpan Kehadiran
                            </button>
                        </form>
                    @endif
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
                @elseif(auth()->check())
                    <div class="p-4 mb-6 bg-blue-50 dark:bg-blue-500/10 rounded-2xl border border-blue-200 dark:border-blue-500/20 text-left flex items-start gap-3">
                        <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                        <p class="text-sm text-blue-700 dark:text-blue-400">Anda sudah masuk ke dalam akun. Halaman ini khusus untuk tamu. Silakan catat kehadiran Anda melalui dashboard jemaat.</p>
                    </div>
                    <a href="{{ route('dashboard') }}" class="w-full bg-primary hover:bg-primary/80 text-white py-4 rounded-2xl font-bold transition shadow-soft hover:shadow-glow flex items-center justify-center gap-2" wire:navigate>
                        Kembali ke Dashboard
                    </a>
                @else
                    <form wire:submit.prevent="verifyOtp" class="space-y-4">
                        <div class="text-left">
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Kode OTP</label>
                            <input type="text" wire:model="otpInput" class="w-full bg-white dark:bg-zinc-900 border border-purple-300 dark:border-purple-600 rounded-xl py-3 px-4 text-zinc-900 dark:text-zinc-100 focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition text-center text-xl tracking-widest font-mono" placeholder="------" maxlength="6" required>
                            @error('otpInput') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="w-full bg-primary hover:bg-primary/80 text-white py-4 rounded-2xl font-bold transition shadow-soft hover:shadow-glow flex items-center justify-center gap-2" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="verifyOtp"><i class="fa-solid fa-check mr-2 text-secondary"></i> Verifikasi OTP</span>
                            <span wire:loading wire:target="verifyOtp"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Sedang Memverifikasi...</span>
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>
