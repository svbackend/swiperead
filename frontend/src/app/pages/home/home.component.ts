import { Component, OnInit } from '@angular/core';
import {environment} from "../../../environments/environment";
import {Router} from "@angular/router";
import {UserState} from "../../modules/user/user.state";

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {

  constructor(
    private router: Router,
    public userState: UserState
  ) { }

  ngOnInit(): void {
  }

  joinUsingGoogle() {
    window.location.href = environment.APP_URL + '/api/v1/oauth2/google/login?url=' + this.router.url
  }
}
