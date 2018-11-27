<?php

namespace App\Models\SearchRules;

use ScoutElastic\SearchRule;

class UserSearchRule extends SearchRule
{
    /**
     * @inheritdoc
     */
    public function buildHighlightPayload()
    {
        //
    }

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
                            'query' => $query
                        ]
                    ]
                ],
                [
                    'match' => [
                        'last_name' => [
                            'query' => $query
                        ]
                    ]
                ],
                [
                    'match' => [
                        'nick_name' => [
                            'query' => $query
                        ]
                    ]
                ]
            ]
        ];
    }
}
