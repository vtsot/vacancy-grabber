import * as Yup from 'yup';
import dayjs, {Dayjs} from 'dayjs';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import SourceInfo from "../../model/source/SourceInfo";
import FactoryInterface from "../../types/FactoryInterface";

export const SourceInfoValidator = {
    id: Yup.string().required(),
    text: Yup.string().required().nullable().transform((value, original) => (original === '' ? null : value)),
    url: Yup.string().required().url().nullable().transform((value, original) => (original === '' ? null : value)),
    boolean: Yup.boolean().required(),
    date: Yup.date().required().nullable(),
    number: Yup.number().required().nullable(),
};

export const SourceInfoSchema = YupObject({
    id: SourceInfoValidator.id,
    name: SourceInfoValidator.text,
    url: SourceInfoValidator.url,
    active: SourceInfoValidator.boolean,
});

export default class SourceInfoFactory implements FactoryInterface<SourceInfo> {
    supports(data: YupInferType<typeof SourceInfoSchema>): boolean {
        try {
            SourceInfoSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('SourceInfoFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof SourceInfoSchema>): SourceInfo {
        data = SourceInfoSchema.validateSync(data);

        return new SourceInfo(
            data.id,
            data.name as string | null,
            data.url as string | null,
            data.active as boolean | true,
        );
    }
}
