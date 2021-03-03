import {Component} from '@angular/core';
import {UserState} from "./modules/user/user.state";

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  title = 'swiperead';

  constructor(
    private userState: UserState
  ) {
    userState.loadCurrentUser();
  }
}
