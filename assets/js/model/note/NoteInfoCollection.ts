import NoteInfo from "./NoteInfo";
import DataCollection from "../transport-response/DataCollection";
import Paginator from "../transport-response/Paginator";
import Filters from "../transport-response/Filters";

export default class NoteInfoCollection extends DataCollection<NoteInfo> {
    constructor(
        items: NoteInfo[],
        paginator: Paginator,
        filters: Filters | null
    ) {
        super(
            items,
            paginator,
            filters
        );
    }

    get items(): NoteInfo[] {
        return super.items as NoteInfo[];
    }
}
