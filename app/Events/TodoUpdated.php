<?php

namespace App\Events;

use App\Models\Todo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TodoUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Todo $todo;

    public function __construct(Todo $todo)
    {
        $this->todo = $todo;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('todos');
    }

    public function broadcastAs(): string
    {
        return 'TodoUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'todo' => [
                'id' => $this->todo->id,
                'title' => $this->todo->title,
                'completed' => $this->todo->completed,
                'created_at' => $this->todo->created_at,
                'updated_at' => $this->todo->updated_at,
            ]
        ];
    }
}
