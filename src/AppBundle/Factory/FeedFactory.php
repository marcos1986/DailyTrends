<?php
/**
 * Created by PhpStorm.
 * User: marcos
 * Date: 22/10/18
 * Time: 0:06
 */

namespace AppBundle\Factory;

use AppBundle\Entity\Feed;
use AppBundle\Services\FeedService;
use Doctrine\Common\Collections\ArrayCollection;

class FeedFactory
{

    /**
     * @param $urls
     * @return ArrayCollection
     */
    public static function buildFeedFromSources($urls)
    {

        $firstArticles = new ArrayCollection();

        foreach ($urls as $url){

            $articles = simplexml_load_string(file_get_contents($url));

            $newsNumber=1;

            foreach($articles->channel->item as $article) {

                $feed = FeedFactory::buildFeedFromSource($article);

                if(empty($feed)){
                    continue;
                }

                $newsNumber++;
                $firstArticles->add($feed);

                if ($newsNumber > FeedService::MAX_NEWS) {
                    break;
                }
            }
        }
        return $firstArticles;
    }

    /**
     * Create object from article
     *
     * @param $article
     * @return Feed
     */
    public static function buildFeedFromSource($article)
    {
        $imageNews = self::extractUrlImages($article);
        $descriptionNews = self::extractUrlDescription($article);
        $publisherNews = self::extractUrlPublisher($article);

        $feed = new Feed();
        return $feed->setTitle($article->title)
            ->setBody($descriptionNews)
            ->setImage($imageNews)
            ->setSource($article->link)
            ->setPublisher($publisherNews);
    }

    /**
     * Extract embebed url from description
     * @param $article
     * @return mixed
     */
    public static function extractUrlImages($article)
    {
        if($article->enclosure[0]['url'] === null){
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
    public static function extractUrlDescription($article)
    {
        if($article->enclosure[0]['url'] === null){
            //$content = $article->description;
            //preg_match_all('/href=([\'"])?(.*?)\\1/', $content, $matches);
            //$descriptionNews = $matches[2][0];
            $descriptionNews = "";
        }else{
            $descriptionNews = $article->description;
        }

        return $descriptionNews;
    }

    /**
     * @param $article
     * @return mixed
     */
    public static function extractUrlPublisher($article)
    {
        $publisher = parse_url($article->link);

        return $publisher['host'];
    }

}