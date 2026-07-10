<?php

namespace App\Livewire\User;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventRegistration;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
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
            ->orderBy('check_in_time', 'desc')
            ->take(5)
            ->get();

        $upcomingEvents = Event::with('eventType')
            ->where('event_date', '>=', today())
            ->whereHas('eventType', function ($q) {
                $q->where('name', '!=', 'Service (Kebaktian/Ibadah)');
            })
            ->orderBy('event_date', 'asc')
            ->take(5)
            ->get();

        $registeredEventIds = EventRegistration::where('member_id', $memberId)
            ->pluck('event_id')
            ->toArray();

        return view('livewire.user.dashboard', [
            'attendances' => $attendances,
            'upcomingEvents' => $upcomingEvents,
            'registeredEventIds' => $registeredEventIds,
        ])->layout('components.layouts.user');
    }
}
