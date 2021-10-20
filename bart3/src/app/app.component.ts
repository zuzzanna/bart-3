import {Component, ElementRef, ViewChild} from '@angular/core';
import {Inject} from '@angular/core';
import {DOCUMENT} from '@angular/common';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})


export class AppComponent {
  title = 'bart2';

  constructor() {
  }

}
