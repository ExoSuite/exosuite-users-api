<?php declare(strict_types = 1);

namespace App\Models;

use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileRestrictions extends UuidModel
{

    /** @var string[] */
    protected $fillable = [
        'id',
        'user_id',
        'city',
        'description',
        'birthday',
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }
}
