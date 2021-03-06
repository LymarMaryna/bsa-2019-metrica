<?php
declare(strict_types=1);

namespace App\Repositories\Elasticsearch\VisitorsFlow;

use App\Aggregates\VisitorsFlow\Aggregate;
use App\Aggregates\VisitorsFlow\CountryAggregate;
use App\DataTransformer\VisitorsFlow\CountryFlowCollection;
use App\DataTransformer\VisitorsFlow\ParameterFlowCollection;
use App\DataTransformer\VisitorsFlow\ParametersCollection;
use App\Repositories\Elasticsearch\VisitorsFlow\Contracts\Criteria;
use App\Repositories\Elasticsearch\VisitorsFlow\Contracts\VisitorFlowCountryRepository;
use Cviebrock\LaravelElasticsearch\Manager as ElasticsearchManager;
use Illuminate\Support\Facades\Log;

final class ElasticsearchVisitorFlowCountryRepository implements VisitorFlowCountryRepository
{
    const INDEX_NAME = 'country-visitors-flow-index';
    private $client;

    public function __construct(ElasticsearchManager $client)
    {
        $this->client = $client;
    }

    public function save(Aggregate $countryAggregate): Aggregate
    {
        $params = [
            'index' => self::INDEX_NAME,
            'id' => $countryAggregate->getId(),
            'type' => '_doc',
            'body' => $countryAggregate->toArray()
        ];
        Log::info(json_encode($params, JSON_PRETTY_PRINT));
        $this->client->index($params);

        return $countryAggregate;
    }

    public function getByCriteria(Criteria $criteria): ?CountryAggregate
    {
        $params = [
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' => ['website_id' => $criteria->websiteId]],
                        ],
                        'filter' => [
                            ['term' => ['level' => $criteria->level]],
                            ['match_phrase' => ['target_url' => $criteria->targetUrl]],
                            ['match_phrase' => ['country' => $criteria->country]],
                            ['match_phrase' => ['prev_page.source_url' => $criteria->prevPageUrl]]
                        ],
                    ]
                ]
            ]
        ];
        Log::info(json_encode($params, JSON_PRETTY_PRINT));
        try {
            $result = $this->client->search($params);
        } catch (\Exception $exception) {
            return null;
        }
        if (empty($result['hits']['hits'])) {
            return null;
        }
        return CountryAggregate::fromResult($result['hits']['hits'][0]['_source']);
    }

    public function getViewsByEachCountry(string $type, int $websiteId): ParametersCollection
    {
        $params = [
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' => ['website_id' => $websiteId]],
                        ]
                    ]
                ],
                'aggregations' => [
                    'countries' => [
                        'terms' => [
                            'field' => $type
                        ],
                        'aggregations' => [
                            'views' => [
                                'sum' => [
                                    'field' => 'views'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        Log::info(json_encode($params, JSON_PRETTY_PRINT));
        $result = $this->client->search($params);
        return new ParametersCollection($result['aggregations']['countries']['buckets']);
    }

    public function getFlow(int $websiteId, int $level): ParameterFlowCollection
    {
        $params = [
            'index' => self::INDEX_NAME,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['match' => ['website_id' => $websiteId]],
                        ],
                        'filter' => [
                            $level > 2 ? ['term' => ['level' => $level]] : [
                                'range' => [
                                    "level" => [
                                        'gte' => 1,
                                        'lte' => 2
                                    ]
                                ]
                            ],
                        ]
                    ]
                ],
                'sort' => [
                    ['level' => ['order' => 'asc', "unmapped_type" => "integer"]],
                    ['views' => ['order' => 'desc', "unmapped_type" => "integer"]]
                ]
            ]
        ];
        Log::info(json_encode($params, JSON_PRETTY_PRINT));
        $result = $this->client->search($params);
        return new CountryFlowCollection($result['hits']['hits']);
    }
}
