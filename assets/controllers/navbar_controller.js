import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  connect() {
    window.addEventListener('scroll', () => this.handleScroll());
  }

  disconnect() {
    window.removeEventListener('scroll', () => this.handleScroll());
  }

  handleScroll() {
    if (window.scrollY > 0) {
      this.element.classList.add('site-nav--scrolled');
    } else {
      this.element.classList.remove('site-nav--scrolled');
    }
  }
}
