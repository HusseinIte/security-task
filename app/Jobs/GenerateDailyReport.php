<?php

namespace App\Jobs;

use App\Models\Task;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateDailyReport implements ShouldQueue
{
    use  Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(array $filterData): void
    {

        $date = now()->format('Y-m-d');
        $tasks = Task::filterTask($filterData)->whereDate('created_at', Carbon::today())->get();
        // generate pdf report
        $pdf = Pdf::loadView('reports/daily-reports', ['tasks' => $tasks, 'date' => $date]);
        // save report in folder reports with name depend on filter data in public folder
        $filter = !empty(array_filter($filterData)) ? implode('_', array_filter($filterData)) : "general";
        $filePath = 'reports/' . $filter . '/daily-report-' . $date . '.pdf';
        Storage::disk('public')->put($filePath, $pdf->output());
    }
}
