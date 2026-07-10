<x-layouts.app title="Services - Thunder Youth">
    <div class="view-section fade-in">
        <!-- Header Services -->
        <div class="bg-primary pt-12 pb-24 px-4 sm:px-6 lg:px-8 text-white relative">
            <div class="absolute inset-0 bg-gradient-to-r from-primary to-purple-900 opacity-60"></div>
            <div class="max-w-6xl mx-auto relative z-10">
                <span class="bg-secondary text-primary text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">Jadwal Kebaktian</span>
                <h1 class="text-3xl md:text-4xl font-heading font-bold text-white mt-3 mb-4">Youth Services</h1>
                <p class="text-white/80 max-w-2xl text-sm md:text-base leading-relaxed">
                    Ibadah dan kebaktian rutin Komisi Pemuda GKI Guntur. Temukan kedamaian dan hadirat Tuhan bersama komunitas pemuda lainnya.
                </p>
            </div>
        </div>
        
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-20 space-y-12 pb-20">
            @auth
                @php
                    // Check if there is an active service right now
                    $activeService = \App\Models\Event::whereDate('event_date', today())->first();
                @endphp
                @if($activeService && $activeService->is_live)
                    <div class="mb-12">
                        <livewire:jemaat.mark-attendance :service="$activeService" />
                    </div>
                @endif
            @endauth

            <!-- Next Service Highlight -->
            @if($nearestService)
            <div class="bg-surface rounded-3xl shadow-card border border-accent overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="font-heading font-bold text-xl text-primary">Ibadah Pemuda Terdekat</h2>
                        @if($nearestService->is_live)
                            <span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full animate-pulse shadow-sm flex items-center gap-1.5">
                                <span class="w-2 h-2 bg-surface rounded-full"></span> NOW LIVE
                            </span>
                        @elseif($nearestService->is_finished && $nearestService->is_today)
                            <span class="bg-slate-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5">
                                <i class="fa-solid fa-check"></i> Selesai
                            </span>
                        @else
                            <span class="bg-red-50 text-red-600 text-xs font-semibold px-2.5 py-1 rounded-full"><i class="fa-solid fa-clock mr-1"></i> Mendatang</span>
                        @endif
                    </div>
                    <div class="flex flex-col md:flex-row gap-8 lg:gap-10">
                        <div class="w-full md:w-5/12 lg:w-4/12 min-h-[12rem] lg:min-h-[14rem] rounded-2xl overflow-hidden relative flex-shrink-0 flex items-stretch shadow-sm border border-black/5">
                            @if($nearestService->banner_image)
                                <img src="{{ asset('storage/'.$nearestService->banner_image) }}" class="w-full h-full object-cover absolute inset-0" alt="Service Banner">
                            @else
                                <div class="w-full flex-grow bg-[#fae046] p-5 flex flex-col justify-center relative min-h-[220px]">
                                    <img src="{{ asset('storage/tyouth-logo.png') }}" class="absolute top-3 right-3 sm:top-4 sm:right-4 h-6 sm:h-7" alt="Logo">
                                    <div class="relative z-10 w-[65%] sm:w-[70%]">
                                        <h4 class="font-bold text-[#46318e] text-lg sm:text-xl md:text-2xl leading-snug drop-shadow-sm mb-1 sm:mb-2 line-clamp-3">{{ $nearestService->theme ?: 'Ibadah Pemuda' }}</h4>
                                        <p class="font-bold text-[#46318e]/90 text-xs sm:text-sm drop-shadow-sm line-clamp-2">{{ $nearestService->speaker ?: 'GKI Guntur' }}</p>
                                    </div>
                                    <img src="{{ asset('storage/jesus-love.png') }}" class="absolute -bottom-2 -right-2 h-28 sm:h-32 object-contain" alt="Jesus">
                                </div>
                            @endif
                        </div>
                        <div class="w-full md:flex-1 flex flex-col justify-center py-2 md:py-4">
                            <div>
                                <span class="bg-primary/10 text-primary text-xs font-bold px-2.5 py-1 rounded-full inline-block mb-3">{{ $nearestService->eventType?->name ?? 'Youth Service' }}</span>
                                <h3 class="font-heading font-bold text-2xl text-text mb-2">{{ $nearestService->theme ?: 'Belum Ada Tema' }}</h3>
                                <p class="text-textlight text-sm mb-4 leading-relaxed">
                                    {{ $nearestService->description ?: 'Mari hadir dan bergabung bersama dalam ibadah pemuda minggu ini.' }}
                                </p>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4 border-t border-accent pt-4">
                                <div>
                                    <p class="text-xs text-textlight mb-1">Pembicara</p>
                                    <p class="text-sm font-semibold text-text">{{ $nearestService->speaker ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-textlight mb-1">Tanggal</p>
                                    <p class="text-sm font-semibold text-text">{{ $nearestService->event_date->format('d F Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-textlight mb-1">Waktu</p>
                                    <p class="text-sm font-semibold text-text">{{ $nearestService->start_time ? date('H:i', strtotime($nearestService->start_time)) : '-' }} {{ $nearestService->end_time ? ' - '.date('H:i', strtotime($nearestService->end_time)) : '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-surface rounded-3xl p-8 shadow-card border border-accent text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 text-slate-400 mb-4">
                    <i class="fa-regular fa-calendar-xmark text-2xl"></i>
                </div>
                <h3 class="font-bold text-xl text-text mb-2">Belum Ada Jadwal</h3>
                <p class="text-textlight text-sm">Jadwal ibadah pemuda terdekat belum dipublikasikan.</p>
            </div>
            @endif

            <!-- Past Services -->
            <div>
                <div class="flex items-center gap-2 mb-6">
                    <h2 class="font-heading font-bold text-2xl text-primary">Past Services (Arsip Kebaktian)</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse($pastServices as $service)
                    <div class="bg-surface rounded-3xl p-6 shadow-soft border border-accent flex flex-col justify-between hover:shadow-card transition">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <span class="bg-slate-100 text-slate-700 text-xs font-semibold px-2.5 py-1 rounded-full">Ibadah Selesai</span>
                                <span class="text-xs text-textlight font-medium">{{ $service->event_date->format('d M Y') }}</span>
                            </div>
                            <h3 class="font-bold text-lg text-text mb-2">{{ $service->theme ?: 'Ibadah Pemuda' }}</h3>
                            <p class="text-xs text-textlight mb-4 line-clamp-2">{{ $service->description ?: '-' }}</p>
                        </div>
                        <div class="border-t border-accent pt-4 mt-4 flex justify-between items-center text-xs">
                            <span class="font-medium text-primary">{{ $service->speaker ?: '-' }}</span>
                            @auth
                                <span class="bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-full font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-check text-xs"></i> Hadir
                                </span>
                            @else
                                <span class="text-textlight text-xs italic">Login untuk konfirmasi kehadiran</span>
                            @endauth
                        </div>
                    </div>
                    @empty
                    <div class="col-span-1 md:col-span-2 bg-surface rounded-3xl p-6 shadow-soft border border-accent text-center text-textlight py-10">
                        Belum ada kebaktian yang berlalu di bulan ini.
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="bg-primary/5 dark:bg-primary/10 border-l-4 border-primary p-5 rounded-2xl flex gap-3">
                <i class="fa-solid fa-database text-primary text-xl mt-0.5 flex-shrink-0"></i>
                <div>
                    <h4 class="font-bold text-primary dark:text-primary-light text-sm mb-1">Status Sinkronisasi Database (KF11)</h4>
                    <p class="text-xs text-primary/80 dark:text-primary-light/80 leading-relaxed">
                        Informasi keagamaan dan jadwal ibadah ini ditarik secara dinamis dari tabel <code class="bg-surface/60 dark:bg-surface/10 px-1 rounded font-bold">kebaktian</code> yang berelasi dengan entitas <code class="bg-surface/60 dark:bg-surface/10 px-1 rounded font-bold">kegiatan</code> di sistem PostgreSQL/MySQL Anda via Eloquent ORM di Laravel.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
