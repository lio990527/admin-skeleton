<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * @method static Illuminate\Database\Eloquent\Builder|Illuminate\Database\Query\Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static \Illuminate\Database\Eloquent\Collection|static[]|static|null find($id, $columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Collection|static[]|static|null findOrFail($id, $columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Collection|static[]|static|null findMany($ids, $columns = ['*'])
 * @method static static findOrNew($id, $columns = ['*'])
 * @method static static firstOrNew(array $attributes = [], array $values = [])
 * @method static static firstOrCreate(array $attributes = [], array $values = [])
 */
abstract class Model extends EloquentModel
{

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

}