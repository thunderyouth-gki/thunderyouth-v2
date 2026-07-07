<x-layouts.app>
    <div class="max-w-md mx-auto mt-8">
        <flux:card>
            <div class="text-center">
                <div class="p-6 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800">
                    <flux:icon.check-circle class="w-16 h-16 text-emerald-500 mx-auto mb-4" />
                    <flux:heading size="xl" class="text-emerald-700 dark:text-emerald-400">Verifikasi Berhasil!</flux:heading>
                    <p class="text-md text-emerald-600 dark:text-emerald-500 mt-2">
                        Terima kasih, kehadiran Anda telah terverifikasi melalui <strong>{{ $method ?? 'Sistem' }}</strong>.
                    </p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-4">
                        Selamat beribadah di kebaktian hari ini!
                    </p>
                </div>

                <div class="mt-6">
                    <flux:button variant="primary" href="{{ route('dashboard') }}" class="w-full">Kembali ke Beranda</flux:button>
                </div>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
