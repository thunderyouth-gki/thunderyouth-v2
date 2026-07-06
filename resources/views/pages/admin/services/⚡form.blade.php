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
    
    public $editMode = [
        'general_info' => true,
        'liturgy_songs' => true,
        'duties' => true,
        'report' => true,
    ];

    public $form = [
        'service_date' => '',
        'service_type' => '',
        'custom_service_type' => '',
        'theme' => '',
        'description' => '',
        'speaker' => '',
        'elder' => '',
        'bible_reading' => '',
        'start_time' => '09:30',
        'end_time' => '',
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
            
            $defaultForm = $this->form;
            $this->form = array_merge($this->form, $service->toArray());
            
            // Format date for input type="date"
            if ($service->service_date) {
                $this->form['service_date'] = $service->service_date->format('Y-m-d');
            }
            
            // Ensure JSON structures are initialized even if null in DB
            $this->form['liturgy_verses'] = $service->liturgy_verses ?? $defaultForm['liturgy_verses'];
            $this->form['liturgy_songs'] = $service->liturgy_songs ?? $defaultForm['liturgy_songs'];
            $this->form['duties'] = $service->duties ?? $defaultForm['duties'];
            
            // Set all sections to false (not in edit mode) if editing an existing record
            $this->editMode = [
                'general_info' => false,
                'liturgy_songs' => false,
                'duties' => false,
                'report' => false,
            ];
        } else {
            // Default to next available Sunday if new
            $date = Carbon::parse('next sunday');
            while (Service::whereDate('service_date', $date->toDateString())->exists()) {
                $date->addWeek();
            }
            $this->form['service_date'] = $date->format('Y-m-d');
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

    public function save($status = 'draft', $redirect = true)
    {
        $rules = [
            'form.service_date' => 'required|date|unique:services,service_date,' . ($this->service ? $this->service->id : 'NULL') . ',id',
            'form.service_type' => 'required|string',
            'form.custom_service_type' => 'required_if:form.service_type,other|nullable|string',
            'banner_image' => 'nullable|image|max:2048', // max 2MB
            'form.description' => 'nullable|string',
            'form.attendance_male' => 'nullable|integer|min:0',
            'form.attendance_female' => 'nullable|integer|min:0',
            'form.offering_amount' => 'nullable|numeric|min:0',
        ];

        if ($status === 'published') {
            $rules['form.theme'] = 'required|string|max:255';
            $rules['form.speaker'] = 'required|string|max:255';
            $rules['form.start_time'] = 'required|string|max:255';
            $rules['form.end_time'] = 'required|string|max:255';
            $rules['form.place'] = 'required|string|max:255';
        } else {
            $rules['form.theme'] = 'nullable|string|max:255';
            $rules['form.speaker'] = 'nullable|string|max:255';
            $rules['form.start_time'] = 'nullable|string|max:255';
            $rules['form.end_time'] = 'nullable|string|max:255';
            $rules['form.place'] = 'nullable|string|max:255';
        }

        $this->validate($rules, [
            'form.service_date.unique' => 'Sudah ada jadwal kebaktian di tanggal ini. Mohon pilih tanggal lain.',
            'form.theme.required' => 'Tema ibadah harus diisi jika jadwal berstatus dipublikasikan.',
            'form.speaker.required' => 'Nama pembicara harus diisi jika jadwal berstatus dipublikasikan.',
            'form.start_time.required' => 'Waktu mulai harus diisi jika jadwal berstatus dipublikasikan.',
            'form.end_time.required' => 'Waktu selesai harus diisi jika jadwal berstatus dipublikasikan.',
            'form.place.required' => 'Tempat ibadah harus diisi jika jadwal berstatus dipublikasikan.',
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
            $this->service = Service::create($this->form);
        }

        if ($redirect) {
            return redirect()->route('admin.services.index');
        }
    }

    public function toggleEdit($section)
    {
        if ($this->editMode[$section]) {
            // We are turning edit mode OFF, which means we want to SAVE
            $this->save($this->form['status'], false);
        }
        
        $this->editMode[$section] = !$this->editMode[$section];
    }
    
    public function canBePublished()
    {
        $required = [
            $this->form['theme'],
            $this->form['service_type'],
            $this->form['service_date'],
            $this->form['speaker'],
            $this->form['start_time'],
            $this->form['end_time'],
            $this->form['place'],
        ];
        
        foreach ($required as $field) {
            if (empty(trim($field))) {
                return false;
            }
        }
        
        return true;
    }
    
    public function deleteService()
    {
        if ($this->service && $this->service->exists) {
            if ($this->service->banner_image) {
                Storage::disk('public')->delete($this->service->banner_image);
            }
            $this->service->delete();
            return redirect()->route('admin.services.index');
        }
    }
};
?>

<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ $service ? 'Detail' : 'Buat' }} Jadwal Kebaktian</h1>
            <p class="text-zinc-500 dark:text-zinc-400">Isi detail jadwal, petugas, dan liturgi ibadah pemuda.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(!$service)
                <flux:button href="{{ route('admin.services.index') }}" class="bg-red-600 hover:bg-red-700 text-white border border-red-700">Batal</flux:button>
                <flux:button wire:click="save('draft')" variant="primary" class="bg-blue-600 hover:bg-blue-700 text-white border border-blue-700">Simpan</flux:button>
            @else
                <flux:button href="{{ route('admin.services.index') }}" variant="outline">Batal</flux:button>
                
                <flux:modal.trigger name="delete-service">
                    <flux:button variant="danger" class="border border-red-700">Hapus Jadwal</flux:button>
                </flux:modal.trigger>
                
                @if($service->status === 'published')
                    <flux:button 
                        wire:click="save('draft')" 
                        wire:confirm="Yakin ingin menyembunyikan jadwal ini? Jadwal akan ditarik dari halaman publik."
                        variant="primary"
                        class="!bg-amber-500 hover:!bg-amber-600 !text-white dark:!bg-amber-600 dark:hover:!bg-amber-700 !border-amber-600"
                    >
                        Sembunyikan
                    </flux:button>
                @else
                    <span class="{{ !$this->canBePublished() ? 'cursor-not-allowed inline-block' : '' }}">
                        <flux:button 
                            wire:click="save('published')" 
                            wire:confirm="Yakin ingin mempublikasikan jadwal ini? Jadwal akan tampil di halaman publik."
                            variant="primary"
                            class="!bg-emerald-600 hover:!bg-emerald-700 !text-white dark:!bg-emerald-500 dark:hover:!bg-emerald-600 !border-emerald-700 {{ !$this->canBePublished() ? '!opacity-20 pointer-events-none' : '' }}"
                            :disabled="!$this->canBePublished()"
                        >
                            Publikasikan
                        </flux:button>
                    </span>
                @endif
            @endif
        </div>
    </div>

    <form wire:submit.prevent="save('{{ $form['status'] }}')" class="space-y-8">
        
        <!-- Informasi Umum -->
        <flux:card>
            <div class="flex justify-between items-center mb-4 border-b border-zinc-200 dark:border-zinc-700 pb-2">
                <flux:heading size="lg" class="!font-bold">Informasi Umum</flux:heading>
                @if($service)
                    <flux:button size="sm" variant="subtle" icon="{{ $editMode['general_info'] ? 'document-check' : 'pencil-square' }}" class="!text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-500/10" wire:click="toggleEdit('general_info')" />
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Tanggal Ibadah <span class="text-red-500 cursor-help" title="Harus diisi agar jadwal dapat dipublikasikan">*</span></flux:label>
                    <flux:input type="date" wire:model="form.service_date" required :disabled="!$editMode['general_info']" />
                </flux:field>

                <!-- Banner Upload -->
                <div class="col-span-1 sm:col-span-2">
                    <flux:input type="file" wire:model="banner_image" label="Banner / Publikasi Ibadah" accept="image/*" description="Format JPG, PNG, atau WEBP maksimal 2MB." :disabled="!$editMode['general_info']" />
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
                    <flux:field>
                        <flux:label>Tipe Ibadah <span class="text-red-500 cursor-help" title="Harus diisi agar jadwal dapat dipublikasikan">*</span></flux:label>
                        <flux:select wire:model.live="form.service_type" placeholder="Pilih tipe kebaktian..." required :disabled="!$editMode['general_info']">
                            <flux:select.option value="back_to_the_bible">Back To The Bible (Minggu 1)</flux:select.option>
                            <flux:select.option value="sharing_sunday">Sharing Sunday (Minggu 2)</flux:select.option>
                            <flux:select.option value="kebaktian_gabungan">Kebaktian Gabungan (Minggu 3)</flux:select.option>
                            <flux:select.option value="celebration_week">Celebration Week (Minggu 4)</flux:select.option>
                            <flux:select.option value="other">Lainnya...</flux:select.option>
                        </flux:select>
                    </flux:field>
                    
                    @if($form['service_type'] === 'other')
                        <flux:input wire:model="form.custom_service_type" placeholder="Masukkan tipe ibadah..." required :disabled="!$editMode['general_info']" />
                    @endif
                </div>

                <flux:field>
                    <flux:label>Tema Ibadah <span class="text-red-500 cursor-help" title="Harus diisi agar jadwal dapat dipublikasikan">*</span></flux:label>
                    <flux:input wire:model="form.theme" placeholder="Contoh: Tetap Berdiri Teguh" :disabled="!$editMode['general_info']" />
                </flux:field>
                <flux:input wire:model="form.bible_reading" label="Bacaan Alkitab" placeholder="Contoh: Matius 13:31-33" :disabled="!$editMode['general_info']" />
                
                <div class="col-span-1 md:col-span-2">
                    <flux:textarea wire:model="form.description" label="Deskripsi Kebaktian" placeholder="Tambahkan deskripsi atau ringkasan terkait ibadah ini..." :disabled="!$editMode['general_info']" rows="3" />
                </div>
                
                <flux:field>
                    <flux:label>Pembicara / Pengkhotbah <span class="text-red-500 cursor-help" title="Harus diisi agar jadwal dapat dipublikasikan">*</span></flux:label>
                    <flux:input wire:model="form.speaker" placeholder="Nama pengkhotbah..." :disabled="!$editMode['general_info']" />
                </flux:field>
                <flux:input wire:model="form.elder" label="Penatua" placeholder="Nama penatua..." :disabled="!$editMode['general_info']" />
                
                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Waktu Mulai <span class="text-red-500 cursor-help" title="Harus diisi agar jadwal dapat dipublikasikan">*</span></flux:label>
                        <flux:input wire:model="form.start_time" type="time" :disabled="!$editMode['general_info']" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Waktu Selesai <span class="text-red-500 cursor-help" title="Harus diisi agar jadwal dapat dipublikasikan">*</span></flux:label>
                        <flux:input wire:model="form.end_time" type="time" :disabled="!$editMode['general_info']" />
                    </flux:field>
                </div>
                <flux:field>
                    <flux:label>Tempat <span class="text-red-500 cursor-help" title="Harus diisi agar jadwal dapat dipublikasikan">*</span></flux:label>
                    <flux:input wire:model="form.place" :disabled="!$editMode['general_info']" />
                </flux:field>
            </div>
        </flux:card>

        @if($service)
        <!-- Liturgi & Lagu -->
        <flux:card>
            <div class="flex justify-between items-center mb-4 border-b border-zinc-200 dark:border-zinc-700 pb-2">
                <flux:heading size="lg" class="!font-bold">Liturgi & Lagu</flux:heading>
                <flux:button size="sm" variant="subtle" icon="{{ $editMode['liturgy_songs'] ? 'document-check' : 'pencil-square' }}" class="!text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-500/10" wire:click="toggleEdit('liturgy_songs')" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Ayat-ayat Liturgi -->
                <div class="space-y-4">
                    <h3 class="font-medium text-sm text-zinc-800 dark:text-zinc-200 border-b pb-2">Ayat-ayat Liturgi</h3>
                    @foreach($form['liturgy_verses'] as $key => $value)
                        <flux:input wire:model="form.liturgy_verses.{{ $key }}" label="{{ $key }}" placeholder="Referensi ayat..." :disabled="!$editMode['liturgy_songs']" />
                    @endforeach
                </div>
                
                <!-- Lagu-lagu Liturgi -->
                <div class="space-y-4">
                    <h3 class="font-medium text-sm text-zinc-800 dark:text-zinc-200 border-b pb-2">Lagu-lagu Liturgi</h3>
                    @foreach($form['liturgy_songs'] as $key => $value)
                        <flux:input wire:model="form.liturgy_songs.{{ $key }}" label="{{ $key }}" placeholder="Judul lagu..." :disabled="!$editMode['liturgy_songs']" />
                    @endforeach
                </div>
            </div>
        </flux:card>

        <!-- Petugas (Duties) -->
        <flux:card>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4 border-b border-zinc-200 dark:border-zinc-700 pb-2">
                <flux:heading size="lg" class="!font-bold">Petugas Ibadah (Duties)</flux:heading>
                <flux:button size="sm" variant="subtle" icon="{{ $editMode['duties'] ? 'document-check' : 'pencil-square' }}" class="!text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-500/10" wire:click="toggleEdit('duties')" />
            </div>
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                
                <!-- Fitur Load Template -->
                <div class="flex gap-2 items-center w-full sm:w-auto">
                    <flux:select wire:model="selectedGroup" placeholder="Pilih Grup" size="sm" class="w-full sm:w-48" :disabled="!$editMode['duties']">
                        @foreach($groups as $group)
                            <flux:select.option value="{{ $group->id }}">{{ $group->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:button wire:click="loadGroup" size="sm" variant="outline" :disabled="!$editMode['duties']">Load Petugas</flux:button>
                </div>
            </div>
            
            <p class="text-sm text-zinc-500 mb-6">Anda dapat menggunakan fitur "Load Petugas" di atas untuk mengisi otomatis nama-nama petugas sesuai jadwal grup, lalu Anda tetap bisa mengubah nama-nama tersebut di bawah jika ada petugas yang diganti.</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($form['duties'] as $role => $value)
                    <flux:input wire:model="form.duties.{{ $role }}" label="{{ $role }}" placeholder="Nama petugas..." :disabled="!$editMode['duties']" />
                @endforeach
            </div>
        </flux:card>

        <!-- Post-Service Metrics -->
        <flux:card>
            <div class="flex justify-between items-center mb-4 border-b border-zinc-200 dark:border-zinc-700 pb-2">
                <flux:heading size="lg" class="!font-bold">Laporan Kehadiran & Persembahan</flux:heading>
                <flux:button size="sm" variant="subtle" icon="{{ $editMode['report'] ? 'document-check' : 'pencil-square' }}" class="!text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-500/10" wire:click="toggleEdit('report')" />
            </div>
            <p class="text-sm text-zinc-500 mb-6">Bagian ini bisa diisi nanti setelah ibadah selesai dilaksanakan.</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <flux:input type="number" wire:model="form.attendance_male" label="Jumlah Jemaat Pria" placeholder="0" min="0" :disabled="!$editMode['report']" />
                <flux:input type="number" wire:model="form.attendance_female" label="Jumlah Jemaat Wanita" placeholder="0" min="0" :disabled="!$editMode['report']" />
                <flux:input type="number" step="1000" wire:model="form.offering_amount" label="Jumlah Persembahan (Rp)" placeholder="0" min="0" :disabled="!$editMode['report']" />
            </div>
        </flux:card>
        @endif

    </form>

    <!-- Delete Confirmation Modal -->
    <flux:modal name="delete-service" class="min-w-[22rem]">
        <form wire:submit.prevent="deleteService" class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Jadwal Kebaktian?</flux:heading>
                <flux:subheading>
                    <p>Apakah Anda yakin ingin menghapus jadwal kebaktian ini?</p>
                    <p>Tindakan ini tidak dapat dibatalkan.</p>
                </flux:subheading>
            </div>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger">Hapus</flux:button>
            </div>
        </form>
    </flux:modal>
</div>