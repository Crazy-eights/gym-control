<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class ClearExpiredSessions extends Command
{
    protected $signature = 'sessions:clear-expired';
    protected $description = 'Clear expired session files';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $sessionPath = storage_path('framework/sessions');
        $files = File::files($sessionPath);
        $cleared = 0;
        $lifetime = config('session.lifetime', 120) * 60; // Convert to seconds

        foreach ($files as $file) {
            $lastModified = File::lastModified($file);
            
            if (Carbon::now()->timestamp - $lastModified > $lifetime) {
                File::delete($file);
                $cleared++;
            }
        }

        $this->info("Cleared {$cleared} expired session files.");
        return 0;
    }
}