import dayjs from "dayjs";
import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import NoteInfo from "../../model/note/NoteInfo";
import FactoryInterface from "../../types/FactoryInterface";
import {GrabLogInfoValidator} from "../grab-log/GrabLogInfoFactory";

export const NoteInfoValidator = {
    id: Yup.string().required(),
    text: Yup.string().required().nullable().transform((value, original) => (original === '' ? null : value)),
    url: Yup.string().required().url().nullable().transform((value, original) => (original === '' ? null : value)),
    date: Yup.date().required().nullable(),
};

export const NoteInfoSchema = YupObject({
    id: NoteInfoValidator.id,
    company_id: NoteInfoValidator.text,
    company_name: NoteInfoValidator.text,
    company_url: NoteInfoValidator.url,
    vacancy_id: NoteInfoValidator.text,
    description: NoteInfoValidator.text,
    created_at: NoteInfoValidator.date,
    noted_at: NoteInfoValidator.date,
});

export default class NoteInfoFactory implements FactoryInterface<NoteInfo> {
    supports(data: YupInferType<typeof NoteInfoSchema>): boolean {
        try {
            NoteInfoSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('NoteInfoFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof NoteInfoSchema>): NoteInfo {
        data = NoteInfoSchema.validateSync(data);

        return new NoteInfo(
            data.id,
            data.company_id as string | null,
            data.company_name as string | null,
            data.company_url as string | null,
            data.vacancy_id as string | null,
            data.description as string | null,
            data.created_at ? dayjs(data.created_at) : null,
            data.noted_at ? dayjs(data.noted_at) : null,
        );
    }
}
