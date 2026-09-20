import {ClientValidationErrorInterface, ServerValidationErrorInterface} from "../types/ValidationErrorTypes";
import _forEach from "lodash/forEach";
import _camelCase from "lodash/camelCase";

export function parseFormErrors(response: ServerValidationErrorInterface, errors: ClientValidationErrorInterface) {
    errors.message = response.message ?? null;

    if (errors.fields) {
        _forEach(response.errors?.fields, (message, key) => {
            errors.fields![_camelCase(key as string)] = message as string;
        });
    }
}

export function clearFormErrors(errors: ClientValidationErrorInterface): void {
    errors.message = null;

    if (errors.fields) {
        _forEach(errors.fields, (message, key) => {
            errors.fields![key] = null;
        });
    }
}
