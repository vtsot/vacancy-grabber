import {ServerValidationErrorInterface} from "../../types/ValidationErrorTypes";

export default class ErrorResponse implements ServerValidationErrorInterface {
    public code: number;
    public message: string;
    public errors: { fields: Record<string, string> } | null;

    constructor(
        code: number,
        message: string,
        errors?: { fields: Record<string, string> } | null
    ) {
        this.code = code;
        this.message = message;
        this.errors = errors ?? null;
    }
}
