<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Property;
use App\Services\FraudDetectionService;
use Illuminate\Console\Command;

class RunFraudDetection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fraud:detect 
                            {--users : Run fraud detection on users}
                            {--properties : Run fraud detection on properties}
                            {--all : Run fraud detection on both users and properties}
                            {--limit=100 : Number of records to process per run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Phase 1 fraud detection on users and/or properties';

    protected FraudDetectionService $fraudService;

    public function __construct(FraudDetectionService $fraudService)
    {
        parent::__construct();
        $this->fraudService = $fraudService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        
        if ($this->option('all') || $this->option('users')) {
            $this->info('Running fraud detection on users...');
            $this->processUsers($limit);
        }

        if ($this->option('all') || $this->option('properties')) {
            $this->info('Running fraud detection on properties...');
            $this->processProperties($limit);
        }

        if (!$this->option('all') && !$this->option('users') && !$this->option('properties')) {
            $this->error('Please specify --users, --properties, or --all');
            return 1;
        }

        $this->info('Fraud detection completed!');
        return 0;
    }

    private function processUsers(int $limit): void
    {
        $users = User::where('role', 'landlord')
            ->orWhere('role', 'renter')
            ->limit($limit)
            ->get();

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $flaggedCount = 0;

        foreach ($users as $user) {
            try {
                $fraudScore = $this->fraudService->calculateUserFraudScore($user);
                
                if ($fraudScore->is_flagged) {
                    $flaggedCount++;
                }
            } catch (\Exception $e) {
                $this->error("\nError processing user {$user->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Processed {$users->count()} users. Flagged: {$flaggedCount}");
    }

    private function processProperties(int $limit): void
    {
        $properties = Property::whereIn('status', ['active', 'pending'])
            ->limit($limit)
            ->get();

        $bar = $this->output->createProgressBar($properties->count());
        $bar->start();

        $flaggedCount = 0;

        foreach ($properties as $property) {
            try {
                $fraudScore = $this->fraudService->calculatePropertyFraudScore($property);
                
                if ($fraudScore->is_flagged) {
                    $flaggedCount++;
                }
            } catch (\Exception $e) {
                $this->error("\nError processing property {$property->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Processed {$properties->count()} properties. Flagged: {$flaggedCount}");
    }
}
