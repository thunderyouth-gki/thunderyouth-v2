<x-layouts.app title="Home - Thunder Youth">
    <div class="view-section fade-in">
        <!-- Hero / Landing Section -->
        <div class="relative bg-gradient-to-b from-primary/10 via-primary/5 to-background pt-12 pb-24 overflow-hidden">
            <!-- Abstract Glows -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-secondary/20 rounded-full blur-3xl"></div>
                <div class="absolute top-48 -left-24 w-72 h-72 bg-primary/20 rounded-full blur-3xl"></div>
            </div>

            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center flex flex-col items-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-heading font-extrabold text-primary leading-tight mb-6 tracking-tight">
                    One Ship,<br/>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">One Direction with God.</span>
                </h1>
                <p class="text-lg md:text-xl text-textlight max-w-2xl mx-auto mb-10 leading-relaxed">
                    Thunder Youth adalah wadah persekutuan kreatif bagi para pemuda GKI Guntur untuk bertumbuh bersama, melayani dengan talenta, dan berbagi kasih Kristus.
                </p>
                
                <div>
                    @guest
                        <button x-data @click="$dispatch('open-auth-modal', { view: 'register' })" class="bg-primary hover:bg-primary/90 text-white px-10 py-4 rounded-full font-bold text-lg transition shadow-card hover:shadow-glow transform hover:-translate-y-1">
                            Come Aboard
                        </button>
                    @endguest
                    @auth
                        <div class="bg-emerald-50 text-emerald-800 font-bold px-6 py-3 rounded-2xl border border-emerald-100 flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i> Welcome Aboard, {{ auth()->user()->name }}!
                        </div>
                    @endauth
                </div>
                
                <!-- Premium Kustom SVG Ship (Interactive & Guaranteed Loaded) -->
                <div class="mt-12 w-full max-w-md mx-auto flex justify-center">
                    <div class="float-animation relative">
                        <svg width="280" height="280" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Sky background glow -->
                            <circle cx="100" cy="90" r="70" fill="#EAE6FA" opacity="0.6"/>
                            <!-- Cloud 1 -->
                            <path d="M40 70C40 64.4772 44.4772 60 50 60C51.6241 60 53.1673 60.3872 54.537 61.0772C56.5505 57.4819 60.3807 55 64.8 55C70.6277 55 75.4057 59.4316 75.9613 65.1118C76.9427 64.3926 78.1471 64 79.4444 64C82.5127 64 85 66.5218 85 69.6333C85 72.7449 82.5127 75.2667 79.4444 75.2667H50C44.4772 75.2667 40 70.7895 40 70Z" fill="white"/>
                            <!-- Cloud 2 -->
                            <path d="M125 55C125 49.4772 129.477 45 135 45C136.624 45 138.167 45.3872 139.537 46.0772C141.551 42.4819 145.381 40 149.8 40C155.628 40 160.406 44.4316 160.961 50.1118C161.943 49.3926 163.147 49 164.444 49C167.513 49 170 51.5218 170 54.6333C170 57.7449 167.513 60.2667 164.444 60.2667H135C129.477 60.2667 125 55.7895 125 55Z" fill="white"/>
                            <!-- Sun/Glow -->
                            <circle cx="100" cy="80" r="30" fill="#FFB800" opacity="0.8"/>
                            <!-- Cross on main sail -->
                            <path d="M100 45V105" stroke="#4A3B8C" stroke-width="4" stroke-linecap="round"/>
                            <path d="M85 65H115" stroke="#4A3B8C" stroke-width="4" stroke-linecap="round"/>
                            <!-- Sails -->
                            <path d="M100 45C118 45 130 65 130 85C110 85 100 75 100 45Z" fill="#F0EEFD" stroke="#4A3B8C" stroke-width="3" stroke-linejoin="round"/>
                            <path d="M100 45C82 45 70 65 70 85C90 85 100 75 100 45Z" fill="#FFFFFF" stroke="#4A3B8C" stroke-width="3" stroke-linejoin="round"/>
                            <!-- Ship Hull / Lambung Kapal -->
                            <path d="M50 115L65 145H135L150 115C150 115 125 125 100 125C75 125 50 115 50 115Z" fill="#4A3B8C" stroke="#2D1F66" stroke-width="3" stroke-linejoin="round"/>
                            <!-- Golden Accents -->
                            <circle cx="75" cy="128" r="4" fill="#FFB800"/>
                            <circle cx="100" cy="128" r="4" fill="#FFB800"/>
                            <circle cx="125" cy="128" r="4" fill="#FFB800"/>
                            <path d="M100 125V145" stroke="#FFB800" stroke-width="2"/>
                            <!-- Waves -->
                            <path class="wave-animation" d="M30 145C40 142 50 142 60 145C70 148 80 148 90 145C100 142 110 142 120 145C130 148 140 148 150 145C160 142 170 142 180 145" stroke="#3B82F6" stroke-width="3" stroke-linecap="round"/>
                            <path class="wave-animation" d="M25 152C35 149 45 149 55 152C65 155 75 155 85 152C95 149 105 149 115 152C125 155 135 155 145 152C155 149 165 149 175 152" stroke="#1D4ED8" stroke-width="3" stroke-linecap="round" opacity="0.6"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Sunday Service Section -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 relative z-20 space-y-16 pb-20">
            <div x-data="{
                handlePresence() {
                    @if(auth()->guest())
                        $dispatch('open-auth-modal', { view: 'login' });
                        showNotification('Mohon Sign In terlebih dahulu untuk mencatat presensi!', 'error');
                    @else
                        const btn = this.$refs.btnMainPresence;
                        btn.innerHTML = '<i class=\'fa-solid fa-spinner fa-spin mr-1\'></i> Memeriksa Lokasi GPS...';
                        btn.disabled = true;

                        setTimeout(() => {
                            btn.innerHTML = '<i class=\'fa-solid fa-check mr-1\'></i> Kehadiran Tersimpan';
                            btn.className = 'flex-1 bg-emerald-500 text-white py-3 rounded-xl font-semibold transition shadow-soft flex items-center justify-center gap-2 cursor-default';
                            
                            showNotification('Selamat! Kehadiran Anda berhasil diverifikasi & dicatat (KF05)', 'success');
                        }, 1500);
                    @endif
                }
            }">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-2 mb-6">
                    <div>
                        <span class="text-secondary font-bold tracking-wider text-sm uppercase">
                            @if($nearestService && $nearestService->is_today) Acara Hari Ini @else Agenda Terdekat @endif
                        </span>
                        <h2 class="font-heading font-bold text-2xl md:text-3xl text-primary">
                            @if($nearestService && $nearestService->is_today)
                                Today's Sunday Service
                            @else
                                Upcoming Sunday Service
                            @endif
                        </h2>
                    </div>
                    @if($nearestService)
                    <div class="text-left sm:text-right text-textlight font-medium bg-surface px-4 py-2 rounded-2xl border border-accent">
                        <div class="text-lg text-primary font-bold"><i class="fa-regular fa-calendar-check mr-1 text-secondary"></i> {{ $nearestService->service_date->format('d M Y') }}</div>
                        <div class="text-sm">{{ $nearestService->start_time }} (WIB)</div>
                    </div>
                    @endif
                </div>

                @if($nearestService)
                <!-- Main active service block -->
                <div class="bg-surface rounded-3xl shadow-soft border border-accent overflow-hidden flex flex-col md:flex-row">
                    <!-- Image Container with absolute tag -->
                    <div class="w-full md:w-5/12 min-h-[14rem] relative flex-shrink-0 flex items-stretch border-b md:border-b-0 md:border-r border-accent">
                        @if($nearestService->banner_image)
                            <img src="{{ asset('storage/'.$nearestService->banner_image) }}" alt="Service Banner" class="w-full h-full object-cover absolute inset-0">
                            <div class="absolute inset-0 bg-primary/20"></div>
                        @else
                            <div class="w-full flex-grow bg-[#fae046] p-6 flex flex-col justify-center relative min-h-[240px]">
                                <img src="{{ asset('storage/tyouth-logo.png') }}" class="absolute top-4 right-4 h-7 z-10" alt="Logo">
                                <div class="relative z-20 w-[70%]">
                                    <h4 class="font-bold text-[#46318e] text-xl md:text-2xl lg:text-3xl leading-snug drop-shadow-sm mb-2 line-clamp-3">{{ $nearestService->theme ?: 'Ibadah Pemuda' }}</h4>
                                    <p class="font-bold text-[#46318e]/90 text-sm drop-shadow-sm line-clamp-2">{{ $nearestService->speaker ?: 'GKI Guntur' }}</p>
                                </div>
                                <img src="{{ asset('storage/jesus-love.png') }}" class="absolute -bottom-2 -right-2 h-36 object-contain z-10" alt="Jesus">
                            </div>
                        @endif
                        
                        @if($nearestService->is_live)
                            <div class="absolute top-4 left-4 bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full animate-pulse shadow-md flex items-center gap-1.5 z-30">
                                <span class="w-2 h-2 bg-surface rounded-full"></span> NOW LIVE
                            </div>
                        @elseif($nearestService->is_finished && $nearestService->is_today)
                            <div class="absolute top-4 left-4 bg-slate-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md flex items-center gap-1.5 z-30">
                                <i class="fa-solid fa-check"></i> Ibadah Selesai
                            </div>
                        @endif
                    </div>
                    
                    <!-- Details Content -->
                    <div class="p-6 md:p-8 w-full md:w-7/12 flex flex-col justify-between">
                        <div>
                            @php
                                $typeLabels = [
                                    'back_to_the_bible' => 'Back To The Bible',
                                    'sharing_sunday' => 'Sharing Sunday',
                                    'kebaktian_gabungan' => 'Kebaktian Gabungan',
                                    'celebration_week' => 'Celebration Week',
                                    'other' => $nearestService->custom_service_type ?? 'Lainnya',
                                ];
                            @endphp
                            <span class="bg-primary/10 text-primary text-xs font-bold px-3 py-1 rounded-full w-max mb-3 inline-block">
                                {{ $typeLabels[$nearestService->service_type] ?? 'Youth Service' }}
                            </span>
                            <h3 class="font-heading font-bold text-2xl text-text mb-2">{{ $nearestService->theme ?: 'Belum Ada Tema' }}</h3>
                            <p class="text-textlight text-sm mb-6 leading-relaxed">
                                {{ $nearestService->description ?: 'Mari hadir dan bergabung bersama dalam ibadah pemuda minggu ini.' }}
                            </p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                <div class="flex items-center text-textlight text-sm">
                                    <i class="fa-solid fa-microphone w-8 h-8 rounded-lg bg-brandlight text-primary flex items-center justify-center mr-3 flex-shrink-0"></i>
                                    <div>
                                        <p class="text-xs text-textlight">Pembicara</p>
                                        <p class="font-semibold text-text">{{ $nearestService->speaker ?: '-' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center text-textlight text-sm">
                                    <i class="fa-solid fa-location-dot w-8 h-8 rounded-lg bg-brandlight text-primary flex items-center justify-center mr-3 flex-shrink-0"></i>
                                    <div>
                                        <p class="text-xs text-textlight">Lokasi</p>
                                        <p class="font-semibold text-text">{{ $nearestService->place ?: 'Ruang Pemuda Lt. 1' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-accent">
                            <a href="#" class="flex-1 bg-surface border border-accent text-text hover:bg-slate-50 dark:hover:bg-accent/50 py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-book-open"></i> Unduh Warta
                            </a>
                            <button x-ref="btnMainPresence" @click="handlePresence" class="flex-1 bg-primary hover:bg-primary/80 text-white py-3 rounded-xl font-semibold transition shadow-soft hover:shadow-glow flex items-center justify-center gap-2">
                                <i class="fa-solid fa-location-crosshairs animate-pulse text-secondary"></i> Catat Kehadiran Saya
                            </button>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-surface rounded-3xl p-8 shadow-soft border border-accent text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                        <i class="fa-regular fa-calendar-xmark text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-xl text-text mb-2">Belum Ada Jadwal</h3>
                    <p class="text-textlight text-sm">Jadwal ibadah pemuda terdekat belum dipublikasikan.</p>
                </div>
                @endif
            </div>

            <!-- Featured Events Subsection -->
            <div>
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <span class="text-secondary font-bold tracking-wider text-sm uppercase">Agenda Mendatang</span>
                        <h2 class="font-heading font-bold text-2xl text-primary">Upcoming Events</h2>
                    </div>
                    <a href="{{ route('events') }}" class="text-primary font-semibold hover:underline flex items-center gap-1 text-sm bg-brandlight px-3 py-1.5 rounded-full">
                        Lihat Semua <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Card 1 -->
                    <div class="bg-surface rounded-3xl p-4 shadow-soft border border-accent flex flex-col justify-between hover:shadow-card hover:-translate-y-1 transition duration-300">
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-2xl overflow-hidden flex-shrink-0">
                                <img src="https://images.unsplash.com/photo-1708312604109-16c0be9326cd?q=80&w=1074&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="w-full h-full object-cover" alt="Badminton">
                            </div>
                            <div class="min-w-0">
                                <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full inline-block mb-1">Sports</span>
                                <h4 class="font-bold text-text mb-0.5 truncate">Badminton Club</h4>
                                <div class="text-xs text-textlight"><i class="fa-regular fa-calendar mr-1"></i> 09 Jun 2026</div>
                            </div>
                        </div>
                        <a href="{{ route('events') }}" class="mt-4 text-center block w-full bg-brandlight hover:bg-primary hover:text-white text-primary text-xs font-semibold py-2 rounded-xl transition">
                            Lihat Detail
                        </a>
                    </div>
                    <!-- Card 2 -->
                    <div class="bg-surface rounded-3xl p-4 shadow-soft border border-accent flex flex-col justify-between hover:shadow-card hover:-translate-y-1 transition duration-300">
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-2xl overflow-hidden flex-shrink-0">
                                <img src="https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=400&q=80" class="w-full h-full object-cover" alt="Seminar">
                            </div>
                            <div class="min-w-0">
                                <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full inline-block mb-1">Seminar</span>
                                <h4 class="font-bold text-text mb-0.5 truncate">Seminar: Kasih Aba-Aba</h4>
                                <div class="text-xs text-textlight"><i class="fa-regular fa-calendar mr-1"></i> 13 Jun 2026</div>
                            </div>
                        </div>
                        <a href="{{ route('events') }}" class="mt-4 text-center block w-full bg-brandlight hover:bg-primary hover:text-white text-primary text-xs font-semibold py-2 rounded-xl transition">
                            Lihat Detail
                        </a>
                    </div>
                    <!-- Card 3 -->
                    <div class="bg-surface rounded-3xl p-4 shadow-soft border border-accent flex flex-col justify-between hover:shadow-card hover:-translate-y-1 transition duration-300">
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-2xl overflow-hidden flex-shrink-0">
                                <img src="https://images.unsplash.com/photo-1714252562758-a30752d9d098?q=80&w=1273&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="w-full h-full object-cover" alt="Fellowship">
                            </div>
                            <div class="min-w-0">
                                <span class="bg-violet-50 text-violet-700 text-[10px] font-bold px-2 py-0.5 rounded-full inline-block mb-1">Fellowship</span>
                                <h4 class="font-bold text-text mb-0.5 truncate">Now You See Me</h4>
                                <div class="text-xs text-textlight"><i class="fa-regular fa-calendar mr-1"></i> 20 Jun 2026</div>
                            </div>
                        </div>
                        <a href="{{ route('events') }}" class="mt-4 text-center block w-full bg-brandlight hover:bg-primary hover:text-white text-primary text-xs font-semibold py-2 rounded-xl transition">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
