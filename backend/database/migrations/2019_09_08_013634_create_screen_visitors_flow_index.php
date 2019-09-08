<?php

use Illuminate\Database\Migrations\Migration;

class CreateScreenVisitorsFlowIndex extends Migration
{
    private const INDEX_NAME = 'screen-visitors-flow-index';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $client = app('elasticsearch');
        $params = [
            'index' => self::INDEX_NAME,
            'body' => [
                'mappings' => [
                    "properties" => [
                        "screen" => [
                            "type" => "keyword"
                        ]
                    ]
                ]
            ]
        ];
        $client->indices()->create($params);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $client = app('elasticsearch');
        $params = ['index' => self::INDEX_NAME];
        $client->indices()->delete($params);
    }

}
