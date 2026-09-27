import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import SourceSummary from "../../model/source/SourceSummary";
import FactoryInterface from "../../types/FactoryInterface";

export const SourceSummaryValidator = {
    id: Yup.string().required(),
    name: Yup.string().nullable().transform((value, original) => (original === '' ? null : value)),
};

export const SourceSummarySchema = YupObject({
    id: SourceSummaryValidator.id,
    name: SourceSummaryValidator.name,
});

export default class SourceSummaryFactory implements FactoryInterface<SourceSummary> {
    supports(data: YupInferType<typeof SourceSummarySchema>): boolean {
        try {
            SourceSummarySchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('SourceSummaryFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof SourceSummarySchema>): SourceSummary {
        data = SourceSummarySchema.validateSync(data);

        return new SourceSummary(
            data.id,
            data.name as string | null,
        );
    }
}
