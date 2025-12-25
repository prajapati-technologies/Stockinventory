<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use App\Mail\ReportMail;
use App\Models\Sale;

class SendReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 2;

    protected $salesIds;
    protected $email;
    protected $format;
    protected $fileName;
    protected $userName;
    protected $userRole;
    protected $filters;

    /**
     * Create a new job instance.
     */
    public function __construct($salesIds, $email, $format, $fileName, $userName, $userRole, $filters = [])
    {
        $this->salesIds = $salesIds;
        $this->email = $email;
        $this->format = $format;
        $this->fileName = $fileName;
        $this->userName = $userName;
        $this->userRole = $userRole;
        $this->filters = $filters;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Increase memory and time limits
        ini_set('memory_limit', '1024M');
        set_time_limit(600);

        // Reload sales data with relationships
        $query = Sale::whereIn('id', $this->salesIds)
            ->with(['customer.district', 'customer.mandal', 'store.district', 'store.mandal']);

        if (isset($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $sales = $query->latest()->get();

        $tempDirectory = storage_path('app/temp');
        if (!File::exists($tempDirectory)) {
            File::makeDirectory($tempDirectory, 0755, true);
        }

        // Generate Excel file
        $filePath = 'temp/' . $this->fileName;
        Excel::store(new SalesExport($sales), $filePath, 'local');
        $fullPath = Storage::disk('local')->path($filePath);

        // Verify file was created
        if (!File::exists($fullPath)) {
            throw new \Exception('File was not created at: ' . $fullPath);
        }

        // Send email
        Mail::to($this->email)->send(new ReportMail(
            $this->fileName,
            $fullPath,
            $this->format,
            $this->userName,
            $this->userRole
        ));

        // Clean up temporary file
        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Log error or notify admin
        \Log::error('SendReportJob failed: ' . $exception->getMessage());
    }
}
