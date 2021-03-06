<?php

namespace App\Service\Epub;

use App\Dto\CardDto;
use App\Dto\ChapterDto;
use App\Entity\Author;
use App\Entity\Book;
use App\Entity\User\User;
use App\Repository\BookRepository;
use App\Service\Flusher;
use App\ValueObject\Author\AuthorId;
use App\ValueObject\Book\BookId;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use voku\helper\HtmlDomParser;

class EpubProcessor
{
    public function __construct(
        private BookRepository $books,
        private Flusher $flusher,
    )
    {
    }

    public function process(User $owner, UploadedFile $epub): void
    {
        $filepath = $epub->getRealPath();
        $parse = new EpubParser($filepath);
        $parse->parse();

        $toc = $parse->getTOC();

        $chapterIndex = 0;
        $cards = [];
        foreach ($toc as $tocItem) {
            $refItemHref = basename($tocItem['file_name']);
            $chapterContent = $parse->getChapterByHref($refItemHref);
            $chapterCards = $this->getCardsByContent($chapterContent);
            $cards[$chapterIndex++] = $chapterCards;
        }

        $chaptersLen = count($cards);
        for ($chapterIndex = 0; $chapterIndex < $chaptersLen; $chapterIndex++) {
            $removeCardsIdx = [];
            $cardsLen = count($cards[$chapterIndex]);
            for ($i = 0, $k = 1; $i < $cardsLen && $k < $cardsLen; $k++) {
                $currentCardSize = mb_strlen(strip_tags($cards[$chapterIndex][$i]));

                if ($currentCardSize < 140) {
                    $cards[$chapterIndex][$i] .= $cards[$chapterIndex][$k];
                    $removeCardsIdx[] = $k;
                    if ($currentCardSize > 140) {
                        $i = ++$k;
                        continue;
                    }
                } elseif ($i + 1 === $k) {
                    $i++;
                } else {
                    $i = $k;
                }
            }

            foreach ($removeCardsIdx as $idx) {
                unset($cards[$chapterIndex][$idx]);
            }
        }

        //$cardOrdering = 1;
        $chapters = new ArrayCollection();

        foreach ($cards as $idx => $chapterCards) {
            $chapterName = $toc[$idx]['name'];
            $cardsDto = [];
            $cardIdx = 1;
            foreach ($chapterCards as $content) {
                $cardsDto[] = new CardDto($content, $cardIdx++);
            }
            $chapters->add(new ChapterDto($chapterName, 1+$idx, $cardsDto));
        }

        $creator = $parse->getDcItem('creator');
        if (is_string($creator)) {
            $creator = [$creator];
        }

        $authors = new ArrayCollection(array_map(static fn(string $name) => new Author(AuthorId::generate(), $name), $creator));

        $book = new Book(
            BookId::generate(),
            $parse->getDcItem('title') ?: 'N/A',
            $owner,
            $authors,
            $chapters
        );
        $this->books->add($book);
        $this->flusher->flush();
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

    private function addHtmlTagsToCards(array $cards, string $prefix, string $postfix): array
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
    private function extractHtmlElementPrefixAndPostfix(string $outerHtml): array
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
