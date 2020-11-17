<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class MigrationModel extends Model
{
    protected $table = 'migrations';

    protected $attributes = [
        //'related'
    ];

    protected $relations = [
        'related'
    ];

    public function related()
    {
        return $this->belongsTo(MigrationModel::class, 'related_id', 'id');
    }
}
