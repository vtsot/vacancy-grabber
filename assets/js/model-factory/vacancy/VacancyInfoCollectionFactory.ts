import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import VacancyInfoCollection from "../../model/vacancy/VacancyInfoCollection";
import PaginatorFactory, {PaginatorSchema} from "../transport-response/PaginatorFactory";
import FiltersFactory, {FiltersSchema} from "../transport-response/FiltersFactory";
import VacancyInfoFactory, {VacancyInfoSchema} from "./VacancyInfoFactory";
import VacancyInfo from "../../model/vacancy/VacancyInfo";

export const VacancyInfoCollectionSchema = YupObject({
    items: YupArray().of(VacancyInfoSchema).required(),
    meta: YupObject({
        paginator: PaginatorSchema.nullable(),
        filters: FiltersSchema.nullable(),
    }),
});

export default class VacancyInfoCollectionFactory implements FactoryInterface<VacancyInfoCollection> {
    supports(data: YupInferType<typeof VacancyInfoCollectionSchema>): boolean {
        try {
            VacancyInfoCollectionSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('VacancyInfoCollectionFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof VacancyInfoCollectionSchema>): VacancyInfoCollection {
        data = VacancyInfoCollectionSchema.validateSync(data);

        const items = data.items
            ? data.items.map(item => new VacancyInfoFactory().create(item as Yup.InferType<typeof VacancyInfoSchema>))
            : [] as VacancyInfo[];

        return new VacancyInfoCollection(
            items,
            new PaginatorFactory().create(data.meta.paginator),
            data.meta.filters ? new FiltersFactory().create(data.meta.filters) : null,
        );
    }
}
