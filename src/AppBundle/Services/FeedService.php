<?php
/**
 * Created by PhpStorm.
 * User: marcos
 * Date: 18/10/18
 * Time: 18:40
 */

namespace AppBundle\Services;

use AppBundle\Entity\Feed;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FeedService
{

    /** Max news loaded */
    const MAX_NEWS = 10;

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
        foreach ($urls as $key => $url){
            $articles = simplexml_load_string(file_get_contents($url));

            $newsNumber=1;

            foreach($articles->channel->item as $article) {

                $imageNews = $this->formatUrlImages($article);
                $descriptionNews = $this->formatUrlDescription($article);

                $feed = new Feed();
                $feed->setTitle($article->title)
                    ->setBody($descriptionNews)
                    ->setImage($imageNews)
                    ->setSource($article->link)
                    ->setPublisher($key);

                $this->save($feed);

                $newsNumber++;

                if ($newsNumber > self::MAX_NEWS) {
                    break;
                }

            }

        }

    }

    /**
     * Get the feeds that were published today.
     *
     * @return array
     */
    public function getTodayFeeds()
    {
        $firstArticles = [];
        $urls = $this->container->getParameter('urls_newspapers');

        $counterNewsPapers = 0;
        foreach ($urls as $key => $url){

            $articles = simplexml_load_string(file_get_contents($url));

            $firstArticles[$counterNewsPapers] = "";

            $newsNumber=1;

            foreach($articles->channel->item as $article) {

                $imageNews = $this->formatUrlImages($article);
                $descriptionNews = $this->formatUrlDescription($article);

                $feed = new Feed();
                $feed->setTitle($article->title)
                    ->setBody($descriptionNews)
                    ->setImage($imageNews)
                    ->setSource($article->link)
                    ->setPublisher($key);

                $newsNumber++;
                $firstArticles[$counterNewsPapers]['item'][] = $feed;

                if ($newsNumber > self::MAX_NEWS) {
                    break;
                }

            }
            $counterNewsPapers++;
        }

        return $firstArticles;
    }


    public function save(Feed $feed)
    {
        $this->entityManager->persist($feed);
        $this->entityManager->flush();
    }

    /**
     * @param $article
     * @return mixed
     */
    public function formatUrlImages($article)
    {
        if($article->enclosure[0]['url'] == null){
            $content = $article->description;
            preg_match_all('/src=([\'"])?(.*?)\\1/', $content, $matches);
            $imageNews = $matches[2][0];
        }else{
            $imageNews = $article->enclosure[0]['url'];
        }

        return $imageNews;
    }

    /**
     * @param $article
     * @return mixed
     */
    public function formatUrlDescription($article)
    {
        if($article->enclosure[0]['url'] == null){
            //$content = $article->description;
            //preg_match_all('/href=([\'"])?(.*?)\\1/', $content, $matches);
            //$descriptionNews = $matches[2][0];
            $descriptionNews = "";
        }else{
            $descriptionNews = $article->description;
        }

        return $descriptionNews;
    }

}