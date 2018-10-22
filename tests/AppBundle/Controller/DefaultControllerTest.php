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

        $newCrawler = $client->click($link);

        //Test preparado
        $this->assertGreaterThan(
            9,
            $newCrawler->filter('img')->count()
        );

    }

    public function testSubmitNewFeed()
    {
        $client = static::createClient();

        $crawler = $client->request('POST','/feed/new');

        $form = $crawler->selectButton('Create Feed')->form();

        $client->submit($form);

        $this->assertEquals('AppBundle\Controller\FeedController::newAction', $client->getRequest()->attributes->get('_controller'));
    }

}
