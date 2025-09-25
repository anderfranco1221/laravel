<?php

namespace App\JsonApi\Mixins;

use Closure;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonApiQueryBuilder
{
    public function allowedSorts(): Closure
    {
        return function ($allowedSorts) {
            /** @var Builder $this */
            if (request()->filled('sort')) {
                $sortFields = explode(',', request()->input('sort'));

                $allowedSorts = ['title', 'content'];

                foreach ($sortFields as $sortField) {

                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';

                    $sortField = ltrim($sortField, '-');

                    if (! in_array($sortField, $allowedSorts)) {
                        throw new BadRequestHttpException("The sort field '{$sortField}' is not in the '{$this->getResourceType()}' resource.");
                    }

                    $this->orderBy($sortField, $sortDirection);
                }
            }

            return $this;
        };
    }

    public function allowedFilters(): Closure
    {
        return function ($allowedFilters) {
            /** @var Builder $this */
            foreach (request('filter', []) as $filter => $value) {

                // abort_unless(in_array($filter, $allowedFilters), 400);
                if (! in_array($filter, $allowedFilters)) {
                    throw new BadRequestHttpException("The filter '{$filter}' is not in the '{$this->getResourceType()}' resource.");
                }

                $this->hasNamedScope($filter)
                    ? $this->{$filter}($value)
                    : $this->where($filter, 'LIKE', '%'.$value.'%');

            }

            return $this;
        };
    }

    public function allowedIncludes(): Closure
    {
        return function ($allowedIncludes) {
            /** @var Builder $this */
            if (request()->isNotFilled('include')) {
                return $this;
            }

            $includes = explode(',', request()->input('include'));

            foreach ($includes as $include) {
                // abort_unless(in_array($include, $allowedIncludes), 400);
                if (! in_array($include, $allowedIncludes)) {
                    throw new BadRequestHttpException("The include relationship '{$include}' is not in the '{$this->getResourceType()}' resource.");
                }

                $this->with($include);
            }

            return $this;
        };
    }

    public function sparseFieldset(): Closure
    {
        return function () {
            /** @var Builder $this */
            if (request()->isNotFilled('fields')) {
                return $this;
            }

            $resorseType = $this->model->getResourceType();

            $fields = explode(',', request('fields.'.$resorseType));
            $routeKeyName = $this->model->getRoutekeyName();

            if (! in_array($routeKeyName, $fields)) {
                $fields[] = $routeKeyName;
            }

            $fields[] = 'id';

            return $this->addSelect($fields);
        };
    }

    public function jsonPaginate(): Closure
    {

        return function () {
            /** @var Builder $this */
            return $this->paginate(
                $perPage = request('page.size', 15),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = request('page.number', 1)
            )->appends(request()->only('sort', 'filter', 'page.size'));
        };
    }

    public function getResourceType(): Closure
    {
        return function () {
            /** @var Builder $this */
            if (property_exists($this->model, 'resourceType')) {
                return $this->model->resourceType;
            }

            return $this->model->getTable();
        };
    }
}
