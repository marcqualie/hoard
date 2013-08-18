<?php

namespace Hoard\Test;
use Hoard\Test\TestCase;
use Hoard\Payload;

class PayloadTest extends TestCase
{

    public function testNumericEvent()
    {
        $payload = new Payload(array(
            'v' => 1,
            'e' => 404
        ));
        $this->assertSame('404', $payload->event);
    }

}
