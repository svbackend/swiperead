import { Component, OnInit } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {BookCardEntity} from "../../modules/book/book-card.entity";
import {BookCardsResponse} from "../../modules/book/book-cards.response";

@Component({
  selector: 'app-book',
  templateUrl: './book.component.html',
  styleUrls: ['./book.component.scss']
})
export class BookComponent implements OnInit {

  constructor(private http: HttpClient) { }

  cards: BookCardEntity[] = []

  ngOnInit(): void {
    let id = '1';
    this.http.get<BookCardsResponse>(`/api/v1/books/${id}/cards`)
      .subscribe((res) => {
        this.cards = res.result
      });
  }

}
