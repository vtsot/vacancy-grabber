import * as Yup from 'yup';
import dayjs, {Dayjs} from 'dayjs';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import VacancyStatusDashboard from "../../model/dashboard/VacancyStatusDashboard";
import FactoryInterface from "../../types/FactoryInterface";

export const VacancyStatusDashboardValidator = {
    number: Yup.number().required().nullable().default(0),
};

export const VacancyStatusDashboardSchema = YupObject({
    total_pending: VacancyStatusDashboardValidator.number,
    total_in_progress: VacancyStatusDashboardValidator.number,
    total_done: VacancyStatusDashboardValidator.number,
});

export default class VacancyStatusDashboardFactory implements FactoryInterface<VacancyStatusDashboard> {
    supports(data: YupInferType<typeof VacancyStatusDashboardSchema>): boolean {
        try {
            VacancyStatusDashboardSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('VacancyStatusDashboardFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof VacancyStatusDashboardSchema>): VacancyStatusDashboard {
        data = VacancyStatusDashboardSchema.validateSync(data);

        return new VacancyStatusDashboard(
            data.total_pending,
            data.total_in_progress,
            data.total_done,
        );
    }
}
