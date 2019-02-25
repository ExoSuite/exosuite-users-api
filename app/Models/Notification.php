<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;

/**
 * Class Notification
 *
 * @package App\Models
 */
class Notification extends UuidModel
{

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at',
        'created_at',
        'updated_at',
    ];
}
