<?php

namespace App\Jobs\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 刷新用户上次活动时间
 *
 * @author Tongle Xu <xutongle@msn.com>
 */
class RefreshUserLastActiveAt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        if (empty($this->user->last_active_at) || $this->user->last_active_at->lt(\now()->subMinutes(5))) {
            $this->user->refreshLastActiveAt();
        }
    }
}
