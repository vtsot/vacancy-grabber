import {ClientValidationErrorInterface, ServerValidationErrorInterface} from "../types/ValidationErrorTypes";
import _forEach from "lodash/forEach";
import _camelCase from "lodash/camelCase";
import _transform from 'lodash/transform';
import _isArray from 'lodash/isArray';
import _isObject from 'lodash/isObject';
import _snakeCase from 'lodash/snakeCase';
import SuccessResponse from "../model/transport-response/SuccessResponse";
import ErrorResponse from "../model/transport-response/ErrorResponse";
import FactoryInterface from "../types/FactoryInterface";
import handleApiResponse from "./HandleApiResponse";

const HTTP_UNPROCESSABLE_ENTITY = 422;

export type Violation = {
    [key: string]: object | string | null,
}

const assignErrors = (errors: object, error: Violation): void => {
    _forEach(error, (message: object | string | null, key: string) => {
        if (_isObject(message)) {
            assignErrors(errors[_camelCase(key as string)], message as Violation);
        } else if (message) {
            errors[_camelCase(key as string)] = message;
        }
    });
}

export const parseFormErrors = (
    response: ServerValidationErrorInterface,
    errors: ClientValidationErrorInterface
): void => {
    errors.message = response.message ?? null;

    if (errors.fields) {
        _forEach(response.errors?.fields, (message: object | string | null, key: string) => {
            if (_isObject(message)) {
                assignErrors(errors.fields![_camelCase(key as string)], message as Violation);
            } else if (message) {
                errors.fields![_camelCase(key as string)] = message as string;
            }
        });
    }
}

export const parseErrorValidation = (
    source: ClientValidationErrorInterface,
    errors: ClientValidationErrorInterface,
    code?: number | null,
): void => {
    parseFormErrors({
        code: code || 0,
        message: source.message,
        errors: {
            fields: source.fields,
        }
    } as ServerValidationErrorInterface, errors);
}

export const clearFormErrors = (
    errors: ClientValidationErrorInterface
): void => {
    errors.message = null;

    if (errors.fields) {
        _forEach(errors.fields, (message, key) => {
            errors.fields![key] = null;
        });
    }
}

export const normalizeFormToRequest = obj => _transform(obj, (result: any[] | {
    [index: string]: any
}, value, key: string | number, target) => {
    const snakeKey = _isArray(target) ? key : _snakeCase(key as string);

    result[snakeKey] = _isObject(value) ? normalizeFormToRequest(value) : value;
});

export const processApiResponse = async <T>(
    request: Promise<SuccessResponse>,
    errors: ClientValidationErrorInterface,
    factory: FactoryInterface<any>,
): Promise<T | null> => {
    const response = await handleApiResponse(request, errors);

    // catch client error responses
    const validationError = await request.catch((error: ServerValidationErrorInterface) => error) as ServerValidationErrorInterface;
    if (validationError.code >= 400
        && validationError.code <= 499
    ) {
        parseErrorValidation(validationError, errors);
        throw new Error(errors.message as string);
    }

    if (!response?.data || !factory.supports(response.data)) {
        parseErrorValidation({message: 'Data received from the server does not match the viewed factory'}, errors);
        throw new Error(errors.message as string);
    }

    return factory.create(response.data);
}

export default async function (
    request: Promise<SuccessResponse>,
    errors: ClientValidationErrorInterface
): Promise<SuccessResponse | null> {
    const handleErrorResponse = (e: ErrorResponse): null => {
        parseFormErrors({
            code: e.code,
            message: e.message,
            errors: e.errors
        } as ServerValidationErrorInterface, errors);

        return null;
    };

    return await request
        .then((r: SuccessResponse): SuccessResponse => {
            if (r.error) {
                parseFormErrors({
                    message: r.error
                } as ServerValidationErrorInterface, errors);
            }

            return r;
        }, handleErrorResponse)
        .catch(handleErrorResponse);
}
