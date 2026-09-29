import VacancyNote from "./VacancyNote";
import DataCollection from "../transport-response/DataCollection";
import Paginator from "../transport-response/Paginator";
import Filters from "../transport-response/Filters";

export default class VacancyNoteCollection extends DataCollection<VacancyNote> {
    constructor(
        items: VacancyNote[],
        paginator: Paginator,
        filters: Filters | null
    ) {
        super(
            items,
            paginator,
            filters
        );
    }

    get items(): VacancyNote[] {
        return super.items as VacancyNote[];
    }
}
