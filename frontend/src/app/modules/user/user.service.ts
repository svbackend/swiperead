import {Injectable} from '@angular/core';
import {HttpClient} from '@angular/common/http';
import {CurrentUserEntity} from "./current-user.entity";
import {AckResponse} from "../../ack.response";

@Injectable({providedIn: 'root'})
export class UserService {
  constructor(private http: HttpClient) { }

  getCurrentUser() {
    return this.http.get<CurrentUserEntity>('/api/v1/user/me');
  }

  logout() {
    return this.http.post<AckResponse>('/api/v1/user/logout', {});
  }
}
