<?php

namespace App\Controller;

use App\Service\Epub\EpubParser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use voku\helper\HtmlDomParser;

class EpubController extends BaseApiController
{
    #[Route('/api/v1/epub')]
    public function playground(Request $request): Response
    {
        $filepath = __DIR__ . '/../Service/Epub/mark.epub';
        $parse = new EpubParser($filepath);
        $parse->parse();

        $toc = $parse->getTOC();
        dump($toc);

        $cards = [];
        foreach ($toc as $tocItem) {
            $refItemHref = basename($tocItem['file_name']);
            $chapterContent = $parse->getChapterByHref($refItemHref);
            $chapterCards = $this->getCardsByContent($chapterContent);
            //dd($chapterCards);
            $cards += $chapterCards;
        }

        $removeCardsIdx = [];
        $len = count($cards);
        for ($i = 0, $k = 1; $i < $len && $k < $len; $k++) {
            $currentCardSize = mb_strlen(strip_tags($cards[$i]));
            dump(strip_tags($cards[$i]), $currentCardSize, $i, $k);
            if ($currentCardSize < 140) {
                $cards[$i] .= $cards[$k];
                $removeCardsIdx[] = $k;
                if ($currentCardSize > 140) {
                    $i = ++$k;
                    continue;
                }
            } elseif ($i+1 === $k) {
                $i++;
            } else {
                $i = ++$k;
            }
        }
        //$cards[--$i] .= $cards[$len - 1];
        //$cards[] =

        foreach ($removeCardsIdx as $idx) {
            unset($cards[$idx]);
        }

        dd($cards, $removeCardsIdx);

        return $this->ack();
    }

    private function getCardsByContent(string $chapterContent): array
    {
        $dbg = mb_substr($chapterContent, 0, 6) === 'Первая';

        $rootId = uniqid('swiperead', false);

        $chapterContentWrapped = "<div id=\"{$rootId}\">{$chapterContent}</div>";
        $chapterContentWrapped = strtr($chapterContentWrapped, ["\n" => '']);
        $dom = HtmlDomParser::str_get_html($chapterContentWrapped);

        if (mb_strlen($dom->text()) <= 200) {
            return [$chapterContent];
        }

        $root = $dom->getElementById($rootId);
        $children = $root->children();
        if (!$children->length) {
            return [$chapterContent];
        }

        $cards = [];

        foreach ($children as $child) {
            if (mb_strlen($child->text()) <= 200) {
                $cards = array_merge($cards, [$child->html()]);
            } else {
                [$currentElPrefix, $currentElPostfix] = $this->extractHtmlElementPrefixAndPostfix($child->outerHtml());

                if (trim($child->innerHtml())) {
                    $cards = array_merge(
                        $cards,
                        $this->addHtmlTagsToCards($this->getCardsByContent($child->innerHtml()), $currentElPrefix, $currentElPostfix)
                    );
                } else {
                    $cards = array_merge($cards, $this->addHtmlTagsToCards([$child->text()], $currentElPrefix, $currentElPostfix));
                }
            }
        }

        return $cards;
    }

    public function addHtmlTagsToCards(array $cards, string $prefix, string $postfix): array
    {
        $c = [];
        foreach ($cards as $card) {
            $c[] = $prefix . $card . $postfix;
        }

        return $c;
    }

    /**
     * @param string $outerHtml example: <div id="123">some text</div>
     * @return ["<div id="123">", "</div>"]
     */
    public function extractHtmlElementPrefixAndPostfix(string $outerHtml): array
    {
        $outerHtml = trim($outerHtml);
        $leftArrowPosition = mb_strpos($outerHtml, '<');
        $rightArrowPosition = mb_strpos($outerHtml, '>');

        if ($rightArrowPosition === false || $leftArrowPosition === false) {
            return ['', ''];
        }

        $el = mb_substr($outerHtml, $leftArrowPosition, $rightArrowPosition + 1);

        $tagNameEndPosition = mb_strpos($el, ' ');
        if ($tagNameEndPosition === false) {
            $tagNameEndPosition = mb_strpos($el, '>');
        }

        $elName = trim(mb_substr($el, 1, $tagNameEndPosition));

        return [$el, "</{$elName}>"];
    }
}
