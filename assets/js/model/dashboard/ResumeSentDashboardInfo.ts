import {Dayjs} from "dayjs";

export default class ResumeSentDashboardInfo {
    constructor(
        public readonly date: Dayjs,
        public readonly vacancies: number,
        public readonly resumeSent: number,
        public readonly deadLine: number,
    ) {
    }
}
