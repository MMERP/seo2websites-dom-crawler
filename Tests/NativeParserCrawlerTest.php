<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DomCrawler\Tests;

use Symfony\Component\DomCrawler\Crawler;

class NativeParserCrawlerTest extends AbstractCrawlerTest
{
    public function createCrawler($node = null, string $uri = null, string $baseHref = null)
    {
        return new Crawler($node, $uri, $baseHref, false);
    }

    public function testAddHtmlContentWithErrors()
    {
        $internalErrors = libxml_use_internal_errors(true);

        $crawler = $this->createCrawler();
        $crawler->addHtmlContent(<<<'EOF'
<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <nav><a href="#"><a href="#"></nav>
    </body>
</html>
EOF
            , 'UTF-8');

        $errors = libxml_get_errors();
        $this->assertCount(1, $errors);
        $this->assertEquals("Tag nav invalid\n", $errors[0]->message);

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
    }

    public function testAddXmlContentWithErrors()
    {
        $internalErrors = libxml_use_internal_errors(true);

        $crawler = $this->createCrawler();
        $crawler->addXmlContent(<<<'EOF'
<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <nav><a href="#"><a href="#"></nav>
    </body>
</html>
EOF
            , 'UTF-8');

        $this->assertGreaterThan(1, libxml_get_errors());

        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);
    }
}
