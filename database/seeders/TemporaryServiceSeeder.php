<?php

namespace Database\Seeders;

use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TemporaryServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mendapatkan tanggal hari Minggu terdekat
        $nextSunday = Carbon::parse('next sunday');

        $services = [
            [
                'service_date' => Carbon::now()->subWeeks(3)->startOfWeek()->addDays(6), // Minggu 3 minggu lalu
                'service_type' => 'back_to_the_bible',
                'theme' => 'Mengasihi Sesama',
                'description' => 'Ibadah pemuda dengan tema mengasihi sesama.',
                'speaker' => 'Pdt. Rahmat Santoso',
                'place' => 'Ruang Pemuda Lt. 1',
                'start_time' => '09:30',
                'end_time' => '11:00',
                'status' => 'published',
            ],
            [
                'service_date' => Carbon::now()->subWeeks(2)->startOfWeek()->addDays(6), // Minggu 2 minggu lalu
                'service_type' => 'sharing_sunday',
                'theme' => 'Berani Melangkah',
                'description' => 'Memiliki keberanian di dalam Tuhan.',
                'speaker' => 'Pnt. Mariana Hartono',
                'place' => 'Ruang Pemuda Lt. 1',
                'start_time' => '09:30',
                'end_time' => '11:00',
                'status' => 'published',
            ],
            [
                'service_date' => Carbon::now()->subWeeks(1)->startOfWeek()->addDays(6), // Minggu lalu
                'service_type' => 'kebaktian_gabungan',
                'theme' => 'Kasih yang Memulihkan',
                'description' => 'Merenungkan kasih tanpa syarat Kristus.',
                'speaker' => 'Pnt. Budi Setiawan',
                'place' => 'Ruang Pemuda Lt. 1',
                'start_time' => '09:30',
                'end_time' => '11:00',
                'status' => 'published',
            ],
            [
                'service_date' => $nextSunday, // Minggu terdekat ke depan
                'service_type' => 'celebration_week',
                'theme' => 'Berakar dan Bertumbuh',
                'description' => 'Membahas pentingnya dasar firman yang kokoh agar kehidupan rohani kita tidak goyah.',
                'speaker' => 'Pdt. Samuel Krispradipta',
                'place' => 'Ruang Pemuda Lt. 1',
                'start_time' => '09:30',
                'end_time' => '11:30',
                'status' => 'published',
            ],
            [
                'service_date' => $nextSunday->copy()->addWeek(), // Minggu depannya lagi
                'service_type' => 'back_to_the_bible',
                'theme' => 'Menjadi Terang',
                'description' => 'Bagaimana peran pemuda membawa damai sejahtera.',
                'speaker' => 'Pdt. Rahmat Santoso',
                'place' => 'Ruang Pemuda Lt. 1',
                'start_time' => '09:30',
                'end_time' => '11:00',
                'status' => 'published',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
