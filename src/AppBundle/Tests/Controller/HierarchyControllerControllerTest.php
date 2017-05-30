<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HierarchyControllerControllerTest extends WebTestCase
{
    public function testAddcatalog()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'catalog');
    }

}
