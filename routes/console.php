<?php

use App\Models\Task;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();



Schedule::call(function () {
    $date = now()->format('Y-m-d');
    $tasks = Task::whereDate('created_at', Carbon::today())->get();
    // generate pdf report
    $pdf = Pdf::loadView('reports/daily-reports', ['tasks' => $tasks, 'date' => $date]);
    // save report in folder reports with name depend on filter data in public folder
    $filePath = 'reports/daily-report-' . $date . '.pdf';
    Storage::disk('public')->put($filePath, $pdf->output());
})->daily();
