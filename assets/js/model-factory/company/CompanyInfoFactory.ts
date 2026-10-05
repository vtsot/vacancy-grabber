import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import CompanyInfo from "../../model/company/CompanyInfo";
import FactoryInterface from "../../types/FactoryInterface";
import {GrabLogInfoValidator} from "../grab-log/GrabLogInfoFactory";
import dayjs from "dayjs";

export const CompanyInfoValidator = {
    id: Yup.string().required(),
    text: Yup.string().required().nullable().transform((value, original) => (original === '' ? null : value)),
    url: Yup.string().required().url().nullable().transform((value, original) => (original === '' ? null : value)),
    date: Yup.date().required().nullable(),
};

export const CompanyInfoSchema = YupObject({
    id: CompanyInfoValidator.id,
    name: CompanyInfoValidator.text,
    url: CompanyInfoValidator.url,
    created_at: GrabLogInfoValidator.createdAt,
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
            data.url as string | null,
            data.created_at ? dayjs(data.created_at) : null,
        );
    }
}
