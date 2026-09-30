import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import SentResumeToAllResumeZoomableInfoCollection from "../../model/dashboard/SentResumeToAllResumeZoomableInfoCollection";
import PaginatorFactory, {PaginatorSchema} from "../transport-response/PaginatorFactory";
import FiltersFactory, {FiltersSchema} from "../transport-response/FiltersFactory";
import SentResumeToAllResumeZoomableInfoFactory, {SentResumeToAllResumeZoomableInfoSchema} from "./SentResumeToAllResumeZoomableInfoFactory";
import SentResumeToAllResumeZoomableInfo from "../../model/dashboard/SentResumeToAllResumeZoomableInfo";
import Paginator from "../../model/transport-response/Paginator";

export const SentResumeToAllResumeZoomableInfoCollectionSchema = YupObject({
    items: YupArray().of(SentResumeToAllResumeZoomableInfoSchema).required(),
    meta: YupObject({
        paginator: PaginatorSchema.nullable(),
        filters: FiltersSchema.nullable(),
    }),
});

export default class SentResumeToAllResumeZoomableInfoCollectionFactory implements FactoryInterface<SentResumeToAllResumeZoomableInfoCollection> {
    supports(data: YupInferType<typeof SentResumeToAllResumeZoomableInfoCollectionSchema>): boolean {
        try {
            SentResumeToAllResumeZoomableInfoCollectionSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('SentResumeToAllResumeZoomableInfoCollectionFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof SentResumeToAllResumeZoomableInfoCollectionSchema>): SentResumeToAllResumeZoomableInfoCollection {
        data = SentResumeToAllResumeZoomableInfoCollectionSchema.validateSync(data);

        const items = data.items
            ? data.items.map(item => new SentResumeToAllResumeZoomableInfoFactory().create(item as Yup.InferType<typeof SentResumeToAllResumeZoomableInfoSchema>))
            : [] as SentResumeToAllResumeZoomableInfo[];

        return new SentResumeToAllResumeZoomableInfoCollection(
            items,
            new Paginator(1, items.length, items.length),
            null,
        );
    }
}
