<div x-data="authModal()" 
     x-show="isOpen" 
     @open-auth-modal.window="openModal($event.detail.view)"
     class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center transition-opacity duration-300"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;">
     
    <div class="bg-surface w-full max-w-md mx-4 rounded-3xl shadow-glow overflow-hidden transform transition-transform duration-300 relative"
         @click.away="closeModal()"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="scale-95 opacity-0"
         x-transition:enter-end="scale-100 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="scale-100 opacity-100"
         x-transition:leave-end="scale-95 opacity-0">
        
        <!-- Close Button -->
        <button @click="closeModal()" class="absolute top-4 right-4 text-textlight hover:text-text bg-accent/20 hover:bg-accent/40 w-8 h-8 rounded-full flex items-center justify-center transition z-10">
            <i class="fa-solid fa-times text-sm"></i>
        </button>

        <!-- Login Card View -->
        <div x-show="view === 'login'" class="p-8">
            <div class="mb-6 text-center">
                <div class="w-14 h-14 bg-brandlight rounded-2xl flex items-center justify-center mx-auto mb-3 text-primary text-xl">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </div>
                <h2 class="text-2xl font-heading font-extrabold text-primary">Welcome Back</h2>
                <p class="text-xs text-textlight mt-1">Masuk untuk mencatat kehadiran & berpartisipasi</p>
            </div>
            
            <form class="space-y-4" @submit.prevent="submitLogin">
                <div x-show="errorMessage" class="text-red-500 text-xs font-bold text-center bg-red-50 p-2 rounded" x-text="errorMessage" style="display: none;"></div>
                
                <div>
                    <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">Email Jemaat</label>
                    <input type="email" x-model="loginForm.email" placeholder="you@gmail.com" required class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                </div>
                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">Password</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" x-model="loginForm.password" placeholder="••••••••" required class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm pr-10">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-textlight hover:text-primary transition focus:outline-none">
                            <i class="fa-regular fa-eye" x-show="!show"></i>
                            <i class="fa-regular fa-eye-slash" x-show="show" style="display: none;"></i>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-textlight hover:text-text select-none group">
                        <div class="relative flex items-center justify-center w-5 h-5 rounded border border-accent bg-background group-hover:border-primary transition">
                            <input type="checkbox" x-model="loginForm.remember" class="peer absolute opacity-0 w-full h-full cursor-pointer">
                            <i class="fa-solid fa-check text-[10px] text-primary opacity-0 peer-checked:opacity-100 transition"></i>
                        </div>
                        Ingat Saya
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-primary hover:underline">Lupa Password?</a>
                    @endif
                </div>
                <button type="submit" :disabled="isLoading" class="w-full bg-primary hover:bg-primary/90 text-white py-3 rounded-xl font-bold transition shadow-soft mt-2 text-sm flex justify-center items-center gap-2 disabled:opacity-70">
                    <span x-show="!isLoading">Sign In</span>
                    <i x-show="isLoading" class="fa-solid fa-spinner fa-spin"></i>
                </button>
            </form>
            
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-accent"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-2 bg-surface text-textlight">Atau login dengan</span>
                </div>
            </div>
            
            <button type="button" @click="loginWithPasskey" :disabled="isLoading" class="w-full bg-surface text-primary border-2 border-brandlight hover:bg-brandlight py-3 rounded-xl font-bold transition shadow-soft text-sm flex justify-center items-center gap-2 disabled:opacity-50">
                <i class="fa-solid fa-fingerprint text-lg"></i> <span x-text="isLoading ? 'Memproses...' : 'Sign in with Passkey'"></span>
            </button>
            
            
            <div class="mt-6 text-center text-xs text-textlight flex flex-col gap-2">
                <div>
                    Belum memiliki akun? <button @click="view = 'register'" class="text-primary font-bold hover:underline focus:outline-none">Daftarkan Sekarang</button>
                </div>
                <div class="pt-2 border-t border-accent mt-2">
                    Bukan anggota? <a href="{{ route('guest.attendance') }}" class="text-primary font-bold hover:underline">Klik Disini</a>
                </div>
            </div>
        </div>

        <!-- Register View -->
        <div x-show="view === 'register'" class="p-8 max-h-[85vh] overflow-y-auto no-scrollbar" style="display: none;">
            <div class="mb-5 text-center">
                <h2 class="text-2xl font-heading font-extrabold text-primary">Daftar Akun Baru</h2>
                <p class="text-xs text-textlight mt-1">Gabung dalam keluarga besar Komisi Pemuda GKI Guntur</p>
            </div>
            
            <form class="space-y-4" @submit.prevent="submitRegister">
                <div x-show="errorMessage" class="text-red-500 text-xs font-bold text-center bg-red-50 p-2 rounded" x-text="errorMessage" style="display: none;"></div>

                <div>
                    <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" x-model="registerForm.name" required class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                    <template x-if="errors.name"><span class="text-red-500 text-[10px]" x-text="errors.name[0]"></span></template>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">Alamat Email</label>
                    <input type="email" x-model="registerForm.email" placeholder="you@domain.com" required class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm">
                    <template x-if="errors.email"><span class="text-red-500 text-[10px]" x-text="errors.email[0]"></span></template>
                </div>
                
                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">Password</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" x-model="registerForm.password" required class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm pr-10">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-textlight hover:text-primary transition focus:outline-none">
                            <i class="fa-regular fa-eye" x-show="!show"></i>
                            <i class="fa-regular fa-eye-slash" x-show="show" style="display: none;"></i>
                        </button>
                    </div>
                    <template x-if="errors.password"><span class="text-red-500 text-[10px]" x-text="errors.password[0]"></span></template>
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-text mb-1 uppercase tracking-wider">Konfirmasi Password</label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" x-model="registerForm.password_confirmation" required class="w-full px-4 py-2.5 rounded-xl border border-accent bg-background focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition text-sm pr-10">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-textlight hover:text-primary transition focus:outline-none">
                            <i class="fa-regular fa-eye" x-show="!show"></i>
                            <i class="fa-regular fa-eye-slash" x-show="show" style="display: none;"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" :disabled="isLoading" class="w-full bg-primary hover:bg-primary/90 text-white py-3 rounded-xl font-bold transition shadow-soft mt-4 text-sm flex justify-center items-center gap-2 disabled:opacity-70">
                    <span x-show="!isLoading">Buat Akun Jemaat</span>
                    <i x-show="isLoading" class="fa-solid fa-spinner fa-spin"></i>
                </button>
            </form>
            
            <div class="mt-4 text-center text-xs text-textlight">
                Sudah punya akun? <button @click="view = 'login'" class="text-primary font-bold hover:underline focus:outline-none">Masuk Di Sini</button>
            </div>
        </div>
    </div>
