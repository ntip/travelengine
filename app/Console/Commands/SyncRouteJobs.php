<?php

namespace App\Console\Commands;

use App\Models\Route;
use App\Models\RouteJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncRouteJobs extends Command
{
    protected $signature = 'routes:sync-jobs';
    protected $description = 'Create/maintain future route jobs and rehydrate successful ones.';

    public function handle(): int
    {
        $today = now()->startOfDay();

        $this->info('Syncing route jobs for date: ' . $today->toDateString());

        DB::transaction(function () use ($today) {
            $this->archivePastJobs($today);
            $this->createFutureJobs($today);
            $this->rehydrateSuccessfulJobs($today);
        });

        $this->info('Route jobs sync completed.');

        return self::SUCCESS;
    }

    /**
     * Mark jobs in the past as archived.
     */
    protected function archivePastJobs(Carbon $today): void
    {
        $affected = RouteJob::whereDate('job_date', '<', $today->toDateString())
            ->where('archived', false)
            ->update([
                'archived' => true,
                'status'   => 'archived',
            ]);

        $this->info("Archived past jobs: {$affected}");
    }

    /**
     * Ensure we have jobs for each route up to route.days_ahead.
     */
    protected function createFutureJobs(Carbon $today): void
    {
        $routes = Route::with('configs')->get();

        $createdCount = 0;

        foreach ($routes as $route) {
            $daysAhead = $route->daysAhead();

            if ($daysAhead <= 0) {
                continue;
            }

            for ($offset = 0; $offset <= $daysAhead; $offset++) {
                $date = $today->copy()->addDays($offset)->toDateString();

                // Check by DATE (ignores any stray time component in DB)
                $exists = RouteJob::where('route_id', $route->id)
                    ->whereDate('job_date', $date)
                    ->exists();

                if ($exists) {
                    continue;
                }

                RouteJob::create([
                    'route_id'       => $route->id,
                    'job_date'       => $date,
                    'status'         => 'pending',
                    'archived'       => false,
                    'next_run_at'    => null,
                    'last_hydrated_at' => null,
                ]);

                $createdCount++;
            }
        }

        $this->info("Future jobs created: {$createdCount}");
    }

    /**
     * Re-open successful jobs inside the hydration window.
     *
     * days_hydrate is *per route* and only affects successful jobs
     * between today and today + days_hydrate.
     */
    protected function rehydrateSuccessfulJobs(Carbon $today): void
    {
        $routes = Route::with('configs')->get();
        $totalRehydrated = 0;

        foreach ($routes as $route) {
            $daysHydrate = $route->daysHydrate();

            if ($daysHydrate <= 0) {
                continue;
            }

            $hydrateUntil = $today->copy()->addDays($daysHydrate)->toDateString();
            $todayDate    = $today->toDateString();

            // Re-open jobs for this route in [today, today + days_hydrate]
            // that were successful but have not been hydrated today.
            $jobs = RouteJob::where('route_id', $route->id)
                ->active()
                ->where('status', 'success')
                ->whereBetween('job_date', [$todayDate, $hydrateUntil])
                ->where(function ($q) use ($today) {
                    $q->whereNull('last_hydrated_at')
                      ->orWhere('last_hydrated_at', '<', $today->startOfDay());
                })
                ->get();

            foreach ($jobs as $job) {
                $job->status       = 'pending';
                $job->next_run_at  = now();
                // Do NOT update last_hydrated_at here; that happens
                // when the worker successfully completes the job.
                $job->save();

                $totalRehydrated++;
            }
        }

        $this->info("Successful jobs re-opened for hydration: {$totalRehydrated}");
    }
}
