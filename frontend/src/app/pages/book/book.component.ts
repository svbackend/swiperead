import { Component, OnInit } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {BookCardEntity} from "../../modules/book/book-card.entity";
import {BookCardsResponse} from "../../modules/book/book-cards.response";
import {ActivatedRoute, Route} from "@angular/router";

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

  ngOnInit(): void {
    this.route.params.subscribe((params) => {
      this.http.get<BookCardsResponse>(`/api/v1/books/${params.id}/cards`)
        .subscribe((res) => {
          this.cards = res.result
        });
    });
  }

}
