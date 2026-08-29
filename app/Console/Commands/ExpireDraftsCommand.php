<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Setting;
use App\Models\ApplicationActivity;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ExpireDraftsCommand extends Command
{
    protected $signature = 'applications:expire-drafts';
    protected $description = 'Automatically expire draft and payment pending applications that exceed the configured inactivity limit.';

    public function handle()
    {
        $days = (int) Setting::get('draft_expiration_days', 30);
        $this->info("Running draft expiration check. Expiry threshold: {$days} days.");

        $expiryDate = Carbon::now()->subDays($days);

        $expiredCount = 0;

        Application::whereIn('status', ['Draft', 'Pending Payment', 'PAYMENT_PENDING', 'DRAFT'])
            ->where(function ($query) use ($expiryDate) {
                $query->where('last_activity_at', '<', $expiryDate)
                      ->orWhere(function ($q) use ($expiryDate) {
                          $q->whereNull('last_activity_at')
                            ->where('updated_at', '<', $expiryDate);
                      });
            })
            ->where(function ($query) {
                $query->whereHas('payment', function ($q) {
                    $q->where('payment_status', '!=', 'paid');
                })->orWhereDoesntHave('payment');
            })
            ->chunk(100, function ($applications) use (&$expiredCount, $days) {
                foreach ($applications as $app) {
                    $app->update([
                        'status' => 'EXPIRED',
                    ]);

                    ApplicationActivity::create([
                        'application_id' => $app->id,
                        'action' => 'Draft Expired',
                        'description' => "Application was marked as EXPIRED after {$days} days of inactivity.",
                    ]);

                    $expiredCount++;
                }
            });

        $this->info("Process completed. Expired {$expiredCount} application(s).");
        return Command::SUCCESS;
    }
}
