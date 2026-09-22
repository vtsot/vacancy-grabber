import {camelCase} from "lodash";
import {camelizeArrayKeys} from "../../utils/ArrayUtils";

export default class Filters {
    private readonly _query: string | null;
    private readonly _sort: string | null;
    private readonly _sortBy: string[] | null;
    private readonly _available: object | null;
    private readonly _applied: object | null;

    constructor(
        query: string | null,
        sort: string | null,
        sortBy: string[] | null,
        available: object | null,
        applied: object | null
    ) {
        this._query = query;
        this._sort = sort;
        this._sortBy = sortBy ? sortBy.map(item => camelCase(item)) : null;
        this._available = available ? camelizeArrayKeys(available) : null;
        this._applied = applied ? camelizeArrayKeys(applied) : null;
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
}
