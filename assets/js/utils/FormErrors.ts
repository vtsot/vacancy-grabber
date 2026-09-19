// import type { ClientValidationErrorInterface, ServerValidationErrorInterface } from '~/types/validationErrorTypes';

import {Ref} from "vue";
import {ClientValidationErrorInterface, ServerValidationErrorInterface} from "../types/ValidationErrorTypes";
import _camelCase from "lodash/camelCase";
import _forEach from "lodash/forEach";

export function parseFormErrors(response: ServerValidationErrorInterface, errors: Ref<ClientValidationErrorInterface>): void {
    errors.value.global = response.errors?.global ?? null;

    if (errors.value.fields) {
        _forEach(response.errors?.fields, (message, key) => {
            errors.value.fields![_camelCase(key as string)] = message as string;
        });
    }
}

export function clearFormErrors(errors: Ref<ClientValidationErrorInterface>): void {
    errors.value.global = null;

    if (errors.value.fields) {
        _forEach(errors.value.fields, (message, key) => {
            errors.value.fields![key] = null;
        });
    }
}
