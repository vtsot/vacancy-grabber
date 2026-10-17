import ResumeSentDashboardInfo from "./ResumeSentDashboardInfo";
import DataCollection from "../transport-response/DataCollection";
import Paginator from "../transport-response/Paginator";
import Filters from "../transport-response/Filters";

export default class ResumeSentDashboardInfoCollection extends DataCollection<ResumeSentDashboardInfo> {
    constructor(
        items: ResumeSentDashboardInfo[],
        paginator: Paginator,
        filters: Filters | null
    ) {
        super(
            items,
            paginator,
            filters
        );
    }

    get items(): ResumeSentDashboardInfo[] {
        return super.items as ResumeSentDashboardInfo[];
    }
}
