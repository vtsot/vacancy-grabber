import SourceInfo from "./SourceInfo";
import DataCollection from "../transport-response/DataCollection";
import Paginator from "../transport-response/Paginator";
import Filters from "../transport-response/Filters";

export default class SourceInfoCollection extends DataCollection<SourceInfo> {
    constructor(items: SourceInfo[], paginator: Paginator, filters: Filters | null) {
        super(items, paginator, filters);
    }

    get items(): SourceInfo[] {
        return super.items as SourceInfo[];
    }
}
