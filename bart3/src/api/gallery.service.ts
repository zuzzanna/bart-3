import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {Observable} from 'rxjs';
import {IGalleryDetails, IGalleryImageDetail, IGalleryList, IImage} from '../interfaces/index';
import {environment} from '../environments/environment';
import {map} from 'rxjs/operators';

@Injectable()
export class GalleryService {

  constructor(private http: HttpClient) {
  }

  public listGalleries(): Observable<IGalleryList> {
    return this.http.get<IGalleryList>(environment.API_url + "/api/gallery");
  }

  public addGallery(name: string): Observable<IGalleryDetails> {
    return this.http.post<IGalleryDetails>(environment.API_url + "/api/gallery", {
      name
    });
  }

  public getGallery(path: string): Observable<IGalleryImageDetail> {
    return this.http.get<IGalleryImageDetail>(environment.API_url + "/api/gallery/" + encodeURIComponent(path));
  }

  public deleteGallery(path: string): Observable<string> {
    return this.http.request('DELETE', environment.API_url + "/api/gallery/" + encodeURIComponent(path),
      {responseType: 'text'})
      .pipe(map(
        v => {
          return v;
        }
      ));
  }

  public deleteImage(path: string, image: string): Observable<string> {
    return this.http.request('DELETE', environment.API_url + "/api/gallery/" + encodeURIComponent(path) + '/'
      + encodeURIComponent(image),
      {responseType: 'text'})
      .pipe(map(
        v => {
          return v;
        }
      ));
  }

  public addImage(path: string, image: File, imageName: string): Observable<IImage> {
    let formData = new FormData();
    formData.append('image', image, "img.png");
    formData.append('name', imageName);
    return this.http.post<IImage>(environment.API_url + '/api/gallery/' + encodeURIComponent(path), formData)
  }


}
