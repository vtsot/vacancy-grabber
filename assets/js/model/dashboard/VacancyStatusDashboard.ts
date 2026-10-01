export default class VacancyStatusDashboard {
    constructor(
        public readonly totalPending: number,
        public readonly totalInProgress: number,
        public readonly totalDone: number,
    ) {
    }
}
