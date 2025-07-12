<?php

namespace Tests;

use App\JsonApi\Document;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

trait MakesJsonApiRequests
{
    protected bool $formatJsonApiDocument = true;

    protected bool $addJsonApiHeaders = true;

    protected function setUp(): void
    {
        parent::setUp();

    }

    public function withoutJsonApiDocumentFormatting(): self
    {
        $this->formatJsonApiDocument = false;

        return $this;
    }

    public function withoutJsonApiHeadersFormatting(): self
    {
        $this->addJsonApiHeaders = false;

        return $this;
    }

    public function withoutJsonApiHelpers(): self
    {
        $this->formatJsonApiDocument = false;
        $this->addJsonApiHeaders = false;

        return $this;
    }

    public function json($method, $uri, array $data = [], array $headers = []): TestResponse
    {

        if ($this->addJsonApiHeaders) {
            $headers['accept'] = 'application/vnd.api+json';

            if ($method === 'POST' || $method === 'PATCH') {
                $headers['content-type'] = 'application/vnd.api+json';
            }
        }

        if ($this->formatJsonApiDocument && ! isset($data['data'])) {
            $formattedData = $this->getFormattedDatta($uri, $data);
        }

        return parent::json($method, $uri, $formattedData ?? $data, $headers);
    }

    public function getFormattedDatta($uri, array $data): array
    {
        $path = parse_url($uri)['path'];
        $type = (string) Str::of($path)->after('api/v1/')->before('/');
        $id = (string) Str::of($path)->after($type)->replace('/', '');

        return Document::type($type)
            ->id($id)
            ->attributes($data)
            ->relationshipsData($data['_relationships'] ?? [])
            ->toArray();
    }
}
