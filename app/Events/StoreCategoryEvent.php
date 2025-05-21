<?php

namespace App\Events;

use App\Models\Category;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StoreCategoryEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Модель категории
     *
     * @var \App\Models\Category
     */
    public $category;

    /**
     * Create a new event instance.
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
        Log::info('StoreCategoryEvent создан', ['category_id' => $category->id ?? 'unknown']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('store-category'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'store-category';
    }

    public function broadcastWith(): array
    {
        $data = [
            'id' => $this->category->id,
            'title' => $this->category->title,
            'description' => $this->category->description,
            'postCount' => $this->category->posts()->count(),
            'created_at' => $this->category->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->category->updated_at->format('Y-m-d H:i:s')
        ];
        Log::info('StoreCategoryEvent: данные для трансляции подготовлены', [$data]);
        return $data;
    }
}
