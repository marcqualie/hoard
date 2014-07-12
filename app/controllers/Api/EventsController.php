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

        $payload = $this->request->getJsonRawBody();

        $event = new Event;
        $event->name = $payload->name;
        $event->bucket_id = new MongoId($payload->bucket_id);
        $event->data = $payload->data;
        $event->save();
        $this->respondWith($event);
    }

}
