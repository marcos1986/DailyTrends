<?php

namespace AppBundle\Controller;

use AppBundle\Services\FeedService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * First function called when init the application
     *
     * @Route("/", name="homepage")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $this->get(FeedService::class)->getMainFeed();
        $feed = $this->get(FeedService::class)->getTodayFeeds();

        return $this->render('default/home.html.twig', ['feed' => $feed]);
    }
}
