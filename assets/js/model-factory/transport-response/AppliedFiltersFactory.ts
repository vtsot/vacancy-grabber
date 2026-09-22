// import * as Yup from 'yup';
// import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
// import type FactoryInterface from '~/types/FactoryInterface';
// import type SortItemType from '~/types/SortItemType';
// import AppliedFilters from '~/model/transport-response/AppliedFilters';
// import type { RouteLocationNormalizedLoaded } from '#vue-router';
//
// export const AppliedFiltersValidator = {
//     page: Yup.number().notRequired().nullable().default(1),
//     perPage: Yup.number().notRequired().nullable().default(10),
//     string: Yup.string().notRequired().nullable().default(null).transform((value, original) => (original === '' ? null : value)),
//     boolean: Yup.boolean().notRequired().nullable().default(null),
// };
//
// export const AppliedFiltersSchema = YupObject({
//     page: AppliedFiltersValidator.page,
//     per_page: AppliedFiltersValidator.perPage,
//     sort: AppliedFiltersValidator.string,
//     query: AppliedFiltersValidator.string,
// });
//
// /** @deprecated */
// export default class AppliedFiltersFactory implements FactoryInterface<AppliedFilters> {
//     supports(data: YupInferType<typeof AppliedFiltersSchema>): boolean {
//         try {
//             AppliedFiltersSchema.validateSync(data);
//
//             return true;
//         } catch (e) {
//             return false;
//         }
//     }
//
//     create(data: YupInferType<typeof AppliedFiltersSchema>): AppliedFilters {
//         data = AppliedFiltersSchema.validateSync(data);
//
//         return new AppliedFilters(
//             data.page,
//             data.per_page,
//             this.resolveSortItems(data.sort),
//             data.query,
//         );
//     }
//
//     createFromRoute(route: RouteLocationNormalizedLoaded): AppliedFilters {
//         const page = route.query.page ? parseInt(route.query.page.toString()) : 1;
//         let perPage = route.query.per_page ? parseInt(route.query.per_page.toString()) : 10;
//
//         if (perPage > 100) {
//             perPage = 100;
//         }
//
//         return new AppliedFilters(
//             page,
//             perPage,
//             route.query.sort ? this.resolveSortItems(route.query.sort.toString()) : [],
//             route.query.query ? route.query.query.toString() : null,
//         );
//     }
//
//     resolveSortItems(sortValue: string | null): SortItemType[] {
//         const sortItems = [] as SortItemType[];
//
//         if (sortValue) {
//             const sortBy = sortValue.split(' ');
//             if (sortBy.length === 2) {
//                 sortItems.push({ key: sortBy[0], order: sortBy[1] } as SortItemType);
//             }
//         }
//
//         return sortItems;
//     }
// }
