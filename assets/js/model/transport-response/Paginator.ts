export default class Paginator {
    private readonly _page: number;
    private readonly _perPage: number;
    private readonly _total: number;
    //private readonly _pagesCount: number;

    constructor(page: number, perPage: number, total: number) {
        this._page = page;
        this._perPage = perPage;
        this._total = total;
        //this._pagesCount = Math.ceil(total / perPage);
    }

    get page(): number {
        return this._page;
    }

    get perPage(): number {
        return this._perPage;
    }

    get total(): number {
        return this._total;
    }

    // get pagesCount(): number {
    //     return this._pagesCount;
    // }
    //
    // get pageItemsCount(): number {
    //     return this._perPage * this._page;
    // }
    //
    // get nextPageItemsCount(): number {
    //     const amount = this._itemsCount - this.pageItemsCount;
    //
    //     return amount > 0 ? amount : 0;
    // }
    //
    // get pageItemsFromCount(): number {
    //     return (this._page - 1) * this._perPage + 1;
    // }
    //
    // get pageItemsToCount(): number {
    //     const amount = this.pageItemsCount;
    //
    //     return amount > this._itemsCount ? this._itemsCount : amount;
    // }
    //
    // get hasNextPage(): boolean {
    //     return this.pageItemsCount < this._itemsCount;
    // }
    //
    // get nextPage(): number | null {
    //     if (this.hasNextPage) {
    //         return this._page + 1;
    //     }
    //
    //     return null;
    // }
}
