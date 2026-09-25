import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import SourceInfoCollection from "../../model/source/SourceInfoCollection";
import PaginatorFactory, {PaginatorSchema} from "../transport-response/PaginatorFactory";
import FiltersFactory, {FiltersSchema} from "../transport-response/FiltersFactory";
import Paginator from "../../model/transport-response/Paginator";
import Filters from "../../model/transport-response/Filters";
import SourceInfoFactory, {SourceInfoSchema} from "./SourceInfoFactory";
import GrabLogInfoFactory, {GrabLogInfoSchema} from "../grab-log/GrabLogInfoFactory";
import * as Yup from "yup";
import VacancyInfo from "../../model/vacancy/VacancyInfo";
import SourceInfo from "../../model/source/SourceInfo";

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
            console.error('SourceInfoCollectionFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof SourceInfoCollectionSchema>): SourceInfoCollection {
        data = SourceInfoCollectionSchema.validateSync(data);

        const items = data.items
            ? data.items.map(item => new SourceInfoFactory().create(item as Yup.InferType<typeof SourceInfoSchema>))
            : [] as SourceInfo[];

        return new SourceInfoCollection(
            items,
            new PaginatorFactory().create(data.meta.paginator),
            data.meta.filters ? new FiltersFactory().create(data.meta.filters) : null,
        );
    }
}
