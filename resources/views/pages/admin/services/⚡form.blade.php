<?php

use Livewire\Component;
use App\Models\Service;
use App\Models\DutyGroup;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithFileUploads;
    
    public ?Service $service = null;
    
    public $groups = [];
    public $selectedGroup = '';
    public $banner_image;

    public $form = [
        'service_date' => '',
        'service_type' => 'back_to_the_bible',
        'custom_service_type' => '',
        'theme' => '',
        'speaker' => '',
        'elder' => '',
        'bible_reading' => '',
        'start_time' => '09:30',
        'end_time' => '11:30',
        'place' => 'Ruang Remaja Pemuda Lt. 1',
        'status' => 'draft',
        'liturgy_verses' => [
            'Pembuka' => '',
            'Pengakuan Dosa' => '',
            'Berita Anugerah' => '',
            'Persembahan' => '',
            'Pengutusan' => '',
        ],
        'liturgy_songs' => [
            'Prosesi' => '',
            'Kata Pembuka' => '',
            'Pengakuan Dosa' => '',
            'Berita Anugerah' => '',
            'Persembahan' => '',
            'Pengutusan' => '',
            'Prosesi Keluar' => '',
        ],
        'duties' => [
            'WL 1' => '',
            'WL 2' => '',
            'Pianis' => '',
            'Gitaris/Bassis' => '',
            'Cajonis' => '',
            'MMSS' => '',
            'Usher' => '',
            'Kolektan' => '',
        ],
        'attendance_male' => null,
        'attendance_female' => null,
        'offering_amount' => null,
    ];

    public function mount(?Service $service = null)
    {
        $this->groups = DutyGroup::all();
        
        if ($service && $service->exists) {
            $this->service = $service;
            
            $this->form = array_merge($this->form, $service->toArray());
            
            // Format date for input type="date"
            if ($service->service_date) {
                $this->form['service_date'] = $service->service_date->format('Y-m-d');
            }
            
            // Ensure JSON structures are initialized even if null in DB
            $this->form['liturgy_verses'] = $service->liturgy_verses ?? $this->form['liturgy_verses'];
            $this->form['liturgy_songs'] = $service->liturgy_songs ?? $this->form['liturgy_songs'];
            $this->form['duties'] = $service->duties ?? $this->form['duties'];
        } else {
            // Default to next Sunday if new
            $this->form['service_date'] = Carbon::parse('next sunday')->format('Y-m-d');
        }
    }
    
    public function loadGroup()
    {
        if (!$this->selectedGroup) return;
        
        $group = DutyGroup::find($this->selectedGroup);
        if ($group && $group->composition) {
            // Only merge keys that exist in the form's duties to avoid arbitrary data
            foreach ($this->form['duties'] as $role => $value) {
                if (isset($group->composition[$role])) {
                    $this->form['duties'][$role] = $group->composition[$role];
                }
            }
        }
    }

    public function save($status = 'draft')
    {
        $this->validate([
            'form.service_date' => 'required|date',
            'form.service_type' => 'required|string',
            'form.custom_service_type' => 'required_if:form.service_type,other|nullable|string',
            'banner_image' => 'nullable|image|max:2048', // max 2MB
            'form.theme' => 'nullable|string|max:255',
            'form.speaker' => 'nullable|string|max:255',
            'form.start_time' => 'required|string|max:255',
            'form.end_time' => 'nullable|string|max:255',
            'form.place' => 'required|string|max:255',
            'form.attendance_male' => 'nullable|integer|min:0',
            'form.attendance_female' => 'nullable|integer|min:0',
            'form.offering_amount' => 'nullable|numeric|min:0',
        ]);
        
        $this->form['status'] = $status;
        
        if ($this->banner_image) {
            $path = $this->banner_image->store('services', 'public');
            
            if ($this->service && $this->service->banner_image) {
                Storage::disk('public')->delete($this->service->banner_image);
            }
            
            $this->form['banner_image'] = $path;
        }

        if ($this->service && $this->service->exists) {
            $this->service->update($this->form);
        } else {
            Service::create($this->form);
        }

        return redirect()->route('admin.services.index');
    }
};
?>

