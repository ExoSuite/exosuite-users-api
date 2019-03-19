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
            'should' => [
                [
                    'match' => [
                        'first_name' => [
                            'query' => $query,
                        ],
                    ],
                ],
                [
                    'match' => [
                        'last_name' => [
                            'query' => $query,
                        ],
                    ],
                ],
                [
                    'match' => [
                        'nick_name' => [
                            'query' => $query,
                        ],
                    ],
                ],
            ],
        ];
    }
}
