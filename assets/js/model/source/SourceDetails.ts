import {Dayjs} from "dayjs";
import SourceInfo from "./SourceInfo";

export default class SourceDetails extends SourceInfo {
    constructor(
        public readonly id: string,
        public readonly name: string | null,
        public readonly url: string | null,
        public readonly active: boolean | null,
        public readonly createdAt: Dayjs | null = null,
    ) {
        super(
            id,
            name,
            url,
            active,
            createdAt,
        );
    }
}
