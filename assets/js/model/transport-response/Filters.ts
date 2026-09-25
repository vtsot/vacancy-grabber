export default class Filters {
    private readonly _query: string | null;
    private readonly _sort: string | null;
    private readonly _availableSortBy: string[] | null;
    private readonly _availableFilters: object | null;
    private readonly _appliedFilters: object | null;

    constructor(
        query: string | null,
        sort: string | null,
        availableSortBy: string[] | null,
        availableFilters: object | null,
        appliedFilters: object | null,
    ) {
        this._query = query;
        this._sort = sort;
        this._availableSortBy = availableSortBy;
        this._availableFilters = availableFilters;
        this._appliedFilters = appliedFilters;
    }

    get query(): string | null {
        return this._query;
    }

    get sort(): string | null {
        return this._sort;
    }

    get availableSortBy(): string[] | null {
        return this._availableSortBy;
    }

    get availableFilters(): object | null {
        return this._availableFilters;
    }

    get appliedFilters(): object | null {
        return this._appliedFilters;
    }
}
