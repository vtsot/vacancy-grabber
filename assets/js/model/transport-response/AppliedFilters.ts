import {camelCase, snakeCase} from "lodash";
import Paginator from "./Paginator";
import {SortByInterface} from "../../types/BaseCollectionTableOptions";
import {pushUrlParams} from "../../utils/UrlUtils";

export default class AppliedFilters {
    constructor(
        public page?: number | null,
        public perPage?: number | null,
        public query?: string | null,
        public sort?: string | null,
    ) {
        this.page = Paginator.preparePage(this.page);
        this.perPage = Paginator.preparePerPage(this.perPage);

        if (!this.sort) {
            this.sort = null;
        }

        if (!this.query) {
            this.query = null;
        }

        this.processFromUrlParams(this);
    }

    get sortBy(): SortByInterface[] {
        if (this.sort) {
            const [key, order] = this.sort.split(' ');
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
                    obj[key] = Paginator.preparePage(params[key]);
                } else if (key === 'perPage') {
                    obj[key] = Paginator.preparePerPage(params[key]);
                } else if (key === 'active') {
                    const value = Paginator.prepareBoolean(params[key]);
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
}
