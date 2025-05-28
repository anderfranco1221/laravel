<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $table = "articles";

    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'category_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'user_id' => 'string',
    ];

    public $resourceType = 'articles';
    /*public function getRouteKeyName()
    {
        return 'slug';
    }*/

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function scopeYear(Builder $query, $year){
        $query->whereYear('created_at', $year);
    }

    public function scopeMonth(Builder $query, $month){
        $query->whereMonth('created_at', $month);
    }

    public function scopeTitle(Builder $query, $value){
        $query->where('title', 'LIKE',  '%' . $value . '%');
    }

    public function scopeContent(Builder $query, $value){
        $query->where('content', 'LIKE',  '%' . $value . '%');
    }

    public function scopeCategories(Builder $query, $categories)
    {
        $categoriesSlugs = explode(',', $categories);

        $query->whereHas('category', function($q) use($categoriesSlugs){
            $q->whereIn('slug', $categoriesSlugs);
        });
    }
}
