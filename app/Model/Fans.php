<?php

declare(strict_types=1);

namespace App\Model;

/**
 * @property int $id 
 * @property int $user_id 
 * @property int $fans_user_id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class Fans extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'fans';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'user_id',
        'fans_user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer', 
        'user_id' => 'integer', 
        'fans_user_id' => 'integer', 
        'created_at' => 'datetime', 
        'updated_at' => 'datetime'
    ];
}
