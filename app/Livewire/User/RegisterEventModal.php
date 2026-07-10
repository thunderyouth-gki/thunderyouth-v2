<?php

namespace App\Livewire\User;

use App\Models\Event;
use App\Models\EventRegistration;
use Livewire\Component;

class RegisterEventModal extends Component
{
    public ?Event $event = null;
    public bool $show = false;
    public ?int $eventId = null;

    protected $listeners = ['openModal' => 'open'];

    public function open(string $modalName, int $eventId)
    {
        if ($modalName === 'register-event') {
            $this->eventId = $eventId;
            $this->event = Event::with('eventType')->find($eventId);
            $this->show = true;
        }
    }

    public function register()
    {
        if (!$this->event) return;

        $user = auth()->user();
        $memberId = $user->member?->id;

        if (!$memberId) {
            // Cannot register guests to events for now, or maybe can? The DB needs member_id or guest_name.
            // Let's check event_registrations table schema.
            // Earlier we created event_registrations with member_id nullable and guest_name nullable.
        }

        EventRegistration::firstOrCreate([
            'event_id' => $this->event->id,
            'member_id' => $memberId,
            'guest_name' => $memberId ? null : $user->name,
        ], [
            'registration_time' => now(),
        ]);

        $this->show = false;
        
        // Dispatch to refresh dashboard
        $this->dispatch('registrationCompleted');
        
        $this->redirectRoute('dashboard');
    }

    public function render()
    {
        return view('livewire.user.register-event-modal');
    }
}
