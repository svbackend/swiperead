import {Injectable} from '@angular/core';
import {UserService} from "./user.service";
import {getCookie} from "../../utils/cookie";
import {CurrentUserEntity} from "./current-user.entity";

@Injectable({providedIn: 'root'})
export class UserState {
  private state: CurrentUserEntity|null = null;

  constructor(private userService: UserService) {
  }

  get user() { return this.state; }

  loadCurrentUser() {
    if (!getCookie('IS_LOGGED_IN').length) return;

    this.userService.getCurrentUser()
      .toPromise()
      .then((res) => {
        this.state = res
      })
      .catch((err) => {
        console.error(err)
    })
  }

  logout() {
    if (!getCookie('IS_LOGGED_IN').length) return;

    this.userService.logout()
      .toPromise()
      .then((res) => {
        this.state = null
      })
      .catch((err) => {
        console.error(err)
      })
  }
}
