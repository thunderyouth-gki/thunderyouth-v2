<?php

use Livewire\Component;
use App\Models\Event;
use App\Models\EventType;
use App\Models\DutyGroup;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithFileUploads;
    
    public ?Event $event = null;
    public $eventTypes = [];
    
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
        'event_date' => '',
        'event_type_id' => 1,
        'service_type' => '',
        'custom_service_type' => '',
        'custom_event_type' => '',
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

    public function mount(?Event $event = null)
    {
        $this->groups = DutyGroup::all();
        $this->eventTypes = EventType::all();
        
        if ($event && $event->exists) {
            $this->event = $event;
            
            $defaultForm = $this->form;
            $this->form = array_merge($this->form, $event->toArray());
            
            // Format date for input type="date"
            if ($event->event_date) {
                $this->form['event_date'] = $event->event_date->format('Y-m-d');
            }
            
            // Ensure JSON structures are initialized even if null in DB
            $this->form['liturgy_verses'] = $event->liturgy_verses ?? $defaultForm['liturgy_verses'];
            $this->form['liturgy_songs'] = $event->liturgy_songs ?? $defaultForm['liturgy_songs'];
            $this->form['duties'] = $event->duties ?? $defaultForm['duties'];
            
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
            while (Event::whereDate('event_date', $date->toDateString())->exists()) {
                $date->addWeek();
            }
            $this->form['event_date'] = $date->format('Y-m-d');
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
            'form.event_date' => 'required|date|unique:events,event_date,' . ($this->event ? $this->event->id : 'NULL') . ',id',
            'form.event_type_id' => 'required|exists:event_types,id',
            'form.service_type' => 'required_if:form.event_type_id,1|nullable|string',
            'form.custom_service_type' => 'required_if:form.service_type,other|nullable|string',
            'form.custom_event_type' => 'required_if:form.event_type_id,4|nullable|string',
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
            $rules['form.end_time'] = 'required|string|max:255|after:form.start_time';
            $rules['form.place'] = 'required|string|max:255';
        } else {
            $rules['form.theme'] = 'nullable|string|max:255';
            $rules['form.speaker'] = 'nullable|string|max:255';
            $rules['form.start_time'] = 'nullable|string|max:255';
            $rules['form.end_time'] = 'nullable|string|max:255|after:form.start_time';
            $rules['form.place'] = 'nullable|string|max:255';
        }

        $this->validate($rules, [
            'form.event_date.unique' => 'Sudah ada jadwal kebaktian di tanggal ini. Mohon pilih tanggal lain.',
            'form.theme.required' => 'Tema ibadah harus diisi jika jadwal berstatus dipublikasikan.',
            'form.speaker.required' => 'Nama pembicara harus diisi jika jadwal berstatus dipublikasikan.',
            'form.start_time.required' => 'Waktu mulai harus diisi jika jadwal berstatus dipublikasikan.',
            'form.end_time.required' => 'Waktu selesai harus diisi jika jadwal berstatus dipublikasikan.',
            'form.end_time.after' => 'Waktu selesai tidak boleh lebih awal dari waktu mulai.',
            'form.place.required' => 'Tempat ibadah harus diisi jika jadwal berstatus dipublikasikan.',
        ]);
        
        $this->form['status'] = $status;
        
        if ($this->banner_image) {
            $path = $this->banner_image->store('events', 'public');
            
            if ($this->event && $this->event->banner_image) {
                Storage::disk('public')->delete($this->event->banner_image);
            }
            
            $this->form['banner_image'] = $path;
        }

        if ($this->event && $this->event->exists) {
            $this->event->update($this->form);
        } else {
            $this->event = Event::create($this->form);
        }

        if ($redirect) {
            return redirect()->route('admin.events.index');
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
            $this->form['event_type_id'],
            $this->form['event_date'],
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
    
    public function deleteEvent()
    {
        if ($this->event && $this->event->exists) {
            if ($this->event->banner_image) {
                Storage::disk('public')->delete($this->event->banner_image);
            }
            $this->event->delete();
            return redirect()->route('admin.events.index');
        }
    }

    public function generateOtp()
    {
        if ($this->event) {
            $this->event->generateOtp();
            $this->event->refresh();
        }
    }
};
?>

<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ $event ? 'Detail' : 'Buat' }} Jadwal Kegiatan</h1>
            <p class="text-zinc-500 dark:text-zinc-400">Isi detail jadwal, petugas, dan info kegiatan.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @if(!$event)
                <flux:button href="{{ route('admin.events.index') }}" class="bg-red-600 hover:bg-red-700 text-white border border-red-700">Batal</flux:button>
                <flux:button wire:click="save('draft')" variant="primary" class="bg-blue-600 hover:bg-blue-700 text-white border border-blue-700">Simpan</flux:button>
            @else
                <flux:button href="{{ route('admin.events.index') }}" variant="outline">Batal</flux:button>
                
                <flux:modal.trigger name="delete-event">
                    <flux:button variant="danger" class="border border-red-700">Hapus Jadwal</flux:button>
                </flux:modal.trigger>
                
                @if($event->status === 'published')
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
        
        @if($event && $event->is_today)
        <!-- Presensi & QR Code -->
        <flux:card class="bg-purple-50 dark:bg-purple-900/10 border-purple-200 dark:border-purple-800/30">
            <div class="flex flex-col sm:flex-row items-center gap-6">
                <!-- QR Code Image -->
                <div class="shrink-0 bg-white p-2 rounded-xl shadow-sm border border-zinc-200">
                    @php
                        // Jika belum ada OTP, generate otomatis pertama kali saat dilihat
                        if (!$event->attendance_otp) {
                            $event->generateOtp();
                        }
                        
                        $qrUrl = $event->qrVerificationUrl(true);
                        $logoPath = public_path('storage/tyouth-logo.png');
                        
                        $builderArgs = [
                            'writer' => new \Endroid\QrCode\Writer\PngWriter(),
                            'writerOptions' => [],
                            'data' => $qrUrl,
                            'encoding' => new \Endroid\QrCode\Encoding\Encoding('UTF-8'),
                            'errorCorrectionLevel' => \Endroid\QrCode\ErrorCorrectionLevel::High,
                            'size' => 200,
                            'margin' => 5,
                            'roundBlockSizeMode' => \Endroid\QrCode\RoundBlockSizeMode::Margin,
                            'foregroundColor' => new \Endroid\QrCode\Color\Color(107, 33, 168),
                            'backgroundColor' => new \Endroid\QrCode\Color\Color(255, 255, 255),
                        ];
                        
                        if (file_exists($logoPath)) {
                            $builderArgs['logoPath'] = $logoPath;
                            $builderArgs['logoResizeToWidth'] = 70;
                            $builderArgs['logoPunchoutBackground'] = false;
                        }
                        
                        $builder = new \Endroid\QrCode\Builder\Builder(...$builderArgs);
                        $result = $builder->build();
                        $qrBase64 = $result->getDataUri();
                    @endphp
                    <img src="{{ $qrBase64 }}" alt="QR Code Absensi" class="w-40 h-40 object-contain">
                </div>
                
                <!-- Info & Actions -->
                <div class="flex-1 text-center sm:text-left">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 text-xs font-medium mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Ibadah Hari Ini
                    </div>
                    <flux:heading size="lg" class="!font-bold mb-1">Presensi Kehadiran QR Code</flux:heading>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4 max-w-lg">
                        Download gambar QR Code ini dan tampilkan di layar proyektor. Jemaat dapat melakukan scan untuk mencatat kehadiran secara langsung, atau memasukkan kode OTP di bawah melalui website.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6">
                        <div>
                            <span class="block text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-1">Kode OTP</span>
                            <span class="text-4xl font-black tracking-widest text-purple-700 dark:text-purple-400 font-mono">
                                {{ $event->attendance_otp }}
                            </span>
                        </div>
                        
                        <div class="h-10 w-px bg-zinc-200 dark:bg-zinc-700 hidden sm:block"></div>
                        
                        <div class="flex gap-2">
                            <flux:button wire:click="generateOtp" icon="arrow-path" variant="outline" class="!text-purple-600 !border-purple-200 hover:!bg-purple-50 dark:!text-purple-400 dark:!border-purple-800/50 dark:hover:!bg-purple-900/20">
                                Regenerate
                            </flux:button>
                            <flux:button href="{{ $qrBase64 }}" download="QR_Absensi_{{ $event->event_date->format('Y-m-d') }}.png" variant="primary" icon="arrow-down-tray" class="!bg-purple-600 hover:!bg-purple-700 text-white !border-purple-700">
                                Download
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>
        @endif

        <!-- Informasi Umum -->
        <flux:card>
            <div class="flex justify-between items-center mb-4 border-b border-zinc-200 dark:border-zinc-700 pb-2">
                <flux:heading size="lg" class="!font-bold">Informasi Umum</flux:heading>
                @if($event)
                    <flux:button size="sm" variant="subtle" icon="{{ $editMode['general_info'] ? 'document-check' : 'pencil-square' }}" class="!text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-500/10" wire:click="toggleEdit('general_info')" />
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>Tanggal Kegiatan <span class="text-red-500 cursor-help" title="Harus diisi agar jadwal dapat dipublikasikan">*</span></flux:label>
                    <flux:input type="date" wire:model="form.event_date" required :disabled="!$editMode['general_info']" />
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

                <div class="space-y-3 col-span-1 md:col-span-2">
                    <flux:field>
                        <flux:label>Jenis Kegiatan <span class="text-red-500 cursor-help" title="Harus diisi agar jadwal dapat dipublikasikan">*</span></flux:label>
                        <flux:select wire:model.live="form.event_type_id" required :disabled="!$editMode['general_info']">
                            @foreach($eventTypes as $type)
                                <flux:select.option value="{{ $type->id }}">{{ $type->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                    
                    @if($form['event_type_id'] == 1)
                        <flux:field>
                            <flux:label>Tipe Kebaktian <span class="text-red-500 cursor-help" title="Harus diisi agar jadwal dapat dipublikasikan">*</span></flux:label>
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
                    @elseif($form['event_type_id'] == 4)
                        <flux:input wire:model="form.custom_event_type" label="Nama Event Khusus" placeholder="Masukkan nama event..." required :disabled="!$editMode['general_info']" />
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

        @if($event)
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
    <flux:modal name="delete-event" class="min-w-[22rem]">
        <form wire:submit.prevent="deleteEvent" class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Jadwal Kegiatan?</flux:heading>
                <flux:subheading>
                    <p>Apakah Anda yakin ingin menghapus jadwal ini?</p>
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