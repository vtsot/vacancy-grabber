import AppliedFilters from "../transport-response/AppliedFilters";

export default class GrabLogInfoFilters extends AppliedFilters {
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

        this.processFromUrlParams(this);
    }
}
