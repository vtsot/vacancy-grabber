import VacancyInfo from "./VacancyInfo";

export default class VacancyDetails extends VacancyInfo {
    constructor(
        public readonly id: string,
        public readonly name: string | null,
        public readonly url: string | null,
        public readonly active: boolean | null,
    ) {
        super(
            id,
            name,
            url,
            active
        );
    }
}
