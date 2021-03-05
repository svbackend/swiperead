import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";

@Injectable({
  providedIn: 'root'
})
export class EpubUploaderService {

  constructor(private httpClient: HttpClient) {

  }


  public upload(formData) {
    return this.httpClient.post<any>('/api/v1/epub/new', formData, {
      reportProgress: true,
      observe: 'events'
    });

  }
}
