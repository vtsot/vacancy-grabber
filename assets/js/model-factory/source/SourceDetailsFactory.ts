import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import SourceDetails from "../../model/source/SourceDetails";
import FactoryInterface from "../../types/FactoryInterface";
import dayjs from "dayjs";

export const SourceDetailsValidator = {
    id: Yup.string().required(),
    text: Yup.string().required().nullable().transform((value, original) => (original === '' ? null : value)),
    url: Yup.string().required().url().nullable().transform((value, original) => (original === '' ? null : value)),
    boolean: Yup.boolean().required(),
    date: Yup.date().required().nullable(),
    number: Yup.number().required().nullable(),
};

export const SourceDetailsSchema = YupObject({
    id: SourceDetailsValidator.id,
    name: SourceDetailsValidator.text,
    url: SourceDetailsValidator.url,
    active: SourceDetailsValidator.boolean,
    created_at: SourceDetailsValidator.date,
});

export default class SourceDetailsFactory implements FactoryInterface<SourceDetails> {
    supports(data: YupInferType<typeof SourceDetailsSchema>): boolean {
        try {
            SourceDetailsSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('SourceDetailsFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof SourceDetailsSchema>): SourceDetails {
        data = SourceDetailsSchema.validateSync(data);

        return new SourceDetails(
            data.id,
            data.name as string | null,
            data.url as string | null,
            data.active as boolean | true,
            data.created_at ? dayjs(data.created_at) : null,
        );
    }
}
