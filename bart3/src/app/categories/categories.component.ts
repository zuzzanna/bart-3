import {Component, OnInit} from '@angular/core';
import {GalleryService} from "../../api/gallery.service";
import {IGalleryImageDetail} from "../../interfaces";
import {environment} from '../../environments/environment';
import {Router} from "@angular/router";

@Component({
  selector: 'app-categories',
  templateUrl: './categories.component.html',
  styleUrls: ['./categories.component.scss']
})
export class CategoriesComponent implements OnInit {
  displayBasic: boolean = false;
  galleries: IGalleryImageDetail[] = [];
  newCategoryName: string = '';

  constructor(private galleryService: GalleryService, private router: Router) {
  }

  ngOnInit(): void {
    this.load_galleries();
  }

  /**
   * Returns list of all galleries
   * @return IGalleryImageDetail[] sorted list of galleries
   */
  load_galleries() {
    this.galleryService.listGalleries().subscribe(data => {
      for (let gallery of data.galleries) {
        this.galleryService.getGallery(gallery.path).subscribe(d => {
          this.galleries.push(d);
          this.galleries.sort((a, b) => {
            return a.gallery.name.toLocaleLowerCase().localeCompare(b.gallery.name.toLocaleLowerCase());
          });
        })
      }
    })
  }

  /**
   * Creates new gallery without images, name is from user´s input
   */
  createGallery(): void {
    this.galleryService.addGallery(this.newCategoryName).subscribe(gallery => {
        this.galleries.push({
          gallery: gallery,
          images: []
        })
      }, (error => {
        console.log(error);
      })
    );
    this.newCategoryName = '';
  }


  /**
   * Returns path of first image of gallery for image preview. If gallery does not have image, function return default image preview
   * @param gallery gallery
   * @return string path of first image of gallery
   */
  get_gallery_image(gallery: IGalleryImageDetail) {
    return gallery.images.length > 0
      ? environment.API_url + '/api/images/300x0/' + gallery.images[0].fullpath
      : environment.API_url + '/assets/default.jpg';
  }


  /**
   * Shows currently hover image as header background
   * @param parent hovered image
   */
  show_img_background(parent: any) {
    let path = parent.getElementsByTagName("img")[0].getAttribute("src");
    let header = <HTMLInputElement>document.getElementById("header")
    header.style.background = "url('" + path + "') repeat scroll 70% 60%";
  }

  /**
   * Hides image from header background
   */
  hide_img_background(): void {
    let header = <HTMLInputElement>document.getElementById("header")
    header.style.background = "darkgray";
    header.classList.remove("bg_image");
  }
}
