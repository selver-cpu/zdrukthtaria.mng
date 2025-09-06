<?php

namespace App\Console\Commands;

use App\Events\NjoftimIRi;
use App\Models\Njoftimet;
use Illuminate\Console\Command;

class TestNotification extends Command
{
    protected $signature = 'test:notification {user_id} {message?}';
    protected $description = 'Send a test notification to a specific user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $message = $this->argument('message') ?? 'Ky është një njoftim testues.';

        $njoftim = Njoftimet::create([
            'perdorues_id' => $userId,
            'mesazhi' => $message,
            'lloji_njoftimit' => 'system',
            'lexuar' => false
        ]);

        event(new NjoftimIRi($njoftim));

        $this->info('Njoftimi u dërgua me sukses!');
    }
}
