<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RentalsReportExport implements FromView
{
    protected $rentals;
    protected $startDate;
    protected $endDate;
    protected $summary;

    public function __construct($rentals, $startDate, $endDate)
    {
        $this->rentals = $rentals;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        $this->summary = [
            'total_rentals' => $this->rentals->count(),
            'total_revenue' => $this->rentals->sum('total_price'),
            'unique_customers' => $this->rentals->pluck('customer_id')->unique()->count(),
            'top_motorbike' => $this->rentals
                ->groupBy('motorbike_id')
                ->sortByDesc(fn ($group) => $group->count())
                ->first()?->first()->motorbike ?? null,
        ];
    }

    public function view(): View
    {
        return view('admin.reports.export_pdf', [
            'rentals' => $this->rentals,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'summary' => $this->summary,
        ]);
    }
}
