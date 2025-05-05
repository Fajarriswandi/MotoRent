<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use Carbon\Carbon;

class AutoCompleteRental extends Command
{
    protected $signature = 'rental:auto-complete';
    protected $description = 'Tandai rental sebagai selesai jika tanggal selesai sudah lewat dan belum ditandai selesai.';

    public function handle()
    {
        $today = Carbon::today();

        $rentals = Rental::where('end_date', '<', $today)
            ->where('is_completed', false)
            ->where('is_cancelled', false)
            ->get();

        foreach ($rentals as $rental) {
            $rental->is_completed = true;
            $rental->save();
        }

        $this->info(count($rentals) . ' rental ditandai selesai otomatis.');
        \Log::info('[Scheduler] AutoCompleteRental dijalankan pada ' . now());
    }
}
