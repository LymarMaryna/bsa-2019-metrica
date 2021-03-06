<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Entities\User;
use App\Entities\Website;

class CreateVisitorTest extends TestCase
{
    use RefreshDatabase;

    private $website;

    const VALID_TRACKING_NUMBER = '00000111';
    const INVALID_TRACKING_NUMBER = '00000222';
    const ENDPOINT = '/api/v1/visitors';

    protected function setUp(): void
    {
        parent::setUp();
        factory(User::class)->create();
        $this->website = factory(Website::class)->create([
            'tracking_number' => 111
        ]);
    }

    public function test_create_visitor_no_header()
    {
        $this->postJson(self::ENDPOINT, [], [
            'Origin' => $this->website->domain
        ])
            ->assertStatus(400)
            ->assertJson([
                'error' => [
                    'message' => 'x-website header is required'
                ]
            ]);
    }

    public function test_create_visitor_wrong_header()
    {
        $this->postJson(self::ENDPOINT, [], [
            'x-website' => self::INVALID_TRACKING_NUMBER,
            'Origin' => $this->website->domain
        ])
            ->assertStatus(404)
            ->assertJson([
                'error' => [
                    'message' => 'Website not found.'
                ]
            ]);
    }

    public function test_create_visitor_valid_header()
    {
        $this->postJson(self::ENDPOINT, [], [
            'x-website' => self::VALID_TRACKING_NUMBER,
            'Origin' => $this->website->domain
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'token'
                ],
                'meta' => []
            ]);
    }
}


