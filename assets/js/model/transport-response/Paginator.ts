import {ItemsPerPageOptions} from "../../utils/ItemsPerPageOptions";

export default class Paginator {
    private readonly _maxPage: number;

    constructor(
        public readonly page?: number | null,
        public readonly perPage?: number | null,
        public readonly total?: number | null,
    ) {
        if (!this.page || this.page <= 0) {
            this.page = 1;
        }

        if (!this.perPage || this.perPage <= 0) {
            this.perPage = ItemsPerPageOptions[0]?.value;
        }

        this._maxPage = Math.ceil(total / perPage);
    }
}
