<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Rules\Slug;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SaveArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'data.attributes.title' =>  ['required'],
            'data.attributes.slug' =>  [
                'required', 
                'alpha_dash',
                new Slug(),
                Rule::unique('articles', 'slug')->ignore($this->route('article'))],
            'data.attributes.content' =>  ['required'],
            'data.relationships' => []
        ];
    }

    public function validated($key = 'data.attributes', $default = null)
    {
        $data = parent::validated()['data'];
        $attributes = $data['attributes'] ;

        //Valida la relacion enviada por las peticiones post y put
        if(isset($data['relationships'])){
            $relationships = $data['relationships'];
            $categorySlug = $relationships['category']['data']['id'];
            $category = Category::where('slug', $categorySlug)->first();
    
            $attributes['category_id'] = $category->id;
        }

        return $attributes;
    }
}
