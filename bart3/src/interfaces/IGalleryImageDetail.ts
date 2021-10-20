import {IGalleryImage} from './IGalleryImage';
import {IGalleryDetails} from './IGalleryDetails'

export interface IGalleryImageDetail {
  gallery: IGalleryDetails;
  images: IGalleryImage[];
}
