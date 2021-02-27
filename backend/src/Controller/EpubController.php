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
            $cards += $chapterCards;
        }

        dd($cards);

        return $this->ack();
    }

    private function getCardsByContent(string $chapterContent): array
    {
        $dom = HtmlDomParser::str_get_html($chapterContent);

        return [];
    }
}
