<?php

namespace Api;

use Event;
use MongoId;

/**
 * @RoutePrefix("/api/events")
 */
class EventsController extends ApiController
{

    /**
     * @Get("/")
     */
    public function indexAction()
    {
        $events = Event::find();
        $this->respondWith($events);
    }

    /**
     * @Post("/")
     */
    public function createAction()
    {
        $event = new Event;
        $event->name = $this->request->getPost('name');
        $event->bucket_id = new MongoId($this->request->getPost('bucket_id'));
        $event->data = (array) $this->request->getPost('data');
        $event->save();
        $this->respondWith($event);
    }

}
