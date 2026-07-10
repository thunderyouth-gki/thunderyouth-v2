<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Verify attendance via QR Code (Signed URL)
     */
    public function verifyViaQr(Request $request, Event $event): View|RedirectResponse
    {
        // Route middleware 'signed:relative' already ensures the signature is valid.

        if (! $event->is_live) {
            abort(403, 'Ibadah tidak sedang berlangsung atau QR Code ini sudah tidak berlaku.');
        }

        $otp = $request->query('otp');

        if (! $otp || $otp !== $event->attendance_otp) {
            abort(403, 'Kode OTP pada QR Code ini tidak valid atau sudah kadaluarsa.');
        }

        $user = auth()->user();
        if (! $user) {
            return redirect()->route('guest.attendance', ['otp' => $otp, 'method' => 'QR']);
        }

        $member = $user->member;
        $memberId = $member ? $member->id : null;
        $guestName = $member ? null : $user->name;

        $existing = Attendance::where('event_id', $event->id)
            ->where(function ($q) use ($memberId, $guestName) {
                if ($memberId) {
                    $q->where('member_id', $memberId);
                } else {
                    $q->where('guest_name', $guestName);
                }
            })->first();

        if (! $existing) {
            Attendance::create([
                'event_id' => $event->id,
                'member_id' => $memberId,
                'guest_name' => $guestName,
                'method' => 'QR',
                'check_in_time' => now(),
            ]);
        }

        return view('attendance.success', [
            'event' => $event,
            'method' => 'QR Code',
        ]);
    }

    /**
     * Verify attendance via NFC (Static URL -> GPS Validation)
     */
    public function verifyViaNfc(Request $request): View|RedirectResponse
    {
        // NFC tags use a static URL that redirects here.
        // We find the active service and render the Livewire component
        // to do the final GPS validation.
        $activeService = Event::whereDate('event_date', today())->first();

        if (! $activeService) {
            abort(404, 'Tidak ada ibadah yang sedang berlangsung hari ini.');
        }

        $user = auth()->user();
        if (! $user) {
            return redirect()->route('guest.attendance', ['method' => 'NFC']);
        }

        // We render a view that includes the MarkAttendance Livewire component
        return view('attendance.nfc', [
            'event' => $activeService,
        ]);
    }
}
