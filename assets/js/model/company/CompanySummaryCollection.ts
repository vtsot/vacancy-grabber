import CompanySummary from "./CompanySummary";
import DataCollection from "../transport-response/DataCollection";
import Paginator from "../transport-response/Paginator";
import Filters from "../transport-response/Filters";

export default class CompanySummaryCollection extends DataCollection<CompanySummary> {
    constructor(
        items: CompanySummary[],
        paginator: Paginator,
        filters: Filters | null
    ) {
        super(
            items,
            paginator,
            filters
        );
    }

    get items(): CompanySummary[] {
        return super.items as CompanySummary[];
    }
}
