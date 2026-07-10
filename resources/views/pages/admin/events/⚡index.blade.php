<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Event;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $perPage = 10;
    public $sortField = 'event_date';
    public $sortDirection = 'desc';
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
            $this->sortField = $field;
        }
    }
    
    public function deleteEvent(Event $event)
    {
        $event->delete();
    }

    public function with(): array
    {
        $query = Event::with('eventType');
        
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

        $query->orderBy($this->sortField, $this->sortDirection);

        return [
            'events' => $query->paginate($this->perPage),
        ];
    }
};
?>

<div>
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">Events & Services</h1>
            <p class="text-zinc-500 dark:text-zinc-400">Kelola jadwal event dan kebaktian.</p>
        </div>
        <flux:button href="{{ route('admin.events.create') }}" class="bg-primary hover:bg-primary/90 text-white dark:bg-primary dark:hover:bg-primary/80 border border-blue-700/50 shadow-sm" icon="plus">Buat Jadwal Baru</flux:button>
    </div>

    <!-- Filters and Search -->
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-2">
            <span class="text-sm text-slate-500 dark:text-slate-400">Show</span>
            <select wire:model.live="perPage" class="border border-slate-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-2 py-1 focus:ring-primary focus:border-primary">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
            </select>
            <span class="text-sm text-slate-500 dark:text-slate-400">entries</span>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <select wire:model.live="status" class="border border-slate-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 px-3 py-2 focus:ring-primary focus:border-primary">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i class="fa-solid fa-search text-xs"></i>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white text-sm rounded-lg focus:ring-primary focus:border-primary block w-full pl-10 p-2" placeholder="Cari tema, pembicara...">
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500 dark:text-slate-400">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50 dark:bg-slate-700/50 dark:text-slate-300">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors" wire:click="sortBy('event_date')">
                            Date 
                            @if($sortField === 'event_date') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors" wire:click="sortBy('event_type_id')">
                            Event Type
                            @if($sortField === 'event_type_id') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors w-60" wire:click="sortBy('theme')">
                            Theme
                            @if($sortField === 'theme') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors w-60" wire:click="sortBy('speaker')">
                            Speaker
                            @if($sortField === 'speaker') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors" wire:click="sortBy('status')">
                            Status
                            @if($sortField === 'status') <i class="fa-solid fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i> @endif
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Options</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse($events as $index => $event)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-slate-900 dark:text-white">{{ $event->event_date->format('d M Y') }}</div>
                                <div class="text-xs text-slate-500">{{ $event->time }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300 text-xs font-medium px-2 py-0.5 rounded whitespace-nowrap">
                                    {{ $event->eventType ? $event->eventType->name : 'Unknown' }}
                                </span>
                                @if($event->eventType && $event->eventType->name === 'Service (Kebaktian/Ibadah)')
                                    <div class="text-xs mt-1 text-slate-500">{{ $event->service_type === 'other' ? $event->custom_service_type : str($event->service_type)->replace('_', ' ')->title() }}</div>
                                @elseif($event->eventType && $event->eventType->name === 'Other (Lainnya)')
                                    <div class="text-xs mt-1 text-slate-500">{{ $event->custom_event_type }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white" title="{{ $event->theme }}">
                                {{ $event->theme ?: '-' }}
                            </td>
                            <td class="px-6 py-4">{{ $event->speaker ?: '-' }}</td>
                            <td class="px-6 py-4">
                                @if($event->status === 'published')
                                    <span class="bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 text-xs font-medium px-2 py-0.5 rounded">Published</span>
                                @else
                                    <span class="bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300 text-xs font-medium px-2 py-0.5 rounded">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <flux:button href="{{ route('admin.events.edit', $event) }}" size="sm" class="bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 transition-colors">View Detail</flux:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada jadwal event.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($events->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700">
                {{ $events->links(data: ['scrollTo' => false]) }}
            </div>
        @endif
    </div>
</div>