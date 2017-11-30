<?php

namespace Mapbender\CoordinatesUtilityBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CoordinatesUtilityControllerTest extends WebTestCase
{
    /**
     * Test SRS autocomplete
     */
    public function testSrsAutocompleteAction()
    {
        $client = static::createClient([], [
            'HTTP_HOST' => '127.0.0.1:8000',
        ]);

        $client
            ->request(
                'GET',
                '/srs-autocomplete?term=EPSG:4647'
            );

        $response = $client->getResponse()->getContent();
        $expected = 'EPSG:4647 | ETRS89 \/ UTM zone N32';

        $this->assertContains($expected, $response);
    }
}
