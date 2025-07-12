<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\JsonApiResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    use JsonApiResource;

    public function toJsonApi(): array
    {
        return [
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'content' => $this->resource->content,
        ];
    }

    public function getRelationshipLinks(): array
    {
        return ['category', 'author'];
    }

    public function getIncludes(): array
    {
        return array_values(array_filter([
            CategoryResource::make($this->whenLoaded('category')),
            AuthorResources::make($this->whenLoaded('author')),
        ], function ($item) {
            $test = (array) $item;

            return ! $test['resource'] instanceof MissingValue;
        }));
    }
}
