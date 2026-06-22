<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\SpecialOffer;
use App\Models\Request;
use App\Models\Review;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Exports\StatsExport;
use Maatwebsite\Excel\Facades\Excel;

class StatsController extends Controller
{
    public function getStats()
    {
        return [
            'totalDoctors' => Doctor::count(),
            'totalServices' => Service::count(),
            'totalOffers' => SpecialOffer::count(),
            'totalRequests' => Request::count(),
            'totalReviews' => Review::count(),
            'totalAppointments' => Appointment::count(),
            'totalUsers' => User::count(),
            'appointmentsToday' => Appointment::whereDate('created_at', today())->count(),
            'appointmentsThisMonth' => Appointment::whereMonth('created_at', now()->month)->count(),
            'newUsersThisMonth' => User::whereMonth('created_at', now()->month)->count(),
            'publishedReviews' => DB::table('published_reviews')->count(),
            'unpublishedReviews' => Review::count() - DB::table('published_reviews')->count(),
            'busyDoctor' => DB::table('appointment as a')
                ->join('doctors as d', 'a.doctors_id_doctor', '=', 'd.id_doctor')
                ->select('d.id_doctor', 'd.first_name', 'd.last_name', DB::raw('count(*) as total'))
                ->groupBy('a.doctors_id_doctor', 'd.first_name', 'd.last_name')
                ->orderBy('total', 'desc')
                ->first(),
        ];
    }
     public function export()
    {
        $stats = $this->getStats();
        return Excel::download(new StatsExport($stats), 'statistika_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}