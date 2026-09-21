import {ClientValidationErrorInterface} from "../../types/ValidationErrorTypes";

export default class ErrorValidation implements ClientValidationErrorInterface {
    message: string | null;
    fields: Record<string, string | null>;

    constructor(
        message?: string | null,
        fields?: Record<string, string | null>
    ) {
        this.message = message || null;
        this.fields = fields || {};
    }
}
