<?php
declare(strict_types=1);

namespace App\Repositories\Elasticsearch\VisitorsFlow;

use App\Aggregates\VisitorsFlow\Aggregate;
use App\Aggregates\VisitorsFlow\DeviceAggregate;
use App\DataTransformer\VisitorsFlow\DeviceFlowCollection;
use App\DataTransformer\VisitorsFlow\ParameterFlowCollection;
use App\DataTransformer\VisitorsFlow\ParametersCollection;
use App\Repositories\Elasticsearch\VisitorsFlow\Contracts\Criteria;
use App\Repositories\Elasticsearch\VisitorsFlow\Contracts\VisitorFlowDeviceRepository;
use Cviebrock\LaravelElasticsearch\Manager as ElasticsearchManager;
use Illuminate\Support\Facades\Log;

class ElasticsearchVisitorFlowDeviceRepository implements VisitorFlowDeviceRepository
{
    const INDEX_NAME = 'device-visitors-flow-index';
    private $client;

    public function __construct(ElasticsearchManager $client)
    {
        $this->client = $client;
    }

    public function save(Aggregate $deviceAggregate): Aggregate
    {
        $params = [
            'index' => self::INDEX_NAME,
            'id' => $deviceAggregate->getId(),
            'type' => '_doc',
            'body' => $deviceAggregate->toArray()
        ];
        Log::info(json_encode($params, JSON_PRETTY_PRINT));
        $this->client->index($params);
        return $deviceAggregate;
    }

    public function getByCriteria(Criteria $criteria): ?DeviceAggregate
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
                            ['match_phrase' => ['device' => $criteria->device]],
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
        return DeviceAggregate::fromResult($result['hits']['hits'][0]['_source']);
    }

    public function getViewsByEachDevice(string $type, int $websiteId): ParametersCollection
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
                    'devices' => [
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
        return new ParametersCollection($result['aggregations']['devices']['buckets']);
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
        return new DeviceFlowCollection($result['hits']['hits']);
    }
}
