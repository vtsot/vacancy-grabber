import SentResumeToAllResumeZoomableInfo from "./SentResumeToAllResumeZoomableInfo";
import DataCollection from "../transport-response/DataCollection";
import Paginator from "../transport-response/Paginator";
import Filters from "../transport-response/Filters";

export default class SentResumeToAllResumeZoomableInfoCollection extends DataCollection<SentResumeToAllResumeZoomableInfo> {
    constructor(
        items: SentResumeToAllResumeZoomableInfo[],
        paginator: Paginator,
        filters: Filters | null
    ) {
        super(
            items,
            paginator,
            filters
        );
    }

    get items(): SentResumeToAllResumeZoomableInfo[] {
        return super.items as SentResumeToAllResumeZoomableInfo[];
    }
}
