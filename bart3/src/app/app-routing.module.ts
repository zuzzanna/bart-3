import {NgModule} from '@angular/core';
import {RouterModule, Routes} from '@angular/router';
import {CategorieComponent} from "./categories/categorie/categorie.component";
import {CategoriesComponent} from "./categories/categories.component";


const routes: Routes = [
  {path: ':path', component: CategorieComponent},
  {path: '**', component: CategoriesComponent},
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {
}
