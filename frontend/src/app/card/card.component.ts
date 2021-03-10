import {Component, EventEmitter, Input, OnInit, Output, TemplateRef, ViewContainerRef} from '@angular/core';
import {BookCardEntity} from "../modules/book/book-card.entity";

@Component({
  selector: 'app-card',
  templateUrl: './card.component.html',
  styleUrls: ['./card.component.scss']
})
export class CardComponent implements OnInit {

  @Input() card: BookCardEntity|null = null
  @Output() visible: EventEmitter<BookCardEntity> = new EventEmitter<BookCardEntity>()

  constructor(
    private vcRef: ViewContainerRef
  ) { }

  ngOnInit(): void {
    const commentEl = this.vcRef.element.nativeElement;

    const observer = new IntersectionObserver(entries => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          this.inViewport()
        }
      });
    }, {threshold: [.5]});
    observer.observe(commentEl);
  }

  inViewport() {
    if (this.card) {
      console.log('inViewport')
      this.visible.emit(this.card)
    }
  }
}
