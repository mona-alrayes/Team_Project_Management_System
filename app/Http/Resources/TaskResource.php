<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'assigned_to' => $this->user ? $this->user->name : null, // Check if user is not null
            'description' => $this->description,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'priority' =>$this->priority,
            'project' => $this->project_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
