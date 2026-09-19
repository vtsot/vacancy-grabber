import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import SourceDetails from "../../model/source/SourceDetails";
import FactoryInterface from "../../types/FactoryInterface";
import {SourceInfoValidator} from "./SourceInfoFactory";

export const SourceDetailsValidator = {
    id: SourceInfoValidator.id,
    name: SourceInfoValidator.name,
    url: SourceInfoValidator.url,
    active: SourceInfoValidator.active,
};

export const SourceDetailsSchema = YupObject({
    id: SourceDetailsValidator.id,
    name: SourceDetailsValidator.name,
    url: SourceDetailsValidator.url,
});

export default class SourceDetailsFactory implements FactoryInterface<SourceDetails> {
    supports(data: YupInferType<typeof SourceDetailsSchema>): boolean {
        try {
            SourceDetailsSchema.validateSync(data);

            return true;
        } catch (e) {
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
        );
    }
}
