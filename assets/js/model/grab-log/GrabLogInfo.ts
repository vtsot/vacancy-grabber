import { Dayjs } from 'dayjs';

export default class GrabLogInfo {
    constructor(
        public readonly id: string,
        public readonly source: string | null,
        public readonly description: string | null,
        public readonly createdAt: Dayjs | null,
        public readonly counterCompanyCreated: number | null,
        public readonly counterVacancyCreated: number | null,
        public readonly counterVacancyUpdated: number | null,
    ) {
    }
}
