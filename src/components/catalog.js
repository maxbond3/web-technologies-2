export class Catalog {
  #el = null;
  #paginationEl = null;
  #itemsEl = null;
  #page = null;
  #total = null;
  #renderItem = null;
  #getItems = null;

  constructor(el, options) {
    const { renderItem, getItems } = options;
    this.#el = el;
    this.#page = this.getPage();
    this.#paginationEl = el.querySelector("[data-catalog-pagination]");
    this.#itemsEl = el.querySelector("[data-catalog-items]");
    this.#renderItem = renderItem;
    this.#getItems = getItems;
  }

  get limit() {
    return 12;
  }

  get pageCount() {
    return Math.ceil(this.#total / this.limit);
  }

  init() {
    window.onpopstate = () => {
      const url = new URL(window.location.href);
      const page = +url.searchParams.get("page");

      if (page !== this.#page) {
        this.setPage(page);
        this.loadItems();
      }
    };

    this.#paginationEl.addEventListener("click", (event) => {
      const item = event.target.dataset.catalogPaginationPage
        ? event.target
        : event.target.closest("[data-catalog-pagination-page]");

      if (!item) {
        return;
      }

      const page = +item.dataset.catalogPaginationPage;

      this.setPage(page);
      this.pushState();
      this.loadItems();
    });

    this.loadItems();
  }

  getPage() {
    const url = new URL(window.location.href);
    const page = +url.searchParams.get("page");

    return page || 1;
  }

  setPage(page) {
    this.#page = page;
  }

  pushState() {
    const url = new URL(window.location.href);
    url.searchParams.set("page", this.#page);

    window.history.pushState({}, "", url);
  }

  async loadItems() {
    try {
      const { items, total } = await this.#getItems({
        limit: this.limit,
        page: this.#page,
      });

      this.#total = total;
      this.renderItems(items);
      this.renderPagination();
    } catch (error) {
      console.log(error);
    }
  }

  renderItems(items) {
    this.#itemsEl.innerHTML = items.map(this.#renderItem).join("");
  }

  renderPagination() {
    let html = "";

    for (let index = 0; index < this.pageCount; index++) {
      const page = index + 1;

      const classes = ["catalog__pagination-item"];

      if (page === this.#page) {
        classes.push("catalog__pagination-item_active");
      }

      html += `
                <button
                    class="${classes.join(" ")}"
                    data-catalog-pagination-page="${page}"
                >
                    ${page}
                </button>
            `;
    }

    this.#paginationEl.innerHTML = html;
  }
}
