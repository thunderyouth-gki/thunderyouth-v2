<?php

namespace App\Livewire\Admin;

use App\Models\Member;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class MemberManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'id';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    // Form state
    public ?int $manageMemberId = null;
    public ?int $deleteMemberId = null;

    public string $member_number = '';
    public string $name = '';
    public string $place_of_birth = '';
    public string $date_of_birth = '';
    public string $address = '';
    public string $email = '';
    public string $phone_number = '';
    public string $gender = '';
    public string $blood_type = '';
    public string $status = '';
    public ?string $user_id = '';
    public string $userSearch = '';

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

    public function updatedUserSearch(): void
    {
        $this->user_id = '';
    }

    public function selectUser(?int $id, string $name): void
    {
        $this->user_id = (string) $id;
        $this->userSearch = $name;
        
        if ($id) {
            $user = User::find($id);
            if ($user) {
                $this->email = $user->email;
            }
        }
    }

    private function resetForm(): void
    {
        $this->reset([
            'manageMemberId', 'member_number', 'name', 'place_of_birth', 
            'date_of_birth', 'address', 'email', 'phone_number', 
            'gender', 'blood_type', 'status', 'user_id'
        ]);
        $this->gender = '';
        $this->blood_type = '';
        $this->status = '';
        $this->user_id = '';
        $this->userSearch = '';
    }

    public function openNewMemberModal(): void
    {
        $this->resetForm();
        \Flux::modal('manage-member-modal')->show();
    }

    public function openEditMemberModal(int $id): void
    {
        $this->resetForm();
        $member = Member::findOrFail($id);
        
        $this->manageMemberId = $member->id;
        $this->member_number = $member->member_number ?? '';
        $this->name = $member->name;
        $this->place_of_birth = $member->place_of_birth ?? '';
        $this->date_of_birth = $member->date_of_birth ? $member->date_of_birth->format('Y-m-d') : '';
        $this->address = $member->address ?? '';
        $this->email = $member->email ?? '';
        $this->phone_number = $member->phone_number ?? '';
        $this->gender = $member->gender ?? 'L';
        $this->blood_type = $member->blood_type ?? 'O';
        $this->status = $member->status ?? 'Active';
        $this->user_id = (string) $member->user_id;
        $this->userSearch = $member->user ? $member->user->name . ' (' . $member->user->email . ')' : '';

        \Flux::modal('manage-member-modal')->show();
    }

    public function saveMember(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'member_number' => 'nullable|string|max:255',
            'place_of_birth' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'gender' => 'required|in:L,P',
            'blood_type' => 'required|in:A,B,AB,O',
            'status' => 'required|in:Active,Abroad,Deceased',
            'user_id' => 'nullable|exists:users,id',
        ];

        if (empty($this->user_id)) {
            $this->user_id = null;
            $rules['user_id'] = 'nullable';
        }

        $validated = $this->validate($rules);

        if ($this->manageMemberId) {
            $member = Member::findOrFail($this->manageMemberId);
            $member->update($validated);
            $message = 'Member updated successfully!';
        } else {
            Member::create($validated);
            $message = 'Member created successfully!';
        }

        // Sync name to User table if a User is linked
        if (!empty($this->user_id)) {
            User::where('id', $this->user_id)->update(['name' => $this->name]);
        }

        \Flux::modal('manage-member-modal')->close();
        $this->dispatch('notify', message: $message);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteMemberId = $id;
        \Flux::modal('delete-member-modal')->show();
    }

    public function deleteMember(): void
    {
        if ($this->deleteMemberId) {
            $member = Member::findOrFail($this->deleteMemberId);
            $userId = $member->user_id;
            
            $member->delete();
            
            if ($userId) {
                User::find($userId)?->delete();
            }
            
            $this->deleteMemberId = null;
            \Flux::modal('delete-member-modal')->close();
            $this->dispatch('notify', message: 'Member and linked user account deleted successfully.');
        }
    }

    public function render(): View
    {
        $members = Member::with('user')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('member_number', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection === 'asc' ? 'asc' : 'desc')
            ->paginate($this->perPage);

        $users = collect();
        if (strlen($this->userSearch) > 0 && empty($this->user_id)) {
            $users = User::where('name', 'like', '%' . $this->userSearch . '%')
                ->orWhere('email', 'like', '%' . $this->userSearch . '%')
                ->take(5)
                ->get();
        }

        return view('livewire.admin.member-manager', [
            'members' => $members,
            'users' => $users,
        ])->layout('components.layouts.admin');
    }
}
