<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Verify attendance via QR Code (Signed URL)
     */
    public function verifyViaQr(Request $request, Service $service): \Illuminate\View\View
    {
        // Route middleware 'signed:relative' already ensures the signature is valid.

        if (! $service->is_live) {
            abort(403, 'Ibadah tidak sedang berlangsung atau QR Code ini sudah tidak berlaku.');
        }

        $otp = $request->query('otp');

        if (! $otp || $otp !== $service->attendance_otp) {
            abort(403, 'Kode OTP pada QR Code ini tidak valid atau sudah kadaluarsa.');
        }

        return view('attendance.success', [
            'service' => $service,
            'method' => 'QR Code',
        ]);
    }

    /**
     * Verify attendance via NFC (Static URL -> GPS Validation)
     */
    public function verifyViaNfc(Request $request): \Illuminate\View\View
    {
        // NFC tags use a static URL that redirects here.
        // We find the active service and render the Livewire component
        // to do the final GPS validation.
        $activeService = Service::whereDate('service_date', today())->first();

        if (! $activeService) {
            abort(404, 'Tidak ada ibadah yang sedang berlangsung hari ini.');
        }

        // We render a view that includes the MarkAttendance Livewire component
        return view('attendance.nfc', [
            'service' => $activeService,
        ]);
    }
}
