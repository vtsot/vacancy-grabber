import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from "../../types/FactoryInterface";
import CompanyInfoCollection from "../../model/company/CompanyInfoCollection";
import PaginatorFactory, {PaginatorSchema} from "../transport-response/PaginatorFactory";
import FiltersFactory, {FiltersSchema} from "../transport-response/FiltersFactory";
import CompanyInfoFactory, {CompanyInfoSchema} from "./CompanyInfoFactory";
import * as Yup from "yup";
import CompanyInfo from "../../model/company/CompanyInfo";

export const CompanyInfoCollectionSchema = YupObject({
    items: YupArray(CompanyInfoSchema.required()).required(),
    meta: YupObject({
        paginator: PaginatorSchema.nullable(),
        filters: FiltersSchema.nullable(),
    }),
});

export default class CompanyInfoCollectionFactory implements FactoryInterface<CompanyInfoCollection> {
    supports(data: YupInferType<typeof CompanyInfoCollectionSchema>): boolean {
        try {
            CompanyInfoCollectionSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('CompanyInfoCollectionFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof CompanyInfoCollectionSchema>): CompanyInfoCollection {
        data = CompanyInfoCollectionSchema.validateSync(data);

        const items = data.items
            ? data.items.map(item => new CompanyInfoFactory().create(item as Yup.InferType<typeof CompanyInfoSchema>))
            : [] as CompanyInfo[];

        return new CompanyInfoCollection(
            items,
            new PaginatorFactory().create(data.meta.paginator),
            data.meta.filters ? new FiltersFactory().create(data.meta.filters) : null,
        );
    }
}
