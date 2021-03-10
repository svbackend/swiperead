import {Component, OnInit, ViewContainerRef} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {BookCardEntity} from "../../modules/book/book-card.entity";
import {BookCardsResponse} from "../../modules/book/book-cards.response";
import {ActivatedRoute} from "@angular/router";
import {AckResponse} from "../../ack.response";

@Component({
  selector: 'app-book',
  templateUrl: './book.component.html',
  styleUrls: ['./book.component.scss']
})
export class BookComponent implements OnInit {

  public cardImageHeight = 320

  constructor(
    private route: ActivatedRoute,
    private http: HttpClient
  ) {
  }

  cards: BookCardEntity[] = []
  bookId: string = ''
  loading: boolean = false;

  ngOnInit(): void {
    this.route.params.subscribe((params) => {
      this.bookId = params.id
      this.loading = true;
      this.http.get<BookCardsResponse>(`/api/v1/books/${params.id}/cards`)
        .subscribe((res) => {
          this.cards = res.result
          this.loading = false
        });
    });
  }

  cardVisible(card: BookCardEntity) {
    if (this.loading) {
      return;
    }

    let lastCard = this.cards[this.cards.length - 1]

    if (!lastCard) {
      return;
    }

    if (card.ordering > (lastCard.ordering - 3)) {
      this.loading = true;
      this.http.get<BookCardsResponse>(`/api/v1/books/${this.bookId}/cards?card_id=${lastCard.id}`)
        .subscribe((res) => {
          let tmp = this.cards.slice(this.cards.length - 5)
          tmp.push(...res.result)
          this.cards = tmp
          this.loading = false;
        });
    }

    this.saveProgress(card);
  }

  saveProgress(card: BookCardEntity) {
    this.http.post<AckResponse>(`/api/v1/books/${this.bookId}/bookmark`, {card_id: card.id})
      .subscribe((res) => {
      });
  }

  loadPrev() {
    if (this.loading) {
      return;
    }

    let card: BookCardEntity = this.cards[0]


    if (card.ordering > 1) {
      this.loading = true;
      this.http.get<BookCardsResponse>(`/api/v1/books/${this.bookId}/cards?card_id=${card.id}&prev`)
        .subscribe((res) => {
          this.cards = [...res.result, ...this.cards.slice(0, -1 * res.result.length)]
          this.loading = false;

          // timeout 0ms needed to call function inside after rerender
          setTimeout(() => {
            let el = document.getElementById('card' + card.id);
            if (el) {
              // offset top = new position of card element + add image size of items that now placed before
              // images not loaded instantly so we have this lag when offsetTop calculates only text size
              // so we have to manually add images size as we know they would be loaded soon so size will be changed
              let offsetTop = el.offsetTop + (this.cardImageHeight * res.result.length) - 50;
              window.scrollTo({top: offsetTop, behavior: "smooth"})
            }
          }, 0);
        });
    }
  }
}
