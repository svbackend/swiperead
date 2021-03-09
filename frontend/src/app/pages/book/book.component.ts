import {Component, OnInit, ViewContainerRef} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {BookCardEntity} from "../../modules/book/book-card.entity";
import {BookCardsResponse} from "../../modules/book/book-cards.response";
import {ActivatedRoute, Route} from "@angular/router";
import {last} from "rxjs/operators";

@Component({
  selector: 'app-book',
  templateUrl: './book.component.html',
  styleUrls: ['./book.component.scss']
})
export class BookComponent implements OnInit {

  constructor(
    private route: ActivatedRoute,
    private http: HttpClient
  ) { }

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
    console.log('cardVisible')
    if (this.loading) {
      console.log('loading.. =(')
      return;
    }

    console.log(this.cards.length)
    let lastCard = this.cards[this.cards.length - 1]

    console.log('last card', lastCard)

    if (!lastCard) {
      return;
    }

    console.log(card.ordering, lastCard.ordering)

    if (card.ordering > (lastCard.ordering - 3)) {
      this.loading = true;
      this.http.get<BookCardsResponse>(`/api/v1/books/${this.bookId}/cards?card_id=${lastCard.id}`)
        .subscribe((res) => {
          this.cards.push(...res.result)
          this.loading = false;
        });
    }
    console.log('visible!', card)
  }
}
