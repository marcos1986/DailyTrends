<?php
/**
 * Created by PhpStorm.
 * User: marcos
 * Date: 18/10/18
 * Time: 18:40
 */

namespace AppBundle\Services;

use AppBundle\Entity\Feed;
use AppBundle\Factory\FeedFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FeedService
{

    /** Max news loaded */
    const  MAX_NEWS = 5;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ContainerInterface */
    private $container;

    /**
     * FeedService constructor.
     * @param EntityManagerInterface $entityManager
     * @param ContainerInterface $container
     */
    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /** Get main feeds from source and keep them into the database */
    public function getMainFeed()
    {
        $urls = $this->container->getParameter('urls_newspapers');

        foreach ($urls as $url){
            $articles = simplexml_load_string(file_get_contents($url));

            $newsNumber=1;

            foreach($articles->channel->item as $article) {

                $feed = FeedFactory::buildFeedFromSource($article);

                $this->save($feed);

                $newsNumber++;

                if ($newsNumber > self::MAX_NEWS) {
                    break;
                }

            }

        }

    }

    /**
     * Get the 5 first feeds for each newspaper that were published today.
     *
     * @return ArrayCollection
     */
    public function getTodayFeeds()
    {
        $urls = $this->container->getParameter('urls_newspapers');

        return $firstArticles = FeedFactory::buildFeedFromSources($urls);
    }


    public function save(Feed $feed)
    {
        $this->entityManager->persist($feed);
        $this->entityManager->flush();
    }

}