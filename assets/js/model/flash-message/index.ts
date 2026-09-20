export default class FlashMessage {
    constructor(
        public readonly text : string | null,
        public readonly color : string | null,
        public readonly location : string | null,
    ) {
    }
}
