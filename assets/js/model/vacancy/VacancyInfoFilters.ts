import AppliedFilters from "../transport-response/AppliedFilters";

export default class VacancyInfoFilters extends AppliedFilters {
    constructor(
        page?: number | null ,
        perPage?: number | null,
        query?: string | null,
        sort?: string | null,
        // extends
        public viewed: boolean | null = null,
        public startDay: string | null = null,
        public endDay: string | null = null,
        public isResumeSent: boolean | null = null,
        public resumeSentStartDay: string | null = null,
        public resumeSentEndDay: string | null = null,
        public source: string | null = null,
        public company: string | null = null,
        public status: string | null = null,
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
