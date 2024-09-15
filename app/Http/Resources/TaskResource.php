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
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => $this->due_date,
            'status_changed_at' => $this->status_changed_at,
            'project_name' => $this->project->name ?? null, 
            'assigned_to' => $this->user->name ?? null,
            'notes' => !is_null($this->notes) && $this->notes->isNotEmpty() ? NoteResource::collection($this->notes) : null,
        ];  
    }
}
