# language-detection
| Build Status | Code Coverage | Version | Total Downloads | Maintenance | Minimum PHP Version | License |
| :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| [![Build Status](https://travis-ci.org/patrickschur/language-detection.svg?branch=master)](https://travis-ci.org/patrickschur/language-detection) | [![codecov](https://codecov.io/gh/patrickschur/language-detection/branch/master/graph/badge.svg)](https://codecov.io/gh/patrickschur/language-detection) | [![Version](https://img.shields.io/packagist/v/patrickschur/language-detection.svg?style=flat-square)](https://packagist.org/packages/patrickschur/language-detection) | [![Total Downloads](https://img.shields.io/packagist/dt/patrickschur/language-detection.svg?style=flat-square)](https://packagist.org/packages/patrickschur/language-detection) | [![Maintenance](https://img.shields.io/maintenance/yes/2017.svg?style=flat-square)](https://github.com/patrickschur/language-detection) | [![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-4AC51C.svg?style=flat-square)](http://php.net/) | [![License](https://img.shields.io/packagist/l/patrickschur/language-detection.svg?style=flat-square)](https://opensource.org/licenses/MIT) |

This library can detect the language of a given text string.
It can parse given training text in many different idioms into a sequence of [N-grams](https://en.wikipedia.org/wiki/N-gram) and builds a database file in JSON format to be used in the detection phase.
Then it can take a given text and detect its language using the database previously generated in the training phase.
The library comes with text samples used for training and detecting text in 106 languages.

## Table of Contents
- [Installation using Composer](#installation-using-composer)
- [Basic Usage](#basic-usage)
- [\_\_construct()](#__construct)
- [whitelist()](#whitelist)
- [blacklist()](#blacklist)
- [bestResults()](#bestresults)
- [limit()](#limit)
- [close()](#close)
- [validate()](#validate)
- [\_\_toString()](#__tostring)
- [Method Chaining](#method-chaining)
- [JsonSerializable](#jsonserializable)
- [IteratorAggregate](#iteratoraggregate)
- [ArrayAccess](#arrayaccess)
- [List of supported languages](#supported-languages)

## Installation using Composer
```bash
$ composer require patrickschur/language-detection
```
> **Note:** This library requires the [Multibyte String](http://php.net/manual/en/book.mbstring.php) extension in order to work. 

## Basic Usage
If you have added your own files, you must first generate a language profile. 
Otherwise skip this step.

```php
use LanguageDetection\Trainer;
 
$t = new Trainer();
 
$t->learn();
```
 
Now, we can classify texts by their language.
To detect the language correctly, the length of the input text should be at least some sentences.
 
```php
use LanguageDetection\Language;
 
$ld = new Language;
 
$ld->detect('Mag het een onsje meer zijn?')->close();
```
Result:
```text
Array
(
    "nl" => 0.65733333333333,
    "af" => 0.50994444444444,
    "br" => 0.49177777777778,
    "nb" => 0.48533333333333,
    "nn" => 0.48422222222222,
    "fy" => 0.47361111111111,
    "dk" => 0.46855555555556,
    "sv" => 0.46066666666667,
    "bi" => 0.45722222222222,
    "de" => 0.45544444444444,
    [...]
)
```

## __construct()
You can pass an array of languages to the constructor. To compare the desired sentence only with the given languages.
This can dramatically increase the performance up to three times faster is possible, than without specifying languages.
```php
$ld = new Language(['de', 'en', 'nl']);
 
$ld->detect('Das ist ein Test'); // compares the sentence only with de, en and nl.
```

## whitelist()
Provide a whitelist. Returns a list of languages, which are required.
```php
$ld->detect('Mag het een onsje meer zijn?')->whitelist('de', 'nn', 'nl', 'af')->close();
```
Result:
```text
Array
(
    "nl" => 0.65733333333333,
    "af" => 0.50994444444444,
    "nn" => 0.48422222222222,
    "de" => 0.45544444444444
)
```

## blacklist()
Provide a blacklist. Removes the given languages from the result.
```php
$ld->detect('Mag het een onsje meer zijn?')->blacklist('dk', 'nb', 'de')->close();
```
Result:
```text
Array
(
    "nl" => 0.65733333333333,
    "af" => 0.50994444444444,
    "br" => 0.49177777777778,
    "nn" => 0.48422222222222,
    "fy" => 0.47361111111111,
    "sv" => 0.46066666666667,
    "bi" => 0.45722222222222,
    [...]
)
```

## bestResults()
Returns the best results.
```php
$ld->detect('Mag het een onsje meer zijn?')->bestResults()->close();
```
Result:
```text
Array
(
    [nl] => 0.65733333333333
)
```

## limit()
You can specify the number of records to return. For example the following code will return the top three entries.
```php
$ld->detect('Mag het een onsje meer zijn?')->limit(0, 3)->close();
```
Result:
```text
Array
(
    "nl" => 0.65733333333333,
    "af" => 0.50994444444444,
    "br" => 0.49177777777778
)
```

## close()
Returns the result as an array.
```php
$ld->detect('This is an example!')->close();
```
Result:
```text
Array
(
    "en" => 0.58436507936508,
    "gd" => 0.55325396825397,
    "ga" => 0.54920634920635,
    "et" => 0.48,
    "af" => 0.47920634920635,
    [...]
)
```

## validate()
> **Experimental**: Returns true if we can detect the language and false if we can not detect the language.

```php
$ld->detect('xyz')->validate();
```
Result:
```text
false
```

## __toString()
Returns the top entrie of the result. Note the `echo` at the beginning.
```php
echo $ld->detect('Das ist ein Test.');
```
Result:
```text
de
```

## Method Chaining
You can also combine methods with each other.
The following example will remove all entries specified in the blacklist and returns only the top four entries.
```php 
$ld->detect('Mag het een onsje meer zijn?')->blacklist('af', 'dk', 'sv')->limit(0, 4)->close();
```
Result:
```text
Array
(
    "nl" => 0.65733333333333,
    "br" => 0.49177777777778,
    "nb" => 0.48533333333333,
    "nn" => 0.48422222222222
)
```

## JsonSerializable
Serialized the data to JSON.
```php
$object = $ld->detect('Tere tulemast tagasi! Nägemist!');
 
json_encode($object, JSON_PRETTY_PRINT);
```
Result:
```text
{
    "et": 0.512258064516129,
    "ch": 0.44596774193548383,
    "bi": 0.43000000000000005,
    "fi": 0.4298924731182796,
    "lt": 0.42774193548387096,
    [...]
}
```

## IteratorAggregate
It's also possible to iterate over the result.
```php
foreach ($ld->detect('मुझे हिंदी नहीं आती') as $lang => $score) {
    // [...]
}
```

## ArrayAccess
You can also access the object directly as an array.
```php
$sentence = 'Das ist ein Test';
 
echo $ld->detect($sentence)['de'];
echo $ld->detect($sentence)['en'];
echo $ld->detect($sentence)['xy']; // doesn't exists
```
Result:
```text
0.65598039215686
0.565
NULL
```

## Supported languages
If your language not supported, feel free to add your own language files.

| Language | Language Code | Language | Language Code |
| :--- | :--- | :--- | :--- |
| Abkhaz | ab | Italian | it |
| Afrikaans | af | Inuktitut | iu |
| Amharic | am | Japanese | jp |
| Arabic | ar | Javanese | jv |
| Aymara | ay | Georgian | ka |
| Azerbaijani, North (Cyrillic) | az-Cyrl | Khmer | km |
| Azerbaijani, North (Latin) | az-Latn | Korean | ko |
| Belarusan | be | Kanuri | kr |
| Bulgarian | bg | Kurdish | ku |
| Bislama | bi | Latin | la |
| Bengali | bn | Ganda | lg |
| Tibetan | bo | Lao | lo |
| Breton | br | Lithuanian | lt |
| Bosnian (Cyrillic) | bs-Cyrl | Latvian | lv |
| Bosnian (Latin) | bs-Latn | Marshallese | mh |
| Catalan | ca | Mongolian, Halh (Cyrillic) | mn-Cyrl |
| Chamorro | ch | Malay (Arabic) | ms-Arab |
| Corsican | co | Malay (Latin) | ms-Latn |
| Cree | cr | Maltese | mt |
| Czech | cs | Norwegian, Nynorsk | nb |
| Welsh | cy | Ndonga | ng |
| German | de | Dutch | nl |
| Danish | dk | Norwegian, Bokmål | nn |
| Dzongkha | dz | Navajo | nv |
| Greek (monotonic) | el-monoton | Polish | pl |
| Greek (polytonic) | el-polyton | Portuguese (Brazil) | pt-BR |
| English | en | Portuguese (Portugal) | pt-PT |
| Esperanto | eo | Romanian | ro |
| Spanish | es | Russian | ru |
| Estonian | et | Slovak | sk |
| Basque | eu | Slovene | sl |
| Persian | fa | Somali | so |
| Finnish | fi | Albanian | sq |
| Fijian | fj | Swati | ss |
| Faroese | fo | Swedish | sv |
| French | fr | Tamil | ta |
| Frisian | fy | Thai | th |
| Gaelic, Irish | ga | Tagalog | tl |
| Gaelic, Scottish | gd | Turkish | tr |
| Galician | gl | Tatar | tt |
| Guarani | gn | Tahitian | ty |
| Gujarati | gu | Uyghur (Arabic) | ug-Arab |
| Hausa | ha | Uyghur (Latin) | ug-Latn |
| Hebrew | he | Ukrainian | uk |
| Hindi | hi | Uzbek | uz |
| Croatian | hr | Venda | ve |
| Hungarian | hu | Vietnamese | vi |
| Armenian | hy | Walloon | wa |
| Interlingua | ia | Wolof | wo |
| Indonesian | id | Xhosa | xh |
| Igbo | ig | Yoruba | yo |
| Ido | io | Chinese, Mandarin (Simplified) | zh-Hans |
| Icelandic | is | Chinese, Mandarin (Traditional) | zh-Hant |
