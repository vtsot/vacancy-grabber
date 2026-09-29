import * as Yup from 'yup';
import dayjs, {Dayjs} from 'dayjs';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import VacancyNote from "../../model/vacancy/VacancyNote";
import FactoryInterface from "../../types/FactoryInterface";

export const VacancyNoteValidator = {
    id: Yup.string().required(),
    text: Yup.string().required().nullable().transform((value, original) => (original === '' ? null : value)),
    date: Yup.date().required().nullable(),
};

export const VacancyNoteSchema = YupObject({
    id: VacancyNoteValidator.id,
    noted_at: VacancyNoteValidator.date,
    description: VacancyNoteValidator.text,
});

export default class VacancyNoteFactory implements FactoryInterface<VacancyNote> {
    supports(data: YupInferType<typeof VacancyNoteSchema>): boolean {
        try {
            VacancyNoteSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('VacancyNoteFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof VacancyNoteSchema>): VacancyNote {
        data = VacancyNoteSchema.validateSync(data);

        return new VacancyNote(
            data.id,
            data.noted_at ? dayjs(data.noted_at) : null,
            data.description as string | null,
        );
    }
}
