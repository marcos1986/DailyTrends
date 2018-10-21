<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        //capture link news
        $link = $crawler->filter('a:contains("News")')->link();

        var_dump($link);
        $newCrawler = $client->click($link);

        //Test preparado
        $this->assertGreaterThan(
            31,
            $newCrawler->filter('img')->count()
        );

        //$this->assertEquals(200, $client->getResponse()->getStatusCode());
        //$this->assertContains('Welcome to Symfony',
            //$crawler->filter('#container h1')->text());
    }
}
