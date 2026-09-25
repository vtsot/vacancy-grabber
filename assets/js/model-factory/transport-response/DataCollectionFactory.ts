import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import PaginatorFactory, {PaginatorSchema} from "./PaginatorFactory";
import FiltersFactory, {FiltersSchema} from "./FiltersFactory";
import FactoryInterface from "../../types/FactoryInterface";
import DataCollection from "../../model/transport-response/DataCollection";

export const DataCollectionValidator = {
    items: YupArray().of(YupObject()),
    meta: YupObject({
        paginator: PaginatorSchema.nullable(),
        filters: FiltersSchema.nullable(),
    }),
};

export const DataCollectionSchema = YupObject({
    items: DataCollectionValidator.items,
    meta: DataCollectionValidator.meta,
});

export default class DataCollectionFactory implements FactoryInterface<DataCollection<object>> {
    supports(data: YupInferType<typeof DataCollectionSchema>): boolean {
        try {
            DataCollectionSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('DataCollectionFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof DataCollectionSchema>): DataCollection<object> {
        data = DataCollectionSchema.validateSync(data);

        return new DataCollection(
            data.items as [],
            new PaginatorFactory().create(data.meta.paginator),
            data.meta.filters ? new FiltersFactory().create(data.meta.filters) : null,
        );
    }
}
