<x-layouts.app title="Prayer Tree - Thunder Youth">
    <div class="view-section fade-in">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-20">
            <div class="text-center mb-10">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4 text-amber-600 text-2xl shadow-soft">
                    <i class="fa-solid fa-hands-praying"></i>
                </div>
                <h1 class="text-3xl font-heading font-extrabold text-primary mb-3">Prayer Tree</h1>
                <p class="text-textlight text-sm max-w-lg mx-auto">
                    "Sebab di mana dua atau tiga orang berkumpul dalam nama-Ku, di situ Aku ada di tengah-tengah mereka." - Matius 18:20
                </p>
            </div>

            <!-- Interactive Prayer Tree Post Form -->
            <div class="bg-surface rounded-3xl p-6 md:p-8 shadow-card border border-accent mb-8" x-data="{
                content: '',
                isAnonymous: false,
                submitPrayer() {
                    if (this.content.trim() === '') return;
                    
                    const senderName = this.isAnonymous ? 'Anonim' : '{{ auth()->check() ? auth()->user()->name : "Anonim" }}';
                    const type = this.isAnonymous ? 'Rahasia' : 'Publik';
                    
                    // Add to list event
                    $dispatch('prayer-added', { sender: senderName, content: this.content, type: type });
                    
                    this.content = '';
                    this.isAnonymous = false;
                    showNotification('Pokok doa berhasil digantung di pohon doa (KF08)', 'success');
                }
            }">
                <h2 class="font-bold text-lg text-text mb-4">Bagikan Pokok Doamu</h2>
                <form @submit.prevent="submitPrayer" class="space-y-4">
                    <div>
                        <textarea x-model="content" rows="4" required class="w-full p-4 border border-accent rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition resize-none bg-background text-sm" placeholder="Tulis pokok doamu di sini secara jujur... Kami akan bersama-sama mendukungmu dalam doa."></textarea>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <label class="flex items-center gap-2.5 cursor-pointer text-sm text-textlight hover:text-text select-none group">
                            <div class="relative flex items-center justify-center w-5 h-5 rounded border border-accent bg-background group-hover:border-primary transition">
                                <input type="checkbox" x-model="isAnonymous" class="peer absolute opacity-0 w-full h-full cursor-pointer">
                                <i class="fa-solid fa-check text-[10px] text-primary opacity-0 peer-checked:opacity-100 transition"></i>
                            </div>
                            Kirim secara Anonim (Sembunyikan Identitas)
                        </label>
                        <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full font-bold transition shadow-soft flex items-center justify-center gap-2 text-sm">
                            <i class="fa-solid fa-paper-plane text-xs"></i> Gantung Doa di Pohon
                        </button>
                    </div>
                </form>
            </div>

            <!-- Live Community Prayers list -->
            <div x-data="{
                prayers: [
                    { sender: 'Steven', content: 'Mohon doanya ujian sidang skripsi saya minggu depan lancar.', type: 'Publik' },
                    { sender: 'Anonim', content: 'Papa saya sedang masa penyembuhan pasca operasi jantung.', type: 'Rahasia' }
                ]
            }" @prayer-added.window="prayers.unshift($event.detail)">
                <h3 class="font-heading font-bold text-xl text-primary mb-4">Live Community Prayers</h3>
                <div class="space-y-4">
                    <template x-for="(prayer, index) in prayers" :key="index">
                        <div class="bg-surface rounded-2xl p-5 shadow-soft border border-accent">
                            <div class="flex justify-between items-start gap-4 mb-3">
                                <div>
                                    <h4 class="font-bold text-sm text-primary" x-text="prayer.sender"></h4>
                                    <p class="text-[10px] text-textlight">Kategori: Persekutuan & Saling Mendukung</p>
                                </div>
                                <span :class="prayer.type === 'Rahasia' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-purple-100 text-primary dark:bg-primary/20 dark:text-primary'" class="text-xs font-bold px-3 py-1 rounded-full" x-text="prayer.type"></span>
                            </div>
                            <p class="text-sm text-textlight leading-relaxed" x-text="prayer.content"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
