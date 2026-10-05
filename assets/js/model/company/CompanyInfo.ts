import {Dayjs} from "dayjs";

export default class CompanyInfo {
    constructor(
        public readonly id: string,
        public readonly name: string | null,
        public readonly url: string | null,
        public readonly createdAt: Dayjs | null,
    ) {
    }
}
