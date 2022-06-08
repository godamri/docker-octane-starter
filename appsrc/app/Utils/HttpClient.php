<?php

namespace App\Utils;

use App\Jobs\OutgoingHttpLogJob;
use App\Utils\Accurate\AccurateCommunication;
use Carbon\Carbon;
use GuzzleHttp\Client;

class HttpClient
{

    private $url;
    private $method;
    private $header = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];
    private ?array $params;
    private bool $logEnabled;
    private bool $trace=false;
    private bool $rawRequest;
    private ?array $clientOption; // = [ 'http_errors' => false, 'verify' => ( env('APP_ENV') == 'local' ? false:true ) ];
    private bool $responseAsArray = false;
    private ?string $lastResponse=null;
    private ?string $baseHost=null;
    private bool $mockResponse=false;
    private bool $runExtraCheck=false;
    private ?array $extraChekVar=null;
    private $extraCheckCallback=null;

    function __construct($config=['log' => false, 'array_resp' => true])
    {
        $this->request = app()->request;
        $this->logEnabled = false !== env('API_LOGGER', false);
        $this->rawRequest = false;
        if( isset($config['log']) ) {
            $this->logEnabled = $config['log'];
        }
        if( isset($config['array_resp']) ) {
            $this->responseAsArray = $config['array_resp'];
        }
        $this->clientOption = [ 'http_errors' => false, 'verify' => false ];
    }

    /**
     * @param null $baseHost
     */
    public function setBaseHost($baseHost): void
    {
        $this->baseHost = $baseHost;
    }

    /**
     * @param bool $mockResponse
     */
    public function mockResponse(bool $mockResponse)
    {
        $this->mockResponse = $mockResponse;
        return $this;
    }

    /**
     * @param bool $runExtraCheck
     */
    public function setRunExtraCheck(bool $runExtraCheck, $check, $callback=null): void
    {
        $this->runExtraCheck = $runExtraCheck;
        $this->extraChekVar = $check;
        $this->extraCheckCallback = $callback;
    }

    public function option($option=[])
    {
        $this->clientOption = array_merge($this->clientOption, $option);
        return $this;
    }

    public function logIs($log=true){
        $this->logEnabled = $log;
        return $this;
    }
    public function trace($trace = false){
        $this->trace = $trace;
        return $this;
    }
    public function raw($raw=true)
    {
        $this->rawRequest = $raw;
        return $this;
    }

    public function __call($method, array $args)
    {
        $this->method = strtolower($method);
        $this->url = $args[0] ?? false;
        $this->params = $args[1] ?? [];
        $this->header = array_merge($this->header, ($args[2] ?? []) );

        if( !in_array(strtolower($this->method), ['post', 'get', 'put', 'patch', 'delete', 'head', 'options']) )
            return (object) [ 'error' => 'invalid method' ];

        return $this->buzz();
    }

    public function pushHeader(array $header): void
    {
        $this->header = array_merge($this->header, $header);
    }

    private function buzz()
    {
        $url = $this->url;
        if(stripos($this->url, 'http') !== 0) {
            $url = ($this->baseHost) ? $this->baseHost . $this->url : $this->url;
        }
        $client = new Client($this->clientOption);
        if( ! isset($this->header['Accept']) ) {
            $this->header['Accept'] = 'application/json';
        }
        if( ! isset($this->header['Authorization']) ) {
            $this->header['Content-Type'] = 'application/json';
        }
        $options['headers'] = $this->header;

        if('get' !== strtolower($this->method) ) {
            if ($options['headers']['Content-Type'] !== 'application/json') {
                $options['form_params'] = $this->params;
            } else {
                $options['json'] = $this->params;
            }
        }
        try {
            $starttime = microtime(true);

            if($this->rawRequest) {
                return $client->{$this->method}($url, $options)->getBody()->getContents() ?? '{"error":1}';
            }

            $response = $client->{$this->method}($url, $options);
            $endtime = microtime(true);
            $body = $response->getBody()->getContents();
            $this->lastResponse = $body;
            if($this->trace) {
                \Log::channel('tracer')->info(
                    sprintf("\n %s load time : %s [ %s - %s ]",
                        $this->trace,
                        $endtime - $starttime . 's',
                        Carbon::createFromTimestampMs($starttime)->format('H:i:s.u'),
                        Carbon::createFromTimestampMs($endtime)->format('H:i:s.u')
                    )
                );
                $this->trace = false;
            }
            if($this->mockResponse && config('app.env') !== 'production') {
                return (new MockRepository())->mock($this->method, $this->baseHost, $this->url);
            }
            $r = json_decode($body, true, 512, JSON_THROW_ON_ERROR | JSON_ERROR_NONE);
            if($this->runExtraCheck) {
                if ( isset($r[$this->extraChekVar['key']]) && isset($r[$this->extraChekVar['key']]) && $r[$this->extraChekVar['key']] === $this->extraChekVar['value'] ) {
                    if(is_callable($this->extraCheckCallback)) {
                        $this->extraCheckCallback();
                    }
                }
            }

            if($this->logEnabled) {
                dispatch(new OutgoingHttpLogJob(
                    $this->url,
                    $this->method,
                    [
                        'body' => $this->params,
                        'request' => $this->header,
                    ],
                    [
                        'body' => $body,
                        'headers' => $response->getHeaders(),
                    ]
                ));
            }

            return $this->responseAsArray ? $r : (object) $r;

        }
        catch (\Exception $e) {
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());
            if($this->logEnabled) {
                dispatch(new OutgoingHttpLogJob(
                    $this->url,
                    $this->method,
                    [
                        'body' => $this->params,
                        'request' => $this->header,
                    ],
                    [
                        'body' => $e->getMessage(),
                        'headers' => [],
                    ]
                ));
            }
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * @return string[]
     */
    public function getLastHeaders(): array
    {
        return $this->header;
    }
}
