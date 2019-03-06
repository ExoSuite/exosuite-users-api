<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\JsonResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Collection;

/**
 * Class Controller
 *
 * @package App\Http\Controllers
 **/
class Controller extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
    use JsonResponses;

    public function alive(): string
    {
        return 'OK';
    }

    /**
     * @param \Illuminate\Support\Collection $collection
     * @param string $modelKey
     * @param string|\Webpatser\Uuid\Uuid $toExceptFromCollection
     * @param bool $isEqualOperator
     * @return \Illuminate\Support\Collection
     */
    protected static function collectionFilterWithExcept(
        Collection $collection,
        string $modelKey,
        $toExceptFromCollection,
        bool $isEqualOperator = false
    ): Collection
    {
        return $collection->filter(static function ($item) use ($toExceptFromCollection, $modelKey, $isEqualOperator) {
            if ($isEqualOperator) {
                return $item->{$modelKey} === $toExceptFromCollection;
            }

            return $item->{$modelKey} !== $toExceptFromCollection;
        });
    }
}
