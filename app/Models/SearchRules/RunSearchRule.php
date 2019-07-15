<?php declare(strict_types = 1);

namespace App\Models\SearchRules;

use App\Models\Run;
use Illuminate\Support\Arr;
use ScoutElastic\SearchRule;

/**
 * Class UserSearchRule
 *
 * @package App\Models\SearchRules
 */
class RunSearchRule extends SearchRule
{

    /**
     * @inheritdoc
     */
    public function buildQueryPayload()
    {
        $query = $this->builder->query;

        return [
            'must'  => [
                'multi_match'   => [
                    'query'     => $query,
                    'fields'    => Arr::add(Run::getSearchableFields(), "user", ["nick_name"]),
                    'type'      => 'phrase_prefix',
                ],
            ],
        ];
    }
}
