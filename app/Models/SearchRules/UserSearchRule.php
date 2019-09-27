<?php declare(strict_types = 1);

namespace App\Models\SearchRules;

use ScoutElastic\SearchRule;

/**
 * Class UserSearchRule
 *
 * @package App\Models\SearchRules
 */
class UserSearchRule extends SearchRule
{

    /**
     * @inheritdoc
     */
    public function buildQueryPayload()
    {
        $query = $this->builder->query;

        return [
            'must' => [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['first_name', 'last_name', 'nick_name'],
                    'type' => 'phrase_prefix',
                ],
            ],
        ];
    }
}