<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ $service ? 'Edit' : 'Buat' }} Jadwal Kebaktian</h1>
            <p class="text-zinc-500 dark:text-zinc-400">Isi detail jadwal, petugas, dan liturgi ibadah pemuda.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <flux:button href="{{ route('admin.services.index') }}" variant="ghost" color="danger">Batal</flux:button>
            <flux:button wire:click="save('draft')" variant="outline" class="text-orange-600 hover:bg-orange-50 dark:text-orange-400 dark:hover:bg-orange-500/10 border-orange-200 dark:border-orange-500/30">Simpan sebagai Draft</flux:button>
            <flux:button wire:click="save('published')" class="bg-emerald-600 hover:bg-emerald-700 text-white dark:bg-emerald-500 dark:hover:bg-emerald-600 border-none">Simpan & Publikasikan</flux:button>
        </div>
    </div>

    <form wire:submit.prevent="save('{{ $form['status'] }}')" class="space-y-8">
        
        <!-- Informasi Umum -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">Informasi Umum</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input type="date" wire:model="form.service_date" label="Tanggal Ibadah" required />

                <!-- Banner Upload -->
                <div class="col-span-1 sm:col-span-2">
                    <flux:input type="file" wire:model="banner_image" label="Banner / Publikasi Ibadah" accept="image/*" description="Format JPG, PNG, atau WEBP maksimal 2MB." />
                    @if ($banner_image)
                        <div class="mt-2 relative inline-block">
                            <img src="{{ $banner_image->temporaryUrl() }}" class="h-32 rounded-lg object-cover shadow-sm">
                        </div>
                    @elseif(isset($form['banner_image']) && $form['banner_image'])
                        <div class="mt-2 relative inline-block">
                            <img src="{{ asset('storage/' . $form['banner_image']) }}" class="h-32 rounded-lg object-cover shadow-sm">
                        </div>
                    @endif
                </div>

                <div class="space-y-3">
                    <flux:select wire:model.live="form.service_type" label="Tipe Ibadah" required>
                        <flux:select.option value="back_to_the_bible">Back To The Bible (Minggu 1)</flux:select.option>
                        <flux:select.option value="sharing_sunday">Sharing Sunday (Minggu 2)</flux:select.option>
                        <flux:select.option value="kebaktian_gabungan">Kebaktian Gabungan (Minggu 3)</flux:select.option>
                        <flux:select.option value="celebration_week">Celebration Week (Minggu 4)</flux:select.option>
                        <flux:select.option value="other">Lainnya...</flux:select.option>
                    </flux:select>
                    
                    @if($form['service_type'] === 'other')
                        <flux:input wire:model="form.custom_service_type" placeholder="Masukkan tipe ibadah..." required />
                    @endif
                </div>

                <flux:input wire:model="form.theme" label="Tema Ibadah" placeholder="Contoh: Tetap Berdiri Teguh" />
                <flux:input wire:model="form.bible_reading" label="Bacaan Alkitab Utama" placeholder="Contoh: Matius 13:31-33" />
                
                <flux:input wire:model="form.speaker" label="Pembicara / Pengkhotbah" placeholder="Nama pengkhotbah..." />
                <flux:input wire:model="form.elder" label="Penatua Bertugas" placeholder="Nama penatua..." />
                
                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="form.start_time" type="time" label="Waktu Mulai" required />
                    <flux:input wire:model="form.end_time" type="time" label="Waktu Selesai" />
                </div>
                <flux:input wire:model="form.place" label="Tempat" required />
            </div>
        </flux:card>

        <!-- Liturgi & Lagu -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">Liturgi & Lagu</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Ayat-ayat Liturgi -->
                <div class="space-y-4">
                    <h3 class="font-medium text-sm text-zinc-800 dark:text-zinc-200 border-b pb-2">Ayat-ayat Liturgi</h3>
                    @foreach($form['liturgy_verses'] as $key => $value)
                        <flux:input wire:model="form.liturgy_verses.{{ $key }}" label="{{ $key }}" placeholder="Referensi ayat..." />
                    @endforeach
                </div>
                
                <!-- Lagu-lagu Liturgi -->
                <div class="space-y-4">
                    <h3 class="font-medium text-sm text-zinc-800 dark:text-zinc-200 border-b pb-2">Lagu-lagu Liturgi</h3>
                    @foreach($form['liturgy_songs'] as $key => $value)
                        <flux:input wire:model="form.liturgy_songs.{{ $key }}" label="{{ $key }}" placeholder="Judul lagu..." />
                    @endforeach
                </div>
            </div>
        </flux:card>

        <!-- Petugas (Duties) -->
        <flux:card>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                <flux:heading size="lg">Petugas Ibadah (Duties)</flux:heading>
                
                <!-- Fitur Load Template -->
                <div class="flex gap-2 items-center w-full sm:w-auto">
                    <flux:select wire:model="selectedGroup" placeholder="Pilih Grup" size="sm" class="w-full sm:w-48">
                        @foreach($groups as $group)
                            <flux:select.option value="{{ $group->id }}">{{ $group->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:button wire:click="loadGroup" size="sm" variant="outline">Load Petugas</flux:button>
                </div>
            </div>
            
            <p class="text-sm text-zinc-500 mb-6">Anda dapat menggunakan fitur "Load Petugas" di atas untuk mengisi otomatis nama-nama petugas sesuai jadwal grup, lalu Anda tetap bisa mengubah nama-nama tersebut di bawah jika ada petugas yang diganti.</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($form['duties'] as $role => $value)
                    <flux:input wire:model="form.duties.{{ $role }}" label="{{ $role }}" placeholder="Nama petugas..." />
                @endforeach
            </div>
        </flux:card>

        <!-- Post-Service Metrics -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">Laporan Kehadiran & Persembahan</flux:heading>
            <p class="text-sm text-zinc-500 mb-6">Bagian ini bisa diisi nanti setelah ibadah selesai dilaksanakan.</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <flux:input type="number" wire:model="form.attendance_male" label="Jumlah Jemaat Pria" placeholder="0" min="0" />
                <flux:input type="number" wire:model="form.attendance_female" label="Jumlah Jemaat Wanita" placeholder="0" min="0" />
                <flux:input type="number" step="1000" wire:model="form.offering_amount" label="Jumlah Persembahan (Rp)" placeholder="0" min="0" />
            </div>
        </flux:card>

    </form>
</div>