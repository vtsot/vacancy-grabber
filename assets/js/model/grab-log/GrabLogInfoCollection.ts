import GrabLogInfo from "./GrabLogInfo";
import DataCollection from "../transport-response/DataCollection";
import Paginator from "../transport-response/Paginator";
import Filters from "../transport-response/Filters";

export default class GrabLogInfoCollection extends DataCollection<GrabLogInfo> {
    constructor(
        items: GrabLogInfo[],
        paginator: Paginator,
        filters: Filters | null
    ) {
        super(
            items,
            paginator,
            filters
        );
    }

    get items(): GrabLogInfo[] {
        return super.items as GrabLogInfo[];
    }
}