</div>

<script>
    function authModal() {
        return {
            isOpen: false,
            view: 'login', // login or register
            isLoading: false,
            errorMessage: '',
            errors: {},
            loginForm: {
                email: '',
                password: '',
                remember: true
            },
            registerForm: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            },
            init() {
                if (window.location.search.includes('login=true') || window.location.search.includes('login=1')) {
                    this.view = 'login';
                    setTimeout(() => {
                        this.isOpen = true;
                    }, 50);
                }
            },
            openModal(viewName) {
                this.view = viewName || 'login';
                this.isOpen = true;
                this.errorMessage = '';
                this.errors = {};
            },
            closeModal() {
                this.isOpen = false;
            },
            async submitLogin() {
                this.isLoading = true;
                this.errorMessage = '';
                
                try {
                    // Make request to Fortify endpoint
                    const response = await fetch('/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.loginForm)
                    });

                    if (response.ok) {
                        const data = await response.clone().json().catch(() => null);
                        if (data && data.two_factor) {
                            window.location.href = '/two-factor-challenge';
                            return;
                        }
                        window.location.reload(); // Reload to update auth state globally
                    } else {
                        const data = await response.json();
                        this.errorMessage = data.message || 'Login failed. Please check your credentials.';
                    }
                } catch (error) {
                    this.errorMessage = 'An error occurred connecting to the server.';
                } finally {
                    this.isLoading = false;
                }
            },
            async submitRegister() {
                this.isLoading = true;
                this.errorMessage = '';
                this.errors = {};
                
                try {
                    const response = await fetch('/register', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(this.registerForm)
                    });

                    if (response.ok) {
                        window.location.reload(); 
                    } else {
                        const data = await response.json();
                        if (data.errors) {
                            this.errors = data.errors;
                            this.errorMessage = 'Please fix the errors below.';
                        } else {
                            this.errorMessage = data.message || 'Registration failed.';
                        }
                    }
                } catch (error) {
                    this.errorMessage = 'An error occurred connecting to the server.';
                } finally {
                    this.isLoading = false;
                }
            },
            async loginWithPasskey() {
                if (!window.Passkeys) {
                    this.errorMessage = 'Passkeys are not loaded yet. Please try again.';
                    return;
                }
                this.isLoading = true;
                this.errorMessage = '';
                try {
                    const response = await window.Passkeys.verify();
                    if (response && response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.reload();
                    }
                } catch (e) {
                    console.error('Passkey login error:', e);
                    this.errorMessage = e.message || 'Could not authenticate with Passkey.';
                } finally {
                    this.isLoading = false;
                }
            }
        }
    }
</script>
