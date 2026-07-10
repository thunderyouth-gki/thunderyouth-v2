<?php

namespace App\Livewire\User;

use App\Models\Attendance;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Attendances extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'check_in_time';
    public string $sortDirection = 'desc';
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

        $attendances = Attendance::with(['event', 'event.eventType'])
            ->where(function ($query) use ($user, $memberId) {
                if ($memberId) {
                    $query->where('member_id', $memberId);
                } else {
                    $query->where('guest_name', $user->name);
                }
            })
            ->when($this->search, function ($query) {
                $query->whereHas('event', function ($q) {
                    $q->where('theme', 'like', '%'.$this->search.'%')
                        ->orWhere('service_type', 'like', '%'.$this->search.'%')
                        ->orWhere('custom_event_type', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.user.attendances', [
            'attendances' => $attendances,
        ])->layout('components.layouts.user');
    }
}
