import AppliedFilters from "../transport-response/AppliedFilters";

export default class CompanyInfoFilters extends AppliedFilters {
    constructor(
        page?: number | null,
        perPage?: number | null,
        query?: string | null,
        sort?: string | null,
        // extends
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
