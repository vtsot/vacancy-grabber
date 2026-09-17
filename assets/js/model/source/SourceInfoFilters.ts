import AppliedFilters from "../transport-response/AppliedFilters";

export default class SourceInfoFilters extends AppliedFilters {
    constructor(
        public page?: number | null,
        public perPage?: number | null,
        public query?: string | null,
        public sort?: string | null,
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
    }
}
