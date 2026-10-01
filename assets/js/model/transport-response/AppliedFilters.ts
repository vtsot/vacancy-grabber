import {camelCase, snakeCase} from "lodash";
import Paginator from "./Paginator";
import {SortByInterface} from "../../types/BaseCollectionTableOptions";
import {pushUrlParams} from "../../utils/UrlUtils";

export default class AppliedFilters {
    constructor(
        public page: number | null = null,
        public perPage: number | null = null,
        public query: string | null = null,
        public sort: string | null = null,
    ) {
        this.page = Paginator.normalizePage(this.page);
        this.perPage = Paginator.normalizePerPage(this.perPage);

        this.processFromUrlParams(this);
    }

    set sort(value: string | null) {
        if (value && typeof value === 'string' && value.includes(' ')) {
            const [sortField, sortDirection] = value.split(' ');
            if (sortField !== value) {
                value = snakeCase(sortField) + ` ${sortDirection}`;
            }
        }

        this.sort = value;
    }

    get sortBy(): SortByInterface[] {
        if (this.sort) {
            const [rawKey, order] = this.sort.split(' ');
            const key = snakeCase(rawKey);

            return [{key, order}] as SortByInterface[];
        }

        return [] as SortByInterface[];
    }

    normalizeUrlParams(): object {
        return Object.fromEntries(
            Object.entries(this)
                // remove empty properties
                .filter(([, value]): boolean => value !== undefined && value !== null && value !== '')
                // convert property name to snake_case
                .map(([key, value]) => {
                    return [snakeCase(key), value];
                })
        );
    }

    get urlParams(): object {
        const url = new URL(window.location.href);
        return Object.fromEntries(Array
            .from(url.searchParams.entries())
            // remove empty properties
            .filter(([, value]) => value !== undefined && value !== null && value !== '')
            // convert property name to camelCase
            .map(([key, value]) => {
                return [camelCase(key), value];
            })
        );
    }

    processFromUrlParams(obj: AppliedFilters): void {
        this.processFromParams(obj, this.urlParams);
    }

    processFromParams(obj: AppliedFilters, params: object): void {

        if (Object.keys(obj).length === 0 && obj.constructor === Object) {
            return;
        }

        Object.entries(obj).forEach(([key, value]) => {
            if (params.hasOwnProperty(key)) {
                if (key === 'page') {
                    obj[key] = Paginator.normalizePage(params[key]);
                } else if (key === 'perPage') {
                    obj[key] = Paginator.normalizePerPage(params[key]);
                } else if (key === 'active') {
                    const value = Paginator.normalizeBoolean(params[key]);
                    if (typeof value === 'boolean') {
                        obj[key] = value;
                    }
                } else {
                    obj[key] = params[key];
                }
            }
        });
    }

    pushUrlParams(): void {
        pushUrlParams(this.normalizeUrlParams());
    }

    reset(): void {
        Object.keys(this).forEach(key => this[key] = null);
        this.page = Paginator.MIN_PAGE;
        this.perPage = Paginator.MIN_PER_PAGE;
    }
}
