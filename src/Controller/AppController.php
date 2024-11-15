<?php

namespace App\Controller;

use Graby\SiteConfig\SiteConfig;
use GrabySiteConfig\SiteConfig\Files;
use Psr\Log\LoggerInterface;
use Readability\Readability;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends AbstractController
{
    #[Route('/app_config', name: 'app_config_files')]
    #[Template("app/index.html.twig")]
    public function configFiles(LoggerInterface $logger): Response|array
    {

        $url = 'http://www.medialens.org/index.php/alerts/alert-archive/alerts-2013/729-thatcher.html';
        $url = 'https://www.cspdailynews.com/tobacco/new-york-city-files-lawsuit-against-disposable-flavored-e-cigarette-maker';

// you can use whatever you want to retrieve the html content (Guzzle, Buzz, cURL ...)
        $html = file_get_contents($url);

        $readability = new Readability($html, $url);
        $readability->setLogger($logger);
        $result = $readability->init();

        if ($result) {
            // display the title of the page
//            echo $readability->getTitle()->textContent;
            // display the *readability* content
//            echo $readability->getContent()->textContent;
        } else {
            echo 'Looks like we couldn\'t find the content. :(';
        }

        return [
            'readability' => $readability,
            'url' => $url,
        ];

    }
}
