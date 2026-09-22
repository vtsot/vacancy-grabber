import AppliedFilters from "../transport-response/AppliedFilters";

export default class SourceInfoFilters extends AppliedFilters {
    constructor(
        page?: number | null,
        perPage?: number | null,
        query?: string | null,
        sort?: string | null,
        // extends
        public active?: boolean | null,
        public startDay?: string | null,
        public endDay?: string | null,
    ) {
        super(
            page,
            perPage,
            query,
            sort,
        );

        this.processFromUrlParams(this);
    }
}
