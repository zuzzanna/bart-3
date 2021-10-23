import {AfterViewChecked, Component, HostListener, OnInit} from '@angular/core';
import {GalleryService} from "../../../api/gallery.service";
import {IGalleryImage, IGalleryImageDetail} from "../../../interfaces";
import {environment} from '../../../environments/environment';
import {Router} from "@angular/router";


@Component({
  selector: 'app-architecture',
  templateUrl: './categorie.component.html',
  styleUrls: ['./categorie.component.scss']
})

export class CategorieComponent implements OnInit, AfterViewChecked {
  activeIndex: number = 0;
  displayBasic: boolean = false;
  displayCustom: boolean;
  gallery: IGalleryImageDetail | null = null;
  name: string | null = '';
  responsiveOptions: any[] = [
    {
      breakpoint: '1024px',
      numVisible: 5
    },
    {
      breakpoint: '768px',
      numVisible: 3
    },
    {
      breakpoint: '560px',
      numVisible: 1
    }
  ];

  @HostListener('document:keydown', ['$event']) onKeyDown(e: KeyboardEvent) {
    switch (e.keyCode) {
      case 27:
        this.displayCustom = false;
        break;
      case 37:
        this.prev();
        break;
      case 39:
        this.next();
        break;
    }
  }

  constructor(private galleryService: GalleryService, private router: Router) {
    this.displayCustom = false;
  }

  ngOnInit(): void {
    this.loadGallery();

  }

  ngAfterViewChecked() {
    let this_ = this;
    setTimeout(function () {
      // @ts-ignore
      this_.name = this_.gallery?.gallery.name;
      // @ts-ignore
      if (this_.gallery?.images.length == 0) {
        let header = <HTMLInputElement>document.getElementById("header")
        header.style.background = "background: darkgray none repeat scroll 0% 0%";
      }
    }, 0);
  }


  /**
   * Change displayCustom to true and shows image based on index of clicked image
   * @param index index of clicked image
   */
  imageClick(index: number): void {
    this.displayCustom = true;
    this.activeIndex = index;
  }

  /**
   * Returns path of image to gallery
   * @param image
   * @return string path of image
   */
  get_gallery_image(image: IGalleryImage): string {
    return environment.API_url + '/api/images/700x0/' + image.fullpath;
  }

  /**
   * Returns path of image preview to gallery
   * @param image
   * @return string path of image preview
   */
  get_gallery_preview(image: IGalleryImage): string {
    return environment.API_url + '/api/images/300x0/' + image.fullpath;
  }

  /**
   * Loads content of gallery to gallery variable and calls function header_background()
   */
  loadGallery(): void {
    console.log(window.location.pathname.replace('/', ''))
    this.galleryService.getGallery(
      window.location.pathname.replace('/', '')).subscribe(d => {
      this.gallery = d;
      let this_ = this;
      setTimeout(function () {
        this_.header_background();
        console.log(this_.gallery)
      }, 0);
    })
  }

  /**
   * On click of right arrow moves to next image
   */
  next(): void {
    let nextItemIndex = this.activeIndex + 1;
    // @ts-ignore
    this.activeIndex = this.gallery?.images.length - 1 === this.activeIndex
      ? 0
      : nextItemIndex;
  }

  /**
   * On click of left arrow moves to previous image
   */
  prev(): void {
    let prevItemIndex = this.activeIndex !== 0 ? this.activeIndex - 1 : 0;
    this.activeIndex = this.activeIndex === 0
      // @ts-ignore
      ? this.gallery?.images.length - 1
      : prevItemIndex;
  }

  /**
   * Uploads image
   * @param event
   */
  public uploadImages(event: { files: File[] }): void {
    if (event.files.length != 1)
      return;
    this.galleryService.addImage(
      window.location.pathname.replace('/', ''),
      event.files[0],
      event.files[0].name
    ).subscribe((img) => {
      for (let im of img.uploaded) {
        console.log(img)
        this.gallery?.images.push(im);
      }
    });
  }

  /**
   * Put path of first image of gallery to header
   */
  header_background(): void {
    let header = <HTMLInputElement>document.getElementById("header")
    let first_image_path = document.getElementsByClassName("categorie_gallery")[0]
      .children[0]
      .children[0].getAttribute("src")
    header.style.background = first_image_path !== null ?
      "url('" + first_image_path + "') repeat scroll 70% 60%" :
      "background: darkgray none repeat scroll 0% 0%";
  }

  /**
   * Deletes image from database and update gallery list
   * @param image deleted image
   */
  deleteImage(image: IGalleryImage): void {
    if (confirm("Naozaj chcete zmazať tento obrázok?")) {
      this.galleryService.deleteImage(window.location.pathname.replace('/', ''),
        image.path).subscribe(() => {
        // @ts-ignore
        this.gallery?.images = this.gallery?.images.filter(obj => obj.fullpath != image.fullpath)
      }, (err) => {
        console.log('Error: \n', err)
      });
    }
  }

  /**
   * Deletes gallery from database redirects to main page
   */
  deleteGallery(): void {
    if (confirm("Naozaj chcete zmazať túto galériu?")) {
      this.galleryService.deleteGallery(environment.API_url +
        window.location.pathname.replace('/', '')).subscribe();
      this.router.navigate((['']));
    }
  }

}
