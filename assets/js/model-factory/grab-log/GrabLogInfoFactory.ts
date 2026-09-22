import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import GrabLogInfo from "../../model/grab-log/GrabLogInfo";
import FactoryInterface from "../../types/FactoryInterface";

export const GrabLogInfoValidator = {
    id: Yup.string().required(),
    description: Yup.string().nullable().transform((value, original) => (original === '' ? null : value)),
};

export const GrabLogInfoSchema = YupObject({
    id: GrabLogInfoValidator.id,
    description: GrabLogInfoValidator.name,
});

export default class GrabLogInfoFactory implements FactoryInterface<GrabLogInfo> {
    supports(data: YupInferType<typeof GrabLogInfoSchema>): boolean {
        try {
            GrabLogInfoSchema.validateSync(data);

            return true;
        } catch (e) {
            return false;
        }
    }

    create(data: YupInferType<typeof GrabLogInfoSchema>): GrabLogInfo {
        data = GrabLogInfoSchema.validateSync(data);

        return new GrabLogInfo(
            data.id,
            data.description as string | null,
        );
    }
}
