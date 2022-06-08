<?php
namespace App\Utils;

class MockRepository
{
    /**
     * @var array
     */
    private array $mockData = [
        'http://localhost/' => [
            '/customer' => [
                'GET' => [
                    'success' => [
                        'error' => 0,
                        "data" => [
                            [
                                "name" => "Ambu Regul",
                                "id" => 165255,
                            ]
                        ],
                    ]
                ]
            ],
            '/customer' => [
                'POST' => [
                    'success' => [
                        'error' => 0,
                        "data" => [
                            "name" => "Ambu Regul",
                            "id" => 165255,
                        ]
                    ]
                ]
            ],
        ]
    ];

    /**
     * @param $method
     * @param $url
     * @param $expectSuccess
     * @return array|mixed
     */
    public function mock($method, $host, $url, $expectSuccess = true)
    {
        $url = explode('?', $url)[0];

        return
            $this->mockData[$host][$url][strtoupper($method)][$expectSuccess ? 'success' : 'error'] ?? [
                'error' => 1,
                'message' => 'Mock response not available'
            ];
    }
}
