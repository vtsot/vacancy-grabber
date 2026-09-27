import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import CompanySummaryCollection from "../../model/company/CompanySummaryCollection";
import Paginator from "../../model/transport-response/Paginator";
import CompanySummaryFactory, {CompanySummarySchema} from "./CompanySummaryFactory";
import * as Yup from "yup";
import CompanySummary from "../../model/company/CompanyInfo";

export const CompanySummaryCollectionSchema = YupObject({
    items: YupArray(CompanySummarySchema.required()).required(),
});

export default class CompanySummaryCollectionFactory implements FactoryInterface<CompanySummaryCollection> {
    supports(data: YupInferType<typeof CompanySummaryCollectionSchema>): boolean {
        try {
            CompanySummaryCollectionSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('CompanySummaryCollectionFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof CompanySummaryCollectionSchema>): CompanySummaryCollection {
        data = CompanySummaryCollectionSchema.validateSync(data);

        const items = data.items
            ? data.items.map(item => new CompanySummaryFactory().create(item as Yup.InferType<typeof CompanySummarySchema>))
            : [] as CompanySummary[];

        return new CompanySummaryCollection(
            items,
            new Paginator(1, items.length, items.length),
            null,
        );
    }
}
