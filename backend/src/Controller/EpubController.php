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
            dump($chapterContent);
            $chapterCards = $this->getCardsByContent($chapterContent);
            dd($chapterCards);
            $cards += $chapterCards;
        }

        dd($cards);

        return $this->ack();
    }

    private function getCardsByContent(string $chapterContent): array
    {
        $rootId = uniqid('swiperead', false);

        $chapterContentWrapped = "<div id=\"{$rootId}\">{$chapterContent}</div>";
        $chapterContentWrapped = strtr($chapterContentWrapped, ["\n" => '']);
        $dom = HtmlDomParser::str_get_html($chapterContentWrapped);

        if (mb_strlen($dom->text()) <= 200) {
            return [$chapterContent];
        }

        $child = $dom->getElementById($rootId)->firstChild();
        if (!$child) {
            return [];
        }

        if (mb_strlen($child->text()) <= 200) {
            return [$child->html()];
        }

        // dd($child->innerHtml());

        return $this->getCardsByContent($child->innerHtml());
    }
}
