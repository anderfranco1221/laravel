<?php
namespace Tests;

use App\JsonApi\Document;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

trait MakesJsonApiRequests
{
    protected bool $formatJsonApiDocument = true;

    protected function setUp(): void
    {
        parent::setUp();

    }

    public function withoutJsonApiDocumentFormatting(){
        $this->formatJsonApiDocument = false;
    }

    /* protected function assertJsonApiValidationErrors(){
        return function($attribute){
            $pointer = Str::of($attribute)->startsWith('data')
                ?"/". str_replace('.', '/', $attribute)
                :"/data/attributes/{$attribute}";

            try{
                $this->assertJsonFragment([
                    'source' => ['pointer' => $pointer]
                ]);
            }catch(ExpectationFailedException $e){
                PHPUnit::fail(
                    "Failed to find a valid JSON:API validation error for key: '{$attribute}'"
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage()
                );
            }

            try{
                $this->assertJsonStructure([
                    'errors' => [
                        ['title', 'detail', 'source' => ['pointer']]
                        ]
                ]);
            }catch(ExpectationFailedException $e){
                PHPUnit::fail(
                    "Failed to find a valid JSON:API error response"
                    .PHP_EOL.PHP_EOL.
                    $e->getMessage()
                );
            }

            $this->assertHeader('content-type', 'application/vnd.api+json')->assertStatus(422);
        };
    } */

    public function json($method, $uri, array $data = [], array $headers = []):TestResponse{
        $headers['accept'] = 'application/vnd.api+json';

        if($this->formatJsonApiDocument){
            $formattedData = $this->getFormattedDatta($uri, $data);
        }

        return parent::json($method, $uri, $formattedData ?? $data, $headers);
    }

    public function postJson($uri, array $data = [], array $headers = []):TestResponse{
        $headers['content-type'] = 'application/vnd.api+json';

        return parent::postJson($uri, $data, $headers);
    }

    public function patchJson($uri, array $data = [], array $headers = []):TestResponse{
        $headers['content-type'] = 'application/vnd.api+json';

        return parent::patchJson($uri, $data, $headers);
    }

    public function getFormattedDatta($uri, array $data):array
    {
        $path = parse_url($uri)['path'];
        $type = (string)Str::of($path)->after('api/v1/')->before('/');
        $id = (string)Str::of($path)->after($type)->replace('/','');

        return Document::type($type)
                ->id($id)
                ->attributes($data)
                ->relationshipsData($data['_relationships'] ?? [])
                ->toArray();

        /* return [
            'data' => array_filter([
                'type' => $type,
                'id'=> $id,
                'attributes' => $data,
                'relationships' => [
                    'category'=> [
                        'data' => [

                        ]
                    ]
                ]
            ])
        ]; */
    }
}
?>
