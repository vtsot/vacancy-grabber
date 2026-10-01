import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import Filters from "../../model/transport-response/Filters";
import {camelCase} from "lodash";
import {camelizeArrayKeys} from "../../utils/ArrayUtils";

export const FiltersValidator = {
    query: Yup.string().optional().nullable().transform((value, original) => (original === '' ? null : value)),
    sort: Yup.string().optional().nullable().transform((value, original) => (original === '' ? null : value)),
    availableSortBy: YupArray().of(Yup.string().optional().transform((value, original) => (original === '' ? null : value))).optional().nullable(),
    availableFilters: Yup.mixed().optional().nullable(),
    appliedFilters: Yup.mixed().optional().nullable(),
};

export const FiltersSchema = YupObject({
    query: FiltersValidator.query,
    sort: FiltersValidator.sort,
    available_sort_by: FiltersValidator.availableSortBy,
    available_filters: FiltersValidator.availableFilters,
    applied_filters: FiltersValidator.appliedFilters,
});

export default class FiltersFactory implements FactoryInterface<Filters> {
    supports(data: YupInferType<typeof FiltersSchema>): boolean {
        try {
            FiltersSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('FiltersFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof FiltersSchema>): Filters {
        data = FiltersSchema.validateSync(data);

        return new Filters(
            data.query as string | null,
            data.sort as string | null,
            data.available_sort_by, // (data.available_sort_by ? data.available_sort_by.map(item => camelCase(item)) as string[] : null),
            (data.available_filters ? camelizeArrayKeys(data.available_filters) as object : null),
            data.applied_filters, // (data.applied_filters ? camelizeArrayKeys(data.applied_filters) as object : null),
        );
    }
}
