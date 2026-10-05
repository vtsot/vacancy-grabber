import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import NoteInfoCollection from "../../model/note/NoteInfoCollection";
import PaginatorFactory, {PaginatorSchema} from "../transport-response/PaginatorFactory";
import FiltersFactory, {FiltersSchema} from "../transport-response/FiltersFactory";
import NoteInfoFactory, {NoteInfoSchema} from "./NoteInfoFactory";
import * as Yup from "yup";
import NoteInfo from "../../model/note/NoteInfo";

export const NoteInfoCollectionSchema = YupObject({
    items: YupArray(NoteInfoSchema.required()).required(),
    meta: YupObject({
        paginator: PaginatorSchema.nullable(),
        filters: FiltersSchema.nullable(),
    }),
});

export default class NoteInfoCollectionFactory implements FactoryInterface<NoteInfoCollection> {
    supports(data: YupInferType<typeof NoteInfoCollectionSchema>): boolean {
        try {
            NoteInfoCollectionSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('NoteInfoCollectionFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof NoteInfoCollectionSchema>): NoteInfoCollection {
        data = NoteInfoCollectionSchema.validateSync(data);

        const items = data.items
            ? data.items.map(item => new NoteInfoFactory().create(item as Yup.InferType<typeof NoteInfoSchema>))
            : [] as NoteInfo[];

        return new NoteInfoCollection(
            items,
            new PaginatorFactory().create(data.meta.paginator),
            data.meta.filters ? new FiltersFactory().create(data.meta.filters) : null,
        );
    }
}
