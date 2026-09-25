import * as Yup from "yup";
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import GrabLogInfoCollection from "../../model/grab-log/GrabLogInfoCollection";
import PaginatorFactory, {PaginatorSchema} from "../transport-response/PaginatorFactory";
import FiltersFactory, {FiltersSchema} from "../transport-response/FiltersFactory";
import GrabLogInfoFactory, {GrabLogInfoSchema} from "./GrabLogInfoFactory";
import VacancyInfo from "../../model/vacancy/VacancyInfo";

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
            console.error('GrabLogInfoCollectionFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof GrabLogInfoCollectionSchema>): GrabLogInfoCollection {
        data = GrabLogInfoCollectionSchema.validateSync(data);

        const items = data.items
            ? data.items.map(item => new GrabLogInfoFactory().create(item as Yup.InferType<typeof GrabLogInfoSchema>))
            : [] as VacancyInfo[];

        return new GrabLogInfoCollection(
            items,
            new PaginatorFactory().create(data.meta.paginator),
            data.meta.filters ? new FiltersFactory().create(data.meta.filters) : null,
        );
    }
}
