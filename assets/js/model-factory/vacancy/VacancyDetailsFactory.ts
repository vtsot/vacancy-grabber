import * as Yup from 'yup';
import dayjs, {Dayjs} from 'dayjs';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import VacancyDetails from "../../model/vacancy/VacancyDetails";
import FactoryInterface from "../../types/FactoryInterface";

export const VacancyDetailsValidator = {
    id: Yup.string().required(),
    text: Yup.string().required().nullable().transform((value, original) => (original === '' ? null : value)),
    url: Yup.string().required().url().nullable().transform((value, original) => (original === '' ? null : value)),
    boolean: Yup.boolean().required(),
    date: Yup.date().required().nullable(),
};

export const VacancyDetailsSchema = YupObject({
    id: VacancyDetailsValidator.id,
    title: VacancyDetailsValidator.text,
    description: VacancyDetailsValidator.text,
    external_id: VacancyDetailsValidator.text,
    url: VacancyDetailsValidator.url,
    salary: VacancyDetailsValidator.text,
    source_id: VacancyDetailsValidator.text,
    source_name: VacancyDetailsValidator.text,
    company_id: VacancyDetailsValidator.text,
    company_name: VacancyDetailsValidator.text,
    status: VacancyDetailsValidator.text,
    //notes: VacancyDetailsValidator.text,
    viewed: VacancyDetailsValidator.boolean,
    resume_sent_at: VacancyDetailsValidator.date,
    created_at: VacancyDetailsValidator.date,
});

export default class VacancyDetailsFactory implements FactoryInterface<VacancyDetails> {
    supports(data: YupInferType<typeof VacancyDetailsSchema>): boolean {
        try {
            VacancyDetailsSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('VacancyDetailsFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof VacancyDetailsSchema>): VacancyDetails {
        data = VacancyDetailsSchema.validateSync(data);

        return new VacancyDetails(
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
            // null, notes
            data.viewed as boolean | true,
            data.resume_sent_at ? dayjs(data.resume_sent_at) : null,
            data.created_at ? dayjs(data.created_at) : null,
        );
    }
}
