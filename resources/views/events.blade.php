<x-layouts.app title="Events - Thunder Youth">
    <div class="view-section fade-in">
        <!-- Header Events -->
        <div class="bg-surface border-b border-accent pt-10 pb-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="bg-secondary/20 text-yellow-800 dark:text-yellow-400 text-xs font-bold px-3 py-1.5 rounded-full inline-block mb-3 uppercase tracking-wider">Persekutuan & Komunitas</span>
                <h1 class="text-3xl md:text-4xl font-heading font-extrabold text-primary mb-4">Upcoming Events & Activities</h1>
                <p class="text-textlight max-w-2xl mx-auto text-sm md:text-base leading-relaxed">
                    Daftarkan dirimu dan ikuti ragam komunitas, seminar interaktif, dan olahraga bersama rekan-rekan pemuda GKI Guntur.
                </p>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20 space-y-8 pb-20" x-data="{ currentFilter: 'all' }">
            <!-- Interactive Filter Component -->
            <div class="bg-surface rounded-3xl p-3 shadow-soft border border-accent flex flex-wrap gap-2 overflow-x-auto no-scrollbar">
                <button @click="currentFilter = 'all'" :class="currentFilter === 'all' ? 'bg-primary text-white' : 'bg-background text-textlight hover:text-primary'" class="px-6 py-2.5 rounded-2xl text-sm font-semibold transition">
                    Semua Kategori
                </button>
                <button @click="currentFilter = 'Sports'" :class="currentFilter === 'Sports' ? 'bg-primary text-white' : 'bg-background text-textlight hover:text-primary'" class="px-6 py-2.5 rounded-2xl text-sm font-medium transition">
                    <i class="fa-solid fa-volleyball mr-1 text-emerald-500"></i> Olahraga (Sports)
                </button>
                <button @click="currentFilter = 'Seminar'" :class="currentFilter === 'Seminar' ? 'bg-primary text-white' : 'bg-background text-textlight hover:text-primary'" class="px-6 py-2.5 rounded-2xl text-sm font-medium transition">
                    <i class="fa-solid fa-graduation-cap mr-1 text-amber-500"></i> Seminar
                </button>
                <button @click="currentFilter = 'Fellowship'" :class="currentFilter === 'Fellowship' ? 'bg-primary text-white' : 'bg-background text-textlight hover:text-primary'" class="px-6 py-2.5 rounded-2xl text-sm font-medium transition">
                    <i class="fa-solid fa-people-group mr-1 text-violet-500"></i> Persekutuan (Fellowship)
                </button>
            </div>

            <!-- Dynamic Events Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Event 1 (Sports) -->
                <div x-show="currentFilter === 'all' || currentFilter === 'Sports'" class="bg-surface rounded-3xl shadow-soft border border-accent overflow-hidden flex flex-col justify-between group hover:shadow-card hover:-translate-y-1 transition duration-300">
                    <div>
                        <div class="h-48 relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1708312604109-16c0be9326cd?q=80&w=1074&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Badminton">
                            <span class="absolute top-4 left-4 bg-emerald-500 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">Sports</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-heading font-bold text-lg text-text mb-3">Thunder Club: Badminton</h3>
                            <div class="space-y-2 text-xs text-textlight mb-4">
                                <div class="flex items-center"><i class="fa-regular fa-calendar w-5 text-primary"></i> 09 Jun 2026</div>
                                <div class="flex items-center"><i class="fa-regular fa-clock w-5 text-primary"></i> 15:00 - 18:00 WIB</div>
                                <div class="flex items-center"><i class="fa-solid fa-location-dot w-5 text-primary"></i> Gor Lodaya</div>
                                <div class="flex items-center"><i class="fa-solid fa-receipt w-5 text-primary"></i> Rp 15.000 / Bulan (Biaya Partisipasi)</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 border-t border-accent bg-slate-50/50" x-data="{ registered: false }">
                        <template x-if="!registered">
                            <button @click="
                                @if(auth()->guest())
                                    $dispatch('open-auth-modal', { view: 'login' });
                                    showNotification('Silakan Sign In untuk mendaftar kegiatan!', 'error');
                                @else
                                    registered = true;
                                    showNotification('Berhasil! Anda terdaftar pada kegiatan: Badminton Club (KF07)', 'success');
                                @endif
                            " class="w-full text-primary dark:text-white bg-brandlight dark:bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 hover:text-white py-2.5 rounded-xl font-bold text-sm transition shadow-soft flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-user-plus text-xs"></i> Gabung Sekarang
                            </button>
                        </template>
                        <template x-if="registered">
                            <div class="w-full bg-emerald-50 border border-emerald-200 text-emerald-600 py-2.5 rounded-xl font-bold text-sm transition flex items-center justify-center gap-1.5 cursor-default">
                                <i class="fa-solid fa-check mr-1"></i> Anda Telah Terdaftar
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Event 2 (Seminar) -->
                <div x-show="currentFilter === 'all' || currentFilter === 'Seminar'" class="bg-surface rounded-3xl shadow-soft border border-accent overflow-hidden flex flex-col justify-between group hover:shadow-card hover:-translate-y-1 transition duration-300">
                    <div>
                        <div class="h-48 relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=600&q=80" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Seminar">
                            <span class="absolute top-4 left-4 bg-amber-500 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">Seminar</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-heading font-bold text-lg text-text mb-3">Seminar: Kasih Aba-Aba</h3>
                            <div class="space-y-2 text-xs text-textlight mb-4">
                                <div class="flex items-center"><i class="fa-regular fa-calendar w-5 text-primary"></i> 13 Jun 2026</div>
                                <div class="flex items-center"><i class="fa-regular fa-clock w-5 text-primary"></i> 15:00 - 18:00 WIB</div>
                                <div class="flex items-center"><i class="fa-solid fa-location-dot w-5 text-primary"></i> Ruang Pemuda Lt.1, GKI Guntur</div>
                                <div class="flex items-center"><i class="fa-solid fa-microphone w-5 text-primary"></i> Pdt. Samuel Krispradipta</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 border-t border-accent bg-slate-50/50" x-data="{ registered: false }">
                        <template x-if="!registered">
                            <button @click="
                                @if(auth()->guest())
                                    $dispatch('open-auth-modal', { view: 'login' });
                                    showNotification('Silakan Sign In untuk mendaftar kegiatan!', 'error');
                                @else
                                    registered = true;
                                    showNotification('Berhasil! Anda terdaftar pada kegiatan: Seminar: Kasih Aba-Aba (KF07)', 'success');
                                @endif
                            " class="w-full text-primary dark:text-white bg-brandlight dark:bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 hover:text-white py-2.5 rounded-xl font-bold text-sm transition shadow-soft flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-user-plus text-xs"></i> Gabung Sekarang
                            </button>
                        </template>
                        <template x-if="registered">
                            <div class="w-full bg-emerald-50 border border-emerald-200 text-emerald-600 py-2.5 rounded-xl font-bold text-sm transition flex items-center justify-center gap-1.5 cursor-default">
                                <i class="fa-solid fa-check mr-1"></i> Anda Telah Terdaftar
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Event 3 (Fellowship) -->
                <div x-show="currentFilter === 'all' || currentFilter === 'Fellowship'" class="bg-surface rounded-3xl shadow-soft border border-accent overflow-hidden flex flex-col justify-between group hover:shadow-card hover:-translate-y-1 transition duration-300">
                    <div>
                        <div class="h-48 relative overflow-hidden">
                            <img src="https://images.unsplash.com/photo-1714252562758-a30752d9d098?q=80&w=1273&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="Fellowship">
                            <span class="absolute top-4 left-4 bg-violet-500 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full shadow-sm">Fellowship</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-heading font-bold text-lg text-text mb-3">Fellowship: Now You See Me</h3>
                            <div class="space-y-2 text-xs text-textlight mb-4">
                                <div class="flex items-center"><i class="fa-regular fa-calendar w-5 text-primary"></i> 20 Jun 2026</div>
                                <div class="flex items-center"><i class="fa-regular fa-clock w-5 text-primary"></i> 15:00 - 18:00 WIB</div>
                                <div class="flex items-center"><i class="fa-solid fa-location-dot w-5 text-primary"></i> Ruang Pemuda Lt. 1, GKI Guntur</div>
                                <div class="flex items-center"><i class="fa-solid fa-microphone w-5 text-primary"></i> Pdt. Samuel Krispradipta</div>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 border-t border-accent bg-slate-50/50" x-data="{ registered: false }">
                        <template x-if="!registered">
                            <button @click="
                                @if(auth()->guest())
                                    $dispatch('open-auth-modal', { view: 'login' });
                                    showNotification('Silakan Sign In untuk mendaftar kegiatan!', 'error');
                                @else
                                    registered = true;
                                    showNotification('Berhasil! Anda terdaftar pada kegiatan: Fellowship: Now You See Me (KF07)', 'success');
                                @endif
                            " class="w-full text-primary dark:text-white bg-brandlight dark:bg-primary hover:bg-primary/90 dark:hover:bg-primary/80 hover:text-white py-2.5 rounded-xl font-bold text-sm transition shadow-soft flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-user-plus text-xs"></i> Gabung Sekarang
                            </button>
                        </template>
                        <template x-if="registered">
                            <div class="w-full bg-emerald-50 border border-emerald-200 text-emerald-600 py-2.5 rounded-xl font-bold text-sm transition flex items-center justify-center gap-1.5 cursor-default">
                                <i class="fa-solid fa-check mr-1"></i> Anda Telah Terdaftar
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Database Mapping Info Box -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 dark:border-blue-500/50 p-5 rounded-2xl flex gap-3">
                <i class="fa-solid fa-circle-nodes text-blue-500 dark:text-blue-400 text-xl mt-0.5 flex-shrink-0"></i>
                <div>
                    <h4 class="font-bold text-blue-800 dark:text-blue-300 text-sm mb-1">Struktur Relasi Tabel & Pendaftaran (KF07 & KF11)</h4>
                    <p class="text-xs text-blue-700 dark:text-blue-200/80 leading-relaxed">
                        Filter kategori di atas memisahkan baris data berdasarkan relasi sub-jenis ke tabel anak <code class="bg-blue-200/50 dark:bg-blue-900/50 dark:text-blue-200 px-1 rounded font-bold">persekutuan</code> dan <code class="bg-blue-200/50 dark:bg-blue-900/50 dark:text-blue-200 px-1 rounded font-bold">lainnya</code> dari tabel utama <code class="bg-blue-200/50 dark:bg-blue-900/50 dark:text-blue-200 px-1 rounded font-bold">kegiatan</code>. Tombol "Gabung Sekarang" menyimulasikan insert transaksi pendaftaran ke tabel <code class="bg-blue-200/50 dark:bg-blue-900/50 dark:text-blue-200 px-1 rounded font-bold">pendaftaran</code> menggunakan email akun yang sedang aktif.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
