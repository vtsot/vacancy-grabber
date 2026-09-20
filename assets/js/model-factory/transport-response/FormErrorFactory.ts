import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import FormError from "../../model/transport-response/FormError";

export const FormErrorValidator = {
    global: YupArray().of(Yup.string().required()).nullable().optional(),
    fields: YupObject().nullable().optional(),
};

export const FormErrorSchema = YupObject({
    errors: YupObject({
        global: FormErrorValidator.global,
        fields: FormErrorValidator.fields,
    }).nullable().optional(),
});

/** @deprecated */
export default class FormErrorFactory implements FactoryInterface<FormError>{
    supports(data: YupInferType<typeof FormErrorSchema>): boolean {
        try {
            FormErrorSchema.validateSync(data);

            return true;
        } catch (e) {
            return false;
        }
    }

    create(data: YupInferType<typeof FormErrorSchema>) {
        data = FormErrorSchema.validateSync(data);

        return new FormError(
            data.errors?.global ?? null,
            data.errors?.fields ?? null,
        );
    }
}
