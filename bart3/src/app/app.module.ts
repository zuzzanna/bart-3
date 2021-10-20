import {NgModule} from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';
import {DialogModule} from 'primeng/dialog';
import {AppRoutingModule} from './app-routing.module';
import {AppComponent} from './app.component';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';
import {CategorieComponent} from './categories/categorie/categorie.component';
import {CategoriesComponent} from './categories/categories.component';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {GalleriaModule} from 'primeng/galleria';
import {GalleryService} from "../api/gallery.service";
import {FormsModule} from "@angular/forms";
import {HttpClientModule} from '@angular/common/http';
import {FileUploadModule} from 'primeng/fileupload';

@NgModule({
  declarations: [
    AppComponent,
    CategorieComponent,
    CategoriesComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    NgbModule,
    DialogModule,
    BrowserAnimationsModule,
    GalleriaModule,
    FormsModule,
    HttpClientModule,
    FileUploadModule
  ],
  providers: [
    GalleryService,
  ],
  bootstrap: [AppComponent]
})
export class AppModule {
}
