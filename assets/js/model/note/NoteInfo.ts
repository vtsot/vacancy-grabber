import {Dayjs} from "dayjs";

export default class NoteInfo {
    constructor(
        public readonly id: string,
        public readonly companyId: string | null,
        public readonly companyName : string | null,
        public readonly companyUrl : string | null,
        public readonly description: string | null,
        public readonly createdAt: Dayjs | null,
    ) {
    }
}
