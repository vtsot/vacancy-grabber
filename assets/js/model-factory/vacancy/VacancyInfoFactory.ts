import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import VacancyInfo from "../../model/vacancy/VacancyInfo";
import FactoryInterface from "../../types/FactoryInterface";

export const VacancyInfoValidator = {
    id: Yup.string().required(),
    name: Yup.string().nullable().transform((value, original) => (original === '' ? null : value)),
    url: Yup.string().url().nullable().transform((value, original) => (original === '' ? null : value)),
    active: Yup.boolean().required().default(true),
};

export const VacancyInfoSchema = YupObject({
    id: VacancyInfoValidator.id,
    name: VacancyInfoValidator.name,
    url: VacancyInfoValidator.url,
});

export default class VacancyInfoFactory implements FactoryInterface<VacancyInfo> {
    supports(data: YupInferType<typeof VacancyInfoSchema>): boolean {
        try {
            VacancyInfoSchema.validateSync(data);

            return true;
        } catch (e) {
            return false;
        }
    }

    create(data: YupInferType<typeof VacancyInfoSchema>): VacancyInfo {
        data = VacancyInfoSchema.validateSync(data);

        return new VacancyInfo(
            data.id,
            data.name as string | null,
            data.url as string | null,
            data.active as boolean | true,
        );
    }
}
