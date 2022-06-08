<?php

namespace App\Utils;

class CommunicationLogService
{
    private $app;
    private $model=null;
    private $startTime=null;
    public function __construct($app)
    {
        $this->app = $app;
        if(!$this->model){
            $this->model = new \App\Models\CommunicationLog();
        }
    }

    /**
     * @return null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param null $startTime
     */
    public function setStartTime($startTime): void
    {
        $this->startTime = $startTime;
    }

    /**
     * @return null
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    public function initialize()
    {
        $this->model = new \App\Models\CommunicationLog();
        $this->model->method = app()['request']->method();
        $this->model->url = app()['request']->path();
        $this->model->request = [
            'header' => app()['request']->headers->all(),
            'input' => app()['request']->all(),
        ];
    }


    public function commit($data=[])
    {
        $this->model->fill([
            'load_time' => microtime(true) - $this->startTime,
            'response' => $data,
            'executor_identity' => isset(app()['request']->user()->id) ? (string) app()['request']->user()->id : NULL,
        ]);
        if($this->model->url) {
            $this->model->save();
        }
    }
}
