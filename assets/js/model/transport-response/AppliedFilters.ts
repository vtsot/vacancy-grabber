import {snakeCase} from "lodash";

export default class AppliedFilters {
    constructor(
        public page?: number | null,
        public perPage?: number | null,
        public query?: string | null,
        public sort?: string | null,
    ) {
        if (!this.page || this.page <= 0) {
            this.page = 1;
        }

        if (!this.perPage) {
            this.perPage = 10;
        }

        if (!this.sort) {
            this.sort = null;
        }

        if (!this.query) {
            this.query = null;
        }
    }

    normalizeUrlParams(): object {
        return Object.fromEntries(
            Object.entries(this)
                // remove empty properties
                .filter(([_, value]) => value !== undefined && value !== null && value !== '')
                // convert property name to snake_case
                .map(([key, value]) => {
                    return [snakeCase(key), value];
                })
        );
    }
}
