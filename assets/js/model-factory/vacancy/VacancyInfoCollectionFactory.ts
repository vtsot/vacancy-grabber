import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import VacancyInfoCollection from "../../model/vacancy/VacancyInfoCollection";
import {PaginatorSchema} from "../transport-response/PaginatorFactory";
import {FiltersSchema} from "../transport-response/FiltersFactory";
import Paginator from "../../model/transport-response/Paginator";
import Filters from "../../model/transport-response/Filters";
import {VacancyInfoSchema} from "./VacancyInfoFactory";

export const VacancyInfoCollectionSchema = YupObject({
    items: YupArray(VacancyInfoSchema.required()).required(),
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
            return false;
        }
    }

    create(data: YupInferType<typeof VacancyInfoCollectionSchema>): VacancyInfoCollection {
        data = VacancyInfoCollectionSchema.validateSync(data);

        return new VacancyInfoCollection(
            data.items,
            data.meta.paginator as Paginator,
            data.meta.filters as Filters | null,
        );
    }
}
