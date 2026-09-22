export default class VacancyInfo {
    constructor(
        public readonly id: string,
        public readonly name: string | null,
        public readonly url: string | null,
        public readonly active: boolean | null,
    ) {
    }
}
