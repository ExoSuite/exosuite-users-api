<?php declare(strict_types = 1);

namespace App\Models;

use App\Http\Controllers\Time\TimeController;
use App\Models\Abstracts\UuidModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserRun extends UuidModel
{

    /** @var string[] */
    protected $fillable = [
        "id",
        "run_id",
        "user_id",
        "final_time",
    ];

    public function run(): BelongsTo
    {
        return $this->belongsTo(Run::class);
    }

    public function times(): HasMany
    {
        return $this->hasMany(Time::class)->limit(TimeController::GET_PER_PAGE);
    }
}
