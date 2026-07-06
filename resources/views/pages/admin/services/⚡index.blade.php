<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Service;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedStatus()
    {
        $this->resetPage();
    }
    
    public function deleteService(Service $service)
    {
        $service->delete();
        // optionally add a toast notification using Flux or browser dispatch
    }

    public function with(): array
    {
        $query = Service::query()->orderBy('service_date', 'desc');
        
        if ($this->search) {
            $query->where(function($q) {
                $q->where('theme', 'like', '%' . $this->search . '%')
                  ->orWhere('speaker', 'like', '%' . $this->search . '%')
                  ->orWhere('bible_reading', 'like', '%' . $this->search . '%');
            });
        }
        
        if ($this->status) {
            $query->where('status', $this->status);
        }

        return [
            'services' => $query->paginate(10),
        ];
    }
};
?>

<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Services (Kebaktian)</h1>
            <p class="text-zinc-500 dark:text-zinc-400">Kelola jadwal dan detail kebaktian pemuda.</p>
        </div>
        <flux:button href="{{ route('admin.services.create') }}" class="bg-primary hover:bg-primary/90 text-white dark:bg-primary dark:hover:bg-primary/80 border-none shadow-sm" icon="plus">Buat Jadwal Baru</flux:button>
    </div>

    <flux:card>
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari tema, pembicara, ayat..." />
            </div>
            <div class="w-full sm:w-48">
                <flux:select wire:model.live="status" placeholder="Semua Status">
                    <flux:select.option value="">Semua Status</flux:select.option>
                    <flux:select.option value="draft">Draft</flux:select.option>
                    <flux:select.option value="published">Published</flux:select.option>
                </flux:select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Tanggal</flux:table.column>
                    <flux:table.column>Tipe</flux:table.column>
                    <flux:table.column>Tema</flux:table.column>
                    <flux:table.column>Pembicara</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($services as $service)
                        <flux:table.row :key="$service->id">
                            <flux:table.cell>
                                <div class="font-medium whitespace-nowrap">{{ $service->service_date->format('d M Y') }}</div>
                                <div class="text-xs text-zinc-500">{{ $service->time }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                @php
                                    $typeLabels = [
                                        'back_to_the_bible' => 'Back To The Bible',
                                        'sharing_sunday' => 'Sharing Sunday',
                                        'kebaktian_gabungan' => 'Kebaktian Gabungan',
                                        'celebration_week' => 'Celebration Week',
                                        'other' => $service->custom_service_type ?? 'Lainnya',
                                    ];
                                @endphp
                                <flux:badge size="sm" color="zinc">{{ $typeLabels[$service->service_type] ?? $service->service_type }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="font-medium truncate max-w-[200px]" title="{{ $service->theme }}">{{ $service->theme ?? '-' }}</div>
                            </flux:table.cell>
                            <flux:table.cell>{{ $service->speaker ?? '-' }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$service->status === 'published' ? 'green' : 'orange'">
                                    {{ ucfirst($service->status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:button.group>
                                    <flux:button href="{{ route('admin.services.edit', $service) }}" size="sm" icon="pencil-square" variant="ghost" />
                                    <flux:button wire:click="deleteService({{ $service->id }})" wire:confirm="Apakah Anda yakin ingin menghapus jadwal ini?" size="sm" icon="trash" variant="ghost" color="danger" />
                                </flux:button.group>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center text-zinc-500 py-8">
                                Belum ada jadwal kebaktian.
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="mt-4">
            {{ $services->links() }}
        </div>
    </flux:card>
</div>