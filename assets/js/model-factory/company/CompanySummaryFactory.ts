import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import CompanySummary from "../../model/company/CompanySummary";
import FactoryInterface from "../../types/FactoryInterface";

export const CompanySummaryValidator = {
    id: Yup.string().required(),
    name: Yup.string().nullable().transform((value, original) => (original === '' ? null : value)),
};

export const CompanySummarySchema = YupObject({
    id: CompanySummaryValidator.id,
    name: CompanySummaryValidator.name,
});

export default class CompanySummaryFactory implements FactoryInterface<CompanySummary> {
    supports(data: YupInferType<typeof CompanySummarySchema>): boolean {
        try {
            CompanySummarySchema.validateSync(data);

            return true;
        } catch (e) {
            return false;
        }
    }

    create(data: YupInferType<typeof CompanySummarySchema>): CompanySummary {
        data = CompanySummarySchema.validateSync(data);
        return new CompanySummary(
            data.id,
            data.name as string | null,
        );
    }
}
