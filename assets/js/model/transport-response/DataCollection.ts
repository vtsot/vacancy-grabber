import type Paginator from './Paginator';
import type Filters from './Filters';

export default class DataCollection<T> {
    protected readonly _items: T[];
    protected readonly _paginator: Paginator;
    protected readonly _filters: Filters | null;

    constructor(
        items: T[],
        paginator: Paginator,
        filters: Filters | null,
    ) {
        this._items = items;
        this._paginator = paginator;
        this._filters = filters;
    }

    get items(): T[] {
        return this._items;
    }

    get paginator(): Paginator {
        return this._paginator;
    }

    get filters(): Filters | null {
        return this._filters;
    }
}
