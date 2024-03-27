<?php
namespace App\JsonApi;

use Closure;
use Illuminate\Support\Str;
use PHPUnit\Framework\ExpectationFailedException;


    class JsonApiTestResponse
    {
        public function assertJsonApiValidationErrors(): Closure{

            /** @var Builder $this */
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
        }
    }
?>