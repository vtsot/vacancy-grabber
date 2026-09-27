import SourceSummary from "./SourceSummary";
import DataCollection from "../transport-response/DataCollection";
import Paginator from "../transport-response/Paginator";
import Filters from "../transport-response/Filters";

export default class SourceSummaryCollection extends DataCollection<SourceSummary> {
    constructor(
        items: SourceSummary[],
        paginator: Paginator,
        filters: Filters | null
    ) {
        super(
            items,
            paginator,
            filters
        );
    }

    get items(): SourceSummary[] {
        return super.items as SourceSummary[];
    }
}
