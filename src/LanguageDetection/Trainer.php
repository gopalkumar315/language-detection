<?php

declare(strict_types = 1);

namespace LanguageDetection;

/**
 * Class Trainer
 *
 * @author Patrick Schur <patrick_schur@outlook.de>
 * @package LanguageDetection
 */
class Trainer extends NgramParser
{
    public function learn()
    {
        /**
         * @var \GlobIterator $txt
         */
        foreach (new \GlobIterator(__DIR__ . '/../../etc/*.txt') as $txt)
        {
            $content = file_get_contents($txt->getPathname());
            $content = mb_strtolower($content);

            echo $txt->getBasename('.txt'), PHP_EOL;

            file_put_contents(
                substr_replace($txt->getPathname(), 'json', -3),
                json_encode([$txt->getBasename('.txt') => $this->getNgrams($content)], JSON_UNESCAPED_UNICODE)
            );
        }
    }
}