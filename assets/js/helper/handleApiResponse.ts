import SuccessResponse from '../model/transport-response/SuccessResponse';
import ErrorResponse from '../model/transport-response/ErrorResponse';
import _forEach from 'lodash/forEach';
import _camelCase from 'lodash/camelCase';

export default async function (request: Promise<SuccessResponse>, errors: {fields: object, global: string | null}): Promise<SuccessResponse|null> {
    return await request
        .then((r: SuccessResponse): SuccessResponse => {
            return r;
        }, (e: ErrorResponse): null => {
            if (e.status === 422) {
                _forEach(e.formError?.fields, (message: string | null, key: string) => {
                    errors.fields[_camelCase(key)] = message;
                });

            }

            errors.global = e.generalError?.message ?? null;

            return null;
        });
}
