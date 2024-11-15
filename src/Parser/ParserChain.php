<?php

namespace App\Parser;

class ParserChain
{
    /** @var array */
    private $parsers;

    public function __construct()
    {
        $this->parsers = [];
    }

    /**
     * Add an parser to the chain.
     *
     * @param string $alias
     */
    public function addParser(AbstractParser $parser, $alias): void
    {
        $this->parsers[$alias] = $parser;
    }

    /**
     * Get one parser by alias.
     *
     * @return false|AbstractParser
     */
    public function getParser(string $alias)
    {
        if (\array_key_exists($alias, $this->parsers)) {
            return $this->parsers[$alias];
        }

        return false;
    }

    /**
     * Loop thru all parser to find one that parse the content.
     */
    public function parseAll(string $url): string
    {
        foreach ($this->parsers as $parser) {
            // hack, it'd be nice to actually use this
            // https://github.com/postlight/parser
            if ($parser::class === External::class) {
                continue;
            }
            /** @var Internal $content */
            $content = $parser->parse($url);
            if ($content) {
                dump($content);
                return (string)$content;
            }
        }

        return '';
    }
}
