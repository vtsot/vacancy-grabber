import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import GrabLogInfoCollection from "../../model/grab-log/GrabLogInfoCollection";
import {PaginatorSchema} from "../transport-response/PaginatorFactory";
import {FiltersSchema} from "../transport-response/FiltersFactory";
import Paginator from "../../model/transport-response/Paginator";
import Filters from "../../model/transport-response/Filters";
import {GrabLogInfoSchema} from "./GrabLogInfoFactory";

export const GrabLogInfoCollectionSchema = YupObject({
    items: YupArray(GrabLogInfoSchema.required()).required(),
    meta: YupObject({
        paginator: PaginatorSchema.nullable(),
        filters: FiltersSchema.nullable(),
    }),
});

export default class GrabLogInfoCollectionFactory implements FactoryInterface<GrabLogInfoCollection> {
    supports(data: YupInferType<typeof GrabLogInfoCollectionSchema>): boolean {
        try {
            GrabLogInfoCollectionSchema.validateSync(data);

            return true;
        } catch (e) {
            return false;
        }
    }

    create(data: YupInferType<typeof GrabLogInfoCollectionSchema>): GrabLogInfoCollection {
        data = GrabLogInfoCollectionSchema.validateSync(data);

        return new GrabLogInfoCollection(
            data.items,
            data.meta.paginator as Paginator,
            data.meta.filters as Filters | null,
        );
    }
}
