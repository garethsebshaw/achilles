<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\WorkoutSignup;

class CheckInUser
{
    use Dispatchable, SerializesModels;

    public WorkoutSignup $signup;

    public function __construct(WorkoutSignup $signup)
    {
        $this->signup = $signup;
    }
}
