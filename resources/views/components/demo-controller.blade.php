<!-- DEMO CONTROLLER (Sangat Membantu untuk Evaluasi Skenario) -->
<div x-data="{ 
        minimize: true,
        x: 16,
        y: window.innerHeight - 64,
        dragging: false,
        moved: false,
        startX: 0,
        startY: 0,
        startMouseX: 0,
        startMouseY: 0,
        
        startDrag(e) {
            this.dragging = true;
            this.moved = false;
            this.startMouseX = e.clientX || (e.touches && e.touches[0].clientX);
            this.startMouseY = e.clientY || (e.touches && e.touches[0].clientY);
            this.startX = this.x;
            this.startY = this.y;
        },
        doDrag(e) {
            if (!this.dragging) return;
            const currentMouseX = e.clientX || (e.touches && e.touches[0].clientX);
            const currentMouseY = e.clientY || (e.touches && e.touches[0].clientY);
            const dx = currentMouseX - this.startMouseX;
            const dy = currentMouseY - this.startMouseY;
            
            if (Math.abs(dx) > 3 || Math.abs(dy) > 3) {
                this.moved = true;
            }
            
            if (this.moved) {
                this.x = this.startX + dx;
                this.y = this.startY + dy;
                
                // Clamp during drag
                if (this.y < 96) this.y = 96; // 80px header + 16px margin
                if (this.y > window.innerHeight - 64) this.y = window.innerHeight - 64;
            }
        },
        endDrag() {
            if (!this.dragging) return;
            this.dragging = false;
            if (this.moved) {
                const screenWidth = window.innerWidth;
                if (this.x > screenWidth / 2) {
                    this.x = screenWidth - 80; 
                } else {
                    this.x = 16; 
                }
                
                if (this.y < 96) this.y = 96;
                if (this.y > window.innerHeight - 64) this.y = window.innerHeight - 64;
            }
        },
        handleClick() {
            if (!this.moved) {
                this.minimize = false;
            }
        },
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
    @mousemove.window="doDrag"
    @mouseup.window="endDrag"
    @touchmove.window="doDrag"
    @touchend.window="endDrag"
    :style="`left: ${x}px; top: ${y}px; transition: ${dragging ? 'none' : 'all 0.3s ease-out'}`"
    class="fixed z-45">
    
    <!-- Minimized Floating Button -->
    <button x-show="minimize" @mousedown="startDrag" @touchstart="startDrag" @click="handleClick" class="bg-primary hover:bg-primary/90 text-white w-12 h-12 rounded-full shadow-2xl flex items-center justify-center border-2 border-white/20 transition transform hover:scale-105 active:scale-95 cursor-move" style="display: none;">
        <i class="fa-solid fa-flask text-lg text-secondary animate-pulse"></i>
    </button>

    <!-- Full Panel Sandbox -->
    <div x-show="!minimize" 
         :class="{
             '-translate-x-[calc(100%-48px)]': x > window.innerWidth / 2,
             '-translate-y-[calc(100%-48px)]': y > window.innerHeight / 2
         }" 
         class="bg-surface/95 backdrop-blur-md rounded-2xl p-4 shadow-2xl border-2 border-primary/20 w-72 max-w-[calc(100vw-32px)] text-xs">
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
