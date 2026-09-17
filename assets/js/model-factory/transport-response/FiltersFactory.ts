import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import Filters from "../../model/transport-response/Filters";

export const FiltersValidator = {
    query: Yup.string().optional().nullable().transform((value, original) => (original === '' ? null : value)),
    sort: Yup.string().optional().nullable().transform((value, original) => (original === '' ? null : value)),
    sortBy: YupArray().of(Yup.string().optional()).nullable(),
    available: YupObject().optional().nullable(),
    applied: YupObject().optional().nullable(),
};

export const FiltersSchema = YupObject({
    query: FiltersValidator.query,
    sort: FiltersValidator.sort,
    sort_by: FiltersValidator.sortBy,
    available: FiltersValidator.available,
    applied: FiltersValidator.applied,
});

export default class FiltersFactory implements FactoryInterface<Filters> {
    supports(data: YupInferType<typeof FiltersSchema>): boolean {
        try {
            FiltersSchema.validateSync(data);

            return true;
        } catch (e) {
            return false;
        }
    }

    create(data: YupInferType<typeof FiltersSchema>): Filters {
        data = FiltersSchema.validateSync(data);

        return new Filters(
            data.query as string | null,
            data.sort as string | null,
            data.sort_by as string[] | null,
            data.available as {} | null,
            data.applied as {} | null,
        );
    }
}
