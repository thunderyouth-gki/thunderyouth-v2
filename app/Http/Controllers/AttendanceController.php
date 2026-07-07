<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Verify attendance via QR Code (Signed URL)
     */
    public function verifyViaQr(Request $request, Service $service): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        // Route middleware 'signed:relative' already ensures the signature is valid.

        if (! $service->is_live) {
            abort(403, 'Ibadah tidak sedang berlangsung atau QR Code ini sudah tidak berlaku.');
        }

        $otp = $request->query('otp');

        if (! $otp || $otp !== $service->attendance_otp) {
            abort(403, 'Kode OTP pada QR Code ini tidak valid atau sudah kadaluarsa.');
        }

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('guest.attendance', ['otp' => $otp, 'method' => 'QR']);
        }

        $member = $user->member;
            $memberId = $member ? $member->id : null;
            $guestName = $member ? null : $user->name;

            $existing = \App\Models\Attendance::where('service_id', $service->id)
                ->where(function ($q) use ($memberId, $guestName) {
                    if ($memberId) {
                        $q->where('member_id', $memberId);
                    } else {
                        $q->where('guest_name', $guestName);
                    }
                })->first();

            if (!$existing) {
                \App\Models\Attendance::create([
                    'service_id' => $service->id,
                    'member_id' => $memberId,
                    'guest_name' => $guestName,
                    'method' => 'QR',
                    'check_in_time' => now(),
                ]);
        }

        return view('attendance.success', [
            'service' => $service,
            'method' => 'QR Code',
        ]);
    }

    /**
     * Verify attendance via NFC (Static URL -> GPS Validation)
     */
    public function verifyViaNfc(Request $request): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        // NFC tags use a static URL that redirects here.
        // We find the active service and render the Livewire component
        // to do the final GPS validation.
        $activeService = Service::whereDate('service_date', today())->first();

        if (! $activeService) {
            abort(404, 'Tidak ada ibadah yang sedang berlangsung hari ini.');
        }

        $user = auth()->user();
        if (!$user) {
            return redirect()->route('guest.attendance', ['method' => 'NFC']);
        }

        // We render a view that includes the MarkAttendance Livewire component
        return view('attendance.nfc', [
            'service' => $activeService,
        ]);
    }
}
