import {Injectable} from "@angular/core";
import {
  HttpClient,
  HttpErrorResponse,
  HttpEvent,
  HttpHandler,
  HttpInterceptor,
  HttpRequest,
} from "@angular/common/http";

import {Observable, of} from "rxjs";
import {catchError} from "rxjs/operators";

@Injectable()
export class HttpConfigInterceptor implements HttpInterceptor {
  constructor(private http: HttpClient) {
  }

  intercept(
    request: HttpRequest<any>,
    next: HttpHandler
  ): Observable<HttpEvent<any>> {
    /*
    if (!request.headers.has("Content-Type")) {
      request = request.clone({
        headers: request.headers.set("Content-Type", "application/json"),
      });
    }*/

    return next.handle(request).pipe(
      catchError((err: any) => {
        if(err instanceof HttpErrorResponse && err.status === 401) {
          this.http.post('/logout', {})
            .subscribe(() => { window.location.href = '/' })
        }
        return of(err);
      }));

  }
}
