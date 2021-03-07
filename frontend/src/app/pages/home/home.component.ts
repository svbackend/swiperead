import { Component, OnInit } from '@angular/core';
import {environment} from "../../../environments/environment";
import {Router} from "@angular/router";
import {UserState} from "../../modules/user/user.state";
import {FormControl, FormGroup, Validators} from "@angular/forms";
import {HttpClient} from "@angular/common/http";
import {BookPreviewEntity} from "../../modules/book/book-preview.entity";
import {BooksResponse} from "../../modules/book/books.response";
import {BookAuthorPreviewEntity} from "../../modules/book/book-author-preview.entity";

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {

  formData = new FormData();

  uploadForm = new FormGroup({
    book: new FormControl('', [Validators.required]),
  });

  books: BookPreviewEntity[] = [];

  constructor(
    private router: Router,
    public userState: UserState,
    private http: HttpClient
  ) { }

  ngOnInit(): void {
    this.http.get<BooksResponse>('/api/v1/books')
      .subscribe(
        (res) => {
          this.books = res.result
        }
      )
  }

  joinUsingGoogle() {
    window.location.href = environment.APP_URL + '/api/v1/oauth2/google/login?url=' + this.router.url
  }

  onSubmit() {
    this.http.post('/api/v1/epub/new', this.formData)
      .subscribe(
        (res) => {},
        (err) => {
          console.log(err)
        }
      )
  }

  onFileSelect(event: any) {
    if (event.target.files.length > 0) {
      const file = event.target.files[0];
      this.formData.append('book', file);
    }
  }

  bookAuthorsMap(a: BookAuthorPreviewEntity) {
    return a.name
  }
}
