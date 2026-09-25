import VacancyInfo from "./VacancyInfo";
import {Dayjs} from "dayjs";

export default class VacancyDetails extends VacancyInfo {
    constructor(
        public readonly id: string,
        public readonly title: string | null = null,
        public readonly description: string | null = null,
        public readonly externalId: string | null = null,
        public readonly url: string | null = null,
        public readonly salary: string | null = null,
        public readonly source: string | null = null,
        public readonly company: string | null = null,
        public readonly status: string | null = null,
        // public readonly  notes: string | null = null,
        public readonly viewed: boolean | null = null,
        public readonly resumeSentAt: Dayjs | null = null,
        public readonly createdAt: Dayjs | null = null,
    ) {
        super(id,
            title,
            description,
            externalId,
            url,
            salary,
            source,
            company,
            status,
            viewed,
            resumeSentAt,
            createdAt,
        );
    }
}
