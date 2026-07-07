<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\Service;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'id';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    // Form state
    public ?int $manageAttendanceId = null;
    public ?int $deleteAttendanceId = null;

    public string $service_date = '';
    public bool $is_guest = false;
    public ?int $member_id = null;
    public string $memberSearch = '';
    public ?string $guest_name = null;
    public string $method = 'Manual';
    public ?string $check_in_time = null;

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

    public function updatedMemberSearch(): void
    {
        $this->member_id = null;
    }

    public function selectMember(?int $id, string $name): void
    {
        $this->member_id = $id;
        $this->memberSearch = $name;
        $this->guest_name = null;
    }

    private function resetForm(): void
    {
        $this->reset([
            'manageAttendanceId', 'member_id', 
            'guest_name', 'check_in_time', 'is_guest', 'service_date'
        ]);
        $this->memberSearch = '';
        $this->method = 'Manual';
    }

    public function openNewAttendanceModal(): void
    {
        $this->resetForm();
        $this->check_in_time = now()->format('H:i');
        \Flux::modal('manage-attendance-modal')->show();
    }

    public function openEditAttendanceModal(int $id): void
    {
        $this->resetForm();
        $attendance = Attendance::findOrFail($id);
        
        $this->manageAttendanceId = $attendance->id;
        $this->service_date = $attendance->service->service_date->format('Y-m-d');
        
        $this->is_guest = empty($attendance->member_id) && !empty($attendance->guest_name);
        $this->member_id = $attendance->member_id;
        $this->memberSearch = $attendance->member ? $attendance->member->name . ' (' . $attendance->member->member_number . ')' : '';
        $this->guest_name = $attendance->guest_name;
        
        $this->method = $attendance->method;
        $this->check_in_time = $attendance->check_in_time->format('H:i');

        \Flux::modal('manage-attendance-modal')->show();
    }

    public function saveAttendance(): void
    {
        $rules = [
            'service_date' => 'required|date',
            'method' => 'required|in:GPS,NFC,QR,Manual',
            'check_in_time' => 'required|date_format:H:i',
        ];

        if ($this->is_guest) {
            $rules['guest_name'] = 'required|string|max:255';
            $this->member_id = null;
            $this->memberSearch = '';
        } else {
            $rules['member_id'] = 'required|exists:members,id';
            $this->guest_name = null;
        }

        $validated = $this->validate($rules);

        $service = Service::whereDate('service_date', $validated['service_date'])->first();
        if (!$service) {
            $this->addError('service_date', 'No service found for the selected date.');
            return;
        }

        $validated['service_id'] = $service->id;
        $validated['check_in_time'] = $validated['service_date'] . ' ' . $validated['check_in_time'] . ':00';
        unset($validated['service_date']);
        
        $validated['member_id'] = $this->is_guest ? null : $this->member_id;
        $validated['guest_name'] = $this->is_guest ? $this->guest_name : null;

        if ($this->manageAttendanceId) {
            $attendance = Attendance::findOrFail($this->manageAttendanceId);
            $attendance->update($validated);
            $message = 'Attendance updated successfully!';
        } else {
            Attendance::create($validated);
            $message = 'Attendance recorded successfully!';
        }

        \Flux::modal('manage-attendance-modal')->close();
        $this->dispatch('notify', message: $message);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteAttendanceId = $id;
        \Flux::modal('delete-attendance-modal')->show();
    }

    public function deleteAttendance(): void
    {
        if ($this->deleteAttendanceId) {
            Attendance::findOrFail($this->deleteAttendanceId)->delete();
            $this->deleteAttendanceId = null;
            \Flux::modal('delete-attendance-modal')->close();
            $this->dispatch('notify', message: 'Attendance deleted successfully.');
        }
    }

    public function render(): View
    {
        $attendances = Attendance::with(['service', 'member'])
            ->when($this->search, function ($query) {
                $query->whereHas('member', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('member_number', 'like', '%' . $this->search . '%');
                })
                ->orWhere('guest_name', 'like', '%' . $this->search . '%')
                ->orWhereHas('service', function ($q) {
                    $q->where('theme', 'like', '%' . $this->search . '%')
                      ->orWhere('service_type', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $members = collect();
        if (strlen($this->memberSearch) > 0 && empty($this->member_id)) {
            $members = Member::where('name', 'like', '%' . $this->memberSearch . '%')
                ->orWhere('member_number', 'like', '%' . $this->memberSearch . '%')
                ->take(5)
                ->get();
        }

        $services = Service::whereDate('service_date', $this->service_date)
            ->orderBy('service_date', 'desc')
            ->get();

        return view('livewire.admin.attendance-manager', [
            'attendances' => $attendances,
            'services' => $services,
            'members' => $members,
        ])->layout('components.layouts.admin');
    }
}
