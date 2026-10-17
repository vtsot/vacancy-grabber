import * as Yup from 'yup';
import dayjs, {Dayjs} from 'dayjs';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import ResumeSentDashboardInfo from "../../model/dashboard/ResumeSentDashboardInfo";
import FactoryInterface from "../../types/FactoryInterface";

export const ResumeSentDashboardInfoValidator = {
    date: Yup.date().required().nullable(),
    number: Yup.number().required().nullable(),
};

export const ResumeSentDashboardInfoSchema = YupObject({
    date: ResumeSentDashboardInfoValidator.date,
    vacancies: ResumeSentDashboardInfoValidator.number,
    resume_sent: ResumeSentDashboardInfoValidator.number,
    dead_line: ResumeSentDashboardInfoValidator.number,
});

export default class ResumeSentDashboardInfoFactory implements FactoryInterface<ResumeSentDashboardInfo> {
    supports(data: YupInferType<typeof ResumeSentDashboardInfoSchema>): boolean {
        try {
            ResumeSentDashboardInfoSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('SentResumeToAllResumeZoomableInfoFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof ResumeSentDashboardInfoSchema>): ResumeSentDashboardInfo {
        data = ResumeSentDashboardInfoSchema.validateSync(data);

        return new ResumeSentDashboardInfo(
            dayjs(data.date),
            data.vacancies as number,
            data.resume_sent as number,
            data.dead_line as number,
        );
    }
}
