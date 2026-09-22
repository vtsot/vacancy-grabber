import VacancyInfo from "./VacancyInfo";
import DataCollection from "../transport-response/DataCollection";
import Paginator from "../transport-response/Paginator";
import Filters from "../transport-response/Filters";

export default class VacancyInfoCollection extends DataCollection<VacancyInfo> {
    constructor(
        items: VacancyInfo[],
        paginator: Paginator,
        filters: Filters | null
    ) {
        super(
            items,
            paginator,
            filters
        );
    }

    get items(): VacancyInfo[] {
        return super.items as VacancyInfo[];
    }
}
