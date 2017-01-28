<?php

declare(strict_types = 1);

namespace LanguageDetection;

/**
 * Class Language
 *
 * @author Patrick Schur <patrick_schur@outlook.de>
 * @package LanguageDetection
 */
class Language extends NgramParser
{
    /**
     * @var array
     */
    private $tokens = [];

    /**
     * Language constructor
     *
     * @param array $lang
     */
    public function __construct(array $lang = [])
    {
        $isEmpty = empty($lang);

        /**
         * @var \GlobIterator $json
         */
        foreach (new \GlobIterator(__DIR__ . '/../../etc/*.json') as $json)
        {
            if ($isEmpty || in_array($json->getBasename('.json'), $lang))
            {
                $content = file_get_contents($json->getPathname());
                $this->tokens = array_merge($this->tokens, json_decode($content, true));
            }
        }
    }

    /**
     * @param string $str
     * @return LanguageResult
     */
    public function detect(string $str): LanguageResult
    {
        $str = mb_strtolower($str);

        $samples = $this->getNgrams($str);

        $result = [];

        foreach ($this->tokens as $lang => $value)
        {
            $index = $sum = 0;
            $value = array_flip($value);

            foreach ($samples as $v)
            {
                if (isset($value[$v]))
                {
                    $x = $index++ - $value[$v];
                    $y = $x >> (PHP_INT_SIZE * 8);
                    $sum += ($x + $y) ^ $y;
                    continue;
                }

                $sum += $this->maxNgrams;
                ++$index;
            }

            $result[$lang] = 1 - ($sum / ($this->maxNgrams * $index));
        }

        arsort($result, SORT_NUMERIC);

        return new LanguageResult($result);
    }
}