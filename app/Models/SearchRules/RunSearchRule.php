<?php declare(strict_types = 1);

namespace App\Models\SearchRules;

use App\Models\Run;
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
                    'fields'    => array_merge(Run::getSearchableFields(), Run::getUserSearchableFields()),
                    'type'      => 'phrase_prefix',
                ],
            ],
        ];
    }
}
