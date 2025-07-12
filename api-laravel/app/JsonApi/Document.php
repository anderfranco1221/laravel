<?php

namespace App\JsonApi;

use Illuminate\Support\Collection;

/**
 * Construlle la structura del json con el tipo de dato Collection
 */
class Document extends Collection
{
    /**
     * Tipo del objeto que se esta devolviendo
     */
    public static function type($type): Document
    {
        return new self([
            'data' => [
                'type' => $type,
            ],
        ]);
    }

    /**
     * Identificador del objeto
     */
    public function id($id): Document
    {
        if ($id) {
            $this->items['data']['id'] = (string) $id;
        }

        return $this;
    }

    /**
     * Los atributtos del objeto
     */
    public function attributes(array $attributes): Document
    {
        unset($attributes['_relationships']);

        $this->items['data']['attributes'] = $attributes;

        return $this;
    }

    /**
     * Los links del modelo
     */
    public function links(array $links): Document
    {
        $this->items['data']['links'] = $links;

        return $this;
    }

    public function relationshipsData(array $relationships): Document
    {
        foreach ($relationships as $key => $relationship) {
            $this->items['data']['relationships'][$key]['data'] = [
                'type' => $relationship->getResourceType(),
                'id' => $relationship->getRouteKey(),
            ];
        }

        return $this;
    }

    public function relationshipsLinks(array $relationships): Document
    {
        foreach ($relationships as $key) {
            $this->items['data']['relationships'][$key]['links'] = [
                'self' => route("api.v1.{$this->items['data']['type']}.relationships.{$key}", $this->items['data']['id']),
                'related' => route("api.v1.{$this->items['data']['type']}.{$key}", $this->items['data']['id']),
            ];
        }

        return $this;
    }
}
