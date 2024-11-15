<?php

namespace App\EventListener;

use App\Event\ItemsCachedEvent;
use Http\Client\Common\HttpMethodsClientInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ItemSubscriber
{
    /** @var string */
    protected $hub = '';
    /** @var RouterInterface */
    protected $router;
    /** @var HttpMethodsClientInterface */
    protected $client;

    /**
     * Create a new subscriber.
     *
     * @param string                     $hub    A hub (url) to ping
     * @param RouterInterface            $router Symfony Router to generate the feed xml
     * @param HttpMethodsClientInterface $client Guzzle client to send the request
     */
    public function __construct(string $hub, RouterInterface $router, HttpMethodsClientInterface $client)
    {
        $this->hub = $hub;
        $this->router = $router;
        $this->client = $client;
    }

    /**
     * Ping available hub when new items are cached.
     *
     * http://nathangrigg.net/2012/09/real-time-publishing/
     */
    public function pingHub(ItemsCachedEvent $event): bool
    {
        if (empty($this->hub)) {
            return false;
        }

        // retrieve feed urls
        $slugs = $event->getFeedSlugs();

        $urls = [];
        foreach ($slugs as $slug) {
            $urls[] = $this->router->generate(
                'feed_xml',
                ['slug' => $slug],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        // ping publisher
        // https://github.com/pubsubhubbub/php-publisher/blob/master/library/Publisher.php
        $params = 'hub.mode=publish';
        foreach ($urls as $url) {
            $params .= '&hub.url=' . $url;
        }

        try {
            $response = $this->client->post(
                $this->hub,
                [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'User-Agent' => 'PubSubHubbub-Publisher-PHP/1.0',
                ],
                $params
            );

            // hub should response 204 if everything went fine
            return !(204 !== $response->getStatusCode());
        } catch (\Exception $e) {
            return false;
        }
    }
}
