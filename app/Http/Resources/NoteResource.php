<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => $this->whenLoaded('user', fn() => ['name' => $this->user->name]),
            'task' => $this->whenLoaded('task', fn() => ['title' => $this->task->title]),
            'note' => $this->note,
           'created_at' => $this->created_at,
           'updated_at' => $this->updated_at,
        ];
    }
}
