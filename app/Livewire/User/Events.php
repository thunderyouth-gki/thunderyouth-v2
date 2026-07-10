<?php

namespace App\Livewire\User;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Events extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'event_date';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render(): View
    {
        $user = auth()->user();
        $memberId = $user->member?->id;

        $upcomingEvents = Event::with('eventType')
            ->where('event_date', '>=', today())
            ->whereHas('eventType', function ($q) {
                $q->where('name', '!=', 'Service (Kebaktian/Ibadah)');
            })
            ->when($this->search, function ($query) {
                $query->where('theme', 'like', '%'.$this->search.'%')
                    ->orWhere('custom_event_type', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $registeredEventIds = [];
        if ($memberId) {
            $registeredEventIds = EventRegistration::where('member_id', $memberId)
                ->pluck('event_id')
                ->toArray();
        }

        return view('livewire.user.events', [
            'upcomingEvents' => $upcomingEvents,
            'registeredEventIds' => $registeredEventIds,
        ])->layout('components.layouts.user');
    }
}
