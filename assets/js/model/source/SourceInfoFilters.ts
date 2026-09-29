import AppliedFilters from "../transport-response/AppliedFilters";

export default class SourceInfoFilters extends AppliedFilters {
    constructor(
        page?: number | null,
        perPage?: number | null,
        query?: string | null,
        sort?: string | null,
        // extends
        public active: boolean | null = null,
        public startDay: string | null = null,
        public endDay: string | null = null,
    ) {
        super(
            page,
            perPage,
            query,
            sort,
        );

        // todo: remove execution from constructor
        this.processFromUrlParams(this);
    }
}
