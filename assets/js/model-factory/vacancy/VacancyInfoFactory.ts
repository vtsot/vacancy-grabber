import * as Yup from 'yup';
import dayjs, {Dayjs} from 'dayjs';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import VacancyInfo from "../../model/vacancy/VacancyInfo";
import FactoryInterface from "../../types/FactoryInterface";

export const VacancyInfoValidator = {
    id: Yup.string().required(),
    text: Yup.string().required().nullable().transform((value, original) => (original === '' ? null : value)),
    url: Yup.string().required().url().nullable().transform((value, original) => (original === '' ? null : value)),
    boolean: Yup.boolean().required(),
    date: Yup.date().required().nullable(),
    number: Yup.number().required().nullable(),
};

export const VacancyInfoSchema = YupObject({
    id: VacancyInfoValidator.id,
    title: VacancyInfoValidator.text,
    description: VacancyInfoValidator.text,
    external_id: VacancyInfoValidator.text,
    url: VacancyInfoValidator.url,
    salary: VacancyInfoValidator.text,
    source_id: VacancyInfoValidator.text,
    source_name: VacancyInfoValidator.text,
    company_id: VacancyInfoValidator.text,
    company_name: VacancyInfoValidator.text,
    status: VacancyInfoValidator.text,
    notes_count: VacancyInfoValidator.number,
    viewed: VacancyInfoValidator.boolean,
    resume_sent_at: VacancyInfoValidator.date,
    created_at: VacancyInfoValidator.date,
});

export default class VacancyInfoFactory implements FactoryInterface<VacancyInfo> {
    supports(data: YupInferType<typeof VacancyInfoSchema>): boolean {
        try {
            VacancyInfoSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('VacancyInfoFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof VacancyInfoSchema>): VacancyInfo {
        data = VacancyInfoSchema.validateSync(data);

        return new VacancyInfo(
            data.id,
            data.title as string | null,
            data.description as string | null,
            data.external_id as string | null,
            data.url as string | null,
            data.salary as string | null,
            data.source_id as string | null,
            data.source_name as string | null,
            data.company_id as string | null,
            data.company_name as string | null,
            data.status as string | null,
            data.notes_count as number | null,
            data.viewed as boolean | true,
            data.resume_sent_at ? dayjs(data.resume_sent_at) : null,
            data.created_at ? dayjs(data.created_at) : null,
        );
    }
}
