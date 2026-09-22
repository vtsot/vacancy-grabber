import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import SourceInfoCollection from "../../model/source/SourceInfoCollection";
import {PaginatorSchema} from "../transport-response/PaginatorFactory";
import {FiltersSchema} from "../transport-response/FiltersFactory";
import Paginator from "../../model/transport-response/Paginator";
import Filters from "../../model/transport-response/Filters";
import {SourceInfoSchema} from "./SourceInfoFactory";

export const SourceInfoCollectionSchema = YupObject({
    items: YupArray(SourceInfoSchema.required()).required(),
    meta: YupObject({
        paginator: PaginatorSchema.nullable(),
        filters: FiltersSchema.nullable(),
    }),
});

export default class SourceInfoCollectionFactory implements FactoryInterface<SourceInfoCollection> {
    supports(data: YupInferType<typeof SourceInfoCollectionSchema>): boolean {
        try {
            SourceInfoCollectionSchema.validateSync(data);

            return true;
        } catch (e) {
            return false;
        }
    }

    create(data: YupInferType<typeof SourceInfoCollectionSchema>): SourceInfoCollection {
        data = SourceInfoCollectionSchema.validateSync(data);

        return new SourceInfoCollection(
            data.items,
            data.meta.paginator as Paginator,
            data.meta.filters as Filters | null,
        );
    }
}
