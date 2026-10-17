import * as Yup from 'yup';
import dayjs from 'dayjs';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import GrabLogInfo from "../../model/grab-log/GrabLogInfo";
import FactoryInterface from "../../types/FactoryInterface";

export const GrabLogInfoValidator = {
    id: Yup.string().required(),
    source: Yup.string().nullable().transform((value, original) => (original === '' ? null : value)),
    description: Yup.string().nullable().transform((value, original) => (original === '' ? null : value)),
    createdAt: Yup.string().required().nullable(),
    counterCompanyCreated: Yup.number().required().nullable(),
    counterVacancyCreated: Yup.number().required().nullable(),
    counterVacancyUpdated: Yup.number().required().nullable(),
};

export const GrabLogInfoSchema = YupObject({
    id: GrabLogInfoValidator.id,
    source: GrabLogInfoValidator.source,
    description: GrabLogInfoValidator.description,
    created_at: GrabLogInfoValidator.createdAt,
    counter_company_created: GrabLogInfoValidator.counterCompanyCreated,
    counter_vacancy_created: GrabLogInfoValidator.counterVacancyCreated,
    counter_vacancy_updated: GrabLogInfoValidator.counterVacancyUpdated,
});

export default class GrabLogInfoFactory implements FactoryInterface<GrabLogInfo> {
    supports(data: YupInferType<typeof GrabLogInfoSchema>): boolean {
        try {
            GrabLogInfoSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('GrabLogInfoFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof GrabLogInfoSchema>): GrabLogInfo {
        data = GrabLogInfoSchema.validateSync(data);

        return new GrabLogInfo(
            data.id,
            data.source as string | null,
            data.description as string | null,
            data.created_at ? dayjs(data.created_at) : null,
            data.counter_company_created as number | null,
            data.counter_vacancy_created as number | null,
            data.counter_vacancy_updated as number | null,
        );
    }
}
