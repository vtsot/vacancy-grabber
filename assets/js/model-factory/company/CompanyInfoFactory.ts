import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import CompanyInfo from "../../model/company/CompanyInfo";
import FactoryInterface from "../../types/FactoryInterface";

export const CompanyInfoValidator = {
    id: Yup.string().required(),
    name: Yup.string().nullable().transform((value, original) => (original === '' ? null : value)),
};

export const CompanyInfoSchema = YupObject({
    id: CompanyInfoValidator.id,
    name: CompanyInfoValidator.name,
});

export default class CompanyInfoFactory implements FactoryInterface<CompanyInfo> {
    supports(data: YupInferType<typeof CompanyInfoSchema>): boolean {
        try {
            CompanyInfoSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('CompanyInfoFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof CompanyInfoSchema>): CompanyInfo {
        data = CompanyInfoSchema.validateSync(data);

        return new CompanyInfo(
            data.id,
            data.name as string | null,
        );
    }
}
