<?php

namespace App\Jobs;

use App\Models\CommunicationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OutgoingHttpLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $url;
    private $method;
    private $request;
    private $response;
    public function __construct($url, $method, $request, $response)
    {
        $this->url = $url;
        $this->method = $method;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        CommunicationLog::create([
            'method' => $this->method,
            'url' => $this->url,
            'request' => $this->request,
            'response' => $this->response,
        ]);
    }
}
