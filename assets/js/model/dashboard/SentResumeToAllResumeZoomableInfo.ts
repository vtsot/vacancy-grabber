import {Dayjs} from "dayjs";

export default class SentResumeToAllResumeZoomableInfo {
    constructor(
        public readonly date: Dayjs,
        public readonly vacancies: number,
        public readonly resumeSent: number,
    ) {
    }
}
