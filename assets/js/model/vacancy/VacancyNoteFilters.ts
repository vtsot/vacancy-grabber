import AppliedFilters from "../transport-response/AppliedFilters";

export default class VacancyNoteFilters extends AppliedFilters {
    constructor(
        page?: number | null ,
        perPage?: number | null,
        query?: string | null,
        sort?: string | null,
        // extends
        public startDay: string | null = null,
        public endDay: string | null = null,
        public notedAtStartDay: string | null = null,
        public notedAtEndDay: string | null = null,
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

    processFromUrlParams(obj: AppliedFilters) {
        //super.processFromUrlParams(obj);
    }
}
