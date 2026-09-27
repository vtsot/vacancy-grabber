import CompanyInfo from "./CompanyInfo";
import DataCollection from "../transport-response/DataCollection";
import Paginator from "../transport-response/Paginator";
import Filters from "../transport-response/Filters";

export default class CompanyInfoCollection extends DataCollection<CompanyInfo> {
    constructor(
        items: CompanyInfo[],
        paginator: Paginator,
        filters: Filters | null
    ) {
        super(
            items,
            paginator,
            filters
        );
    }

    get items(): CompanyInfo[] {
        return super.items as CompanyInfo[];
    }
}
