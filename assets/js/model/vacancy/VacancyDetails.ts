import VacancyInfo from "./VacancyInfo";

export default class VacancyDetails extends VacancyInfo {
    constructor(
        public readonly id: string,
        public readonly name: string | null = null,
        public readonly url: string | null = null,
        public readonly viewed: boolean | null = null,
    ) {
        super(
            id,
            name,
            url,
            viewed,
        );
    }
}
