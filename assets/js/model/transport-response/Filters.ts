export default class Filters {
    private readonly _query: string | null;
    private readonly _sort: string | null;
    private readonly _sortBy: string[] | null;
    private readonly _available: object | null;
    private readonly _applied: object | null;
    // new feature
    private readonly _availableSortBy: string[] | null;
    private readonly _availableFilters: object | null;
    private readonly _appliedFilters: object | null;

    constructor(
        query: string | null,
        sort: string | null,
        sortBy: string[] | null,
        available: object | null,
        applied: object | null,
        // new feature
        availableSortBy: string[] | null,
        availableFilters: object | null,
        appliedFilters: object | null,
    ) {
        this._query = query;
        this._sort = sort;
        this._sortBy = sortBy;
        this._available = available;
        this._applied = applied;
        // new feature
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

    get sortBy(): string[] | null {
        return this._sortBy;
    }

    get available(): object | null {
        return this._available;
    }

    get applied(): object | null {
        return this._applied;
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
