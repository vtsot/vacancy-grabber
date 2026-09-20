import {ClientValidationErrorInterface} from "../../types/ValidationErrorTypes";

interface ErrorsFieldsInterface extends Record<string, string | null> {
    page?: number | null,
    perPage?: number | null,
    query?: string | null,
    sort?: string | null,
    active?: boolean | null,
    startDay?: string | null,
    endDay?: string | null,
}

export default class SourceInfoCollectionErrors implements ClientValidationErrorInterface {
    public message: string | null = null;
    public fields: ErrorsFieldsInterface = {
        page: null,
        perPage: null,
        query: null,
        sort: null,
        active: null,
        startDay: null,
        endDay: null,
    };
}

