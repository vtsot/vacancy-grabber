import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import SourceSummaryCollection from "../../model/source/SourceSummaryCollection";
import Paginator from "../../model/transport-response/Paginator";
import SourceSummaryFactory, {SourceSummarySchema} from "./SourceSummaryFactory";
import * as Yup from "yup";
import SourceSummary from "../../model/source/SourceInfo";

export const SourceSummaryCollectionSchema = YupObject({
    items: YupArray(SourceSummarySchema.required()).required(),
});

export default class SourceSummaryCollectionFactory implements FactoryInterface<SourceSummaryCollection> {
    supports(data: YupInferType<typeof SourceSummaryCollectionSchema>): boolean {
        try {
            SourceSummaryCollectionSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('SourceSummaryCollectionFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof SourceSummaryCollectionSchema>): SourceSummaryCollection {
        data = SourceSummaryCollectionSchema.validateSync(data);

        const items = data.items
            ? data.items.map(item => new SourceSummaryFactory().create(item as Yup.InferType<typeof SourceSummarySchema>))
            : [] as SourceSummary[];

        return new SourceSummaryCollection(
            items,
            new Paginator(1, items.length, items.length),
            null,
        );
    }
}
