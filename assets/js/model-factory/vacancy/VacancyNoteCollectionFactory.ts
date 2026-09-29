import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import VacancyNoteCollection from "../../model/vacancy/VacancyNoteCollection";
import VacancyNoteFactory, {VacancyNoteSchema} from "./VacancyNoteFactory";
import VacancyNote from "../../model/vacancy/VacancyNote";
import PaginatorFactory, {PaginatorSchema} from "../transport-response/PaginatorFactory";
import FiltersFactory, {FiltersSchema} from "../transport-response/FiltersFactory";

export const VacancyNoteCollectionSchema = YupObject({
    items: YupArray().of(VacancyNoteSchema).required(),
    meta: YupObject({
        paginator: PaginatorSchema.nullable(),
        filters: FiltersSchema.nullable(),
    }),
});

export default class VacancyNoteCollectionFactory implements FactoryInterface<VacancyNoteCollection> {
    supports(data: YupInferType<typeof VacancyNoteCollectionSchema>): boolean {
        try {
            VacancyNoteCollectionSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('VacancyNoteCollectionFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof VacancyNoteCollectionSchema>): VacancyNoteCollection {
        data = VacancyNoteCollectionSchema.validateSync(data);

        const items = data.items
            ? data.items.map(item => new VacancyNoteFactory().create(item as Yup.InferType<typeof VacancyNoteSchema>))
            : [] as VacancyNote[];

        return new VacancyNoteCollection(
            items,
            new PaginatorFactory().create(data.meta.paginator),
            data.meta.filters ? new FiltersFactory().create(data.meta.filters) : null,
        );
    }
}
