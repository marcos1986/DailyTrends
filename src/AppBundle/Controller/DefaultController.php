<?php

namespace AppBundle\Controller;

use AppBundle\Services\FeedService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $feed = $this->get(FeedService::class)->getMainFeed();
        //$feed = $this->get(FeedService::class)->getTodayFeeds();

        return $this->render('default/home.html.twig', ['feed' => $feed]);
    }
}
