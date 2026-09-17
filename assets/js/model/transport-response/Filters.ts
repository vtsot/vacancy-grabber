export default class Filters {
    constructor(
        public readonly query: string | null,
        public readonly sort: string | null,
        public readonly sortBy: string[] | null,
        public readonly available: object | null,
        public readonly applied: object | null,
    ) {
    }
}
