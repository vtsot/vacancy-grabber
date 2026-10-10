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
        public readonly sourceId: string | null = null,
        public readonly sourceName: string | null = null,
        public readonly companyId: string | null = null,
        public readonly companyName: string | null = null,
        public readonly status: string | null = null,
        // public readonly  notes: string | null = null,
        public readonly viewed: boolean | null = null,
        public readonly resumeSentAt: Dayjs | null = null,
        public readonly deadLineAt: Dayjs | null = null,
        public readonly createdAt: Dayjs | null = null,
    ) {
        super(id,
            title,
            description,
            externalId,
            url,
            salary,
            sourceId,
            sourceName,
            companyId,
            companyName,
            status,
            viewed,
            resumeSentAt,
            deadLineAt,
            createdAt,
        );
    }
}
