import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import VacancyDetails from "../../model/vacancy/VacancyDetails";
import FactoryInterface from "../../types/FactoryInterface";
import {VacancyInfoValidator} from "./VacancyInfoFactory";

export const VacancyDetailsValidator = {
    id: VacancyInfoValidator.id,
    name: VacancyInfoValidator.name,
    url: VacancyInfoValidator.url,
    active: VacancyInfoValidator.active,
};

export const VacancyDetailsSchema = YupObject({
    id: VacancyDetailsValidator.id,
    name: VacancyDetailsValidator.name,
    url: VacancyDetailsValidator.url,
});

export default class VacancyDetailsFactory implements FactoryInterface<VacancyDetails> {
    supports(data: YupInferType<typeof VacancyDetailsSchema>): boolean {
        try {
            VacancyDetailsSchema.validateSync(data);

            return true;
        } catch (e) {
            return false;
        }
    }

    create(data: YupInferType<typeof VacancyDetailsSchema>): VacancyDetails {
        data = VacancyDetailsSchema.validateSync(data);

        return new VacancyDetails(
            data.id,
            data.name as string | null,
            data.url as string | null,
            data.active as boolean | true,
        );
    }
}
