<?php

/**
 * @RoutePrefix('/buckets')
 */
class BucketsController extends BaseController
{

    /**
     * @Get('/')
     */
    public function indexAction()
    {
        $buckets = $this->authUser->getBuckets();
        $this->view->setVar('buckets', $buckets);
    }

    /**
     * @Get("/{id:[a-zA-Z0-9]+}/events", name="bucket-events")
     */
    public function eventsAction($id)
    {
        $bucket = Bucket::findById($id);
        $this->view->setVar('bucket', $bucket);
    }

    /**
     * @Get("/{id:[a-zA-Z0-9]+}")
     */
    public function showAction($id)
    {
        $bucket = Bucket::findById($id);
        $this->view->setVar('bucket', $bucket);
    }

    /**
     * @Get('/new')
     */
    public function newAction()
    {
    }

    /**
     * @Post('/')
     */
    public function createAction()
    {
    }

}
