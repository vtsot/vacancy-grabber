import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import ResumeSentDashboardInfoCollection from "../../model/dashboard/ResumeSentDashboardInfoCollection";
import PaginatorFactory, {PaginatorSchema} from "../transport-response/PaginatorFactory";
import FiltersFactory, {FiltersSchema} from "../transport-response/FiltersFactory";
import ResumeSentDashboardInfoFactory, {ResumeSentDashboardInfoSchema} from "./ResumeSentDashboardInfoFactory";
import ResumeSentDashboardInfo from "../../model/dashboard/ResumeSentDashboardInfo";
import Paginator from "../../model/transport-response/Paginator";

export const ResumeSentDashboardInfoCollectionSchema = YupObject({
    items: YupArray().of(ResumeSentDashboardInfoSchema).required(),
    meta: YupObject({
        paginator: PaginatorSchema.nullable(),
        filters: FiltersSchema.nullable(),
    }),
});

export default class ResumeSentDashboardInfoCollectionFactory implements FactoryInterface<ResumeSentDashboardInfoCollection> {
    supports(data: YupInferType<typeof ResumeSentDashboardInfoCollectionSchema>): boolean {
        try {
            ResumeSentDashboardInfoCollectionSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('ResumeSentDashboardInfoCollectionFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof ResumeSentDashboardInfoCollectionSchema>): ResumeSentDashboardInfoCollection {
        data = ResumeSentDashboardInfoCollectionSchema.validateSync(data);

        const items = data.items
            ? data.items.map(item => new ResumeSentDashboardInfoFactory().create(item as Yup.InferType<typeof ResumeSentDashboardInfoSchema>))
            : [] as ResumeSentDashboardInfo[];

        return new ResumeSentDashboardInfoCollection(
            items,
            new Paginator(1, items.length, items.length),
            null,
        );
    }
}
