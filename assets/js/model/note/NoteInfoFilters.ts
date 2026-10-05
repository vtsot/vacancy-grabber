import AppliedFilters from "../transport-response/AppliedFilters";

export default class NoteInfoFilters extends AppliedFilters {
    constructor(
        page?: number | null,
        perPage?: number | null,
        query?: string | null,
        sort?: string | null,
        // extends
        public company: string | null = null,
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
