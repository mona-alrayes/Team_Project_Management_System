<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'tasks' => !is_null($this->tasks) && $this->tasks->isNotEmpty() ? TaskResource::collection($this->tasks) : null,
            'users' => !is_null($this->users) && $this->users->isNotEmpty() ? UserResource::collection($this->users) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
