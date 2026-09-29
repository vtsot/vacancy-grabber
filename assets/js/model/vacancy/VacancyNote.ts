import {Dayjs} from "dayjs";

export default class VacancyNote {
    constructor(
        public readonly id: string,
        public readonly notedAt: Dayjs | null = null,
        public readonly description: string | null = null,
    ) {
    }
}
