import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import SourceInfo from "../../model/source/SourceInfo";
import FactoryInterface from "../../types/FactoryInterface";

export const SourceInfoValidator = {
    id: Yup.string().required(),
    name: Yup.string().nullable().transform((value, original) => (original === '' ? null : value)),
    url: Yup.string().url().nullable().transform((value, original) => (original === '' ? null : value)),
    active: Yup.boolean().required().default(true),
};

export const SourceInfoSchema = YupObject({
    id: SourceInfoValidator.id,
    name: SourceInfoValidator.name,
    url: SourceInfoValidator.url,
});

export default class SourceInfoFactory implements FactoryInterface<SourceInfo> {
    supports(data: YupInferType<typeof SourceInfoSchema>): boolean {
        try {
            SourceInfoSchema.validateSync(data);

            return true;
        } catch (e) {
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
