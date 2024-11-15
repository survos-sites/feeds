<?php

namespace App\Parser;

use Graby\Graby;

/**
 * Retrieve content from an internal library instead of a webservice.
 * It's a fallback by default, but can be the only solution if specified.
 */
class Internal extends AbstractParser
{

    public function __construct(protected Graby $graby)
    {
    }

    public function parse(string $url, bool $reloadConfigFiles = false): string
    {
//        dd($this->graby, $reloadConfigFiles);
        if (true === $reloadConfigFiles) {
            $this->graby->reloadConfigFiles();
        }

        $result = $this->graby->fetchContent($url);
        try {
        } catch (\Exception $e) {
            return '';
        }

        return $result->getHtml() ?? '';
    }
}
