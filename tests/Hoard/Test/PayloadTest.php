<?php

namespace Hoard\Test;
use Hoard\Test\TestCase;
use Hoard\Payload;

class PayloadTest extends TestCase
{


    /**
     * All events must be cast to strings as they come in
     * @return [type] [description]
     */
    public function testNumericEvent()
    {
        $payload = new Payload(array(
            'v' => 1,
            'e' => 404
        ));
        $this->assertSame('404', $payload->event);
    }


    /**
     * Currently only version 1 is supported
     */
    public function testValidVersions()
    {
        $payload = new Payload(array(
            'v' => 0
        ));
        $this->assertFalse($payload->isVersionSupported());
        $payload = new Payload(array(
            'v' => 1
        ));
        $this->assertTrue($payload->isVersionSupported());
        $payload = new Payload(array(
            'v' => 2
        ));
        $this->assertFalse($payload->isVersionSupported());
    }
}
