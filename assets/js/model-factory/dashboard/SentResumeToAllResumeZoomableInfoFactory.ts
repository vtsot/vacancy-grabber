import * as Yup from 'yup';
import dayjs, {Dayjs} from 'dayjs';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import SentResumeToAllResumeZoomableInfo from "../../model/dashboard/SentResumeToAllResumeZoomableInfo";
import FactoryInterface from "../../types/FactoryInterface";

export const SentResumeToAllResumeZoomableInfoValidator = {
    date: Yup.date().required().nullable(),
    number: Yup.number().required().nullable(),
};

export const SentResumeToAllResumeZoomableInfoSchema = YupObject({
    date: SentResumeToAllResumeZoomableInfoValidator.date,
    vacancies: SentResumeToAllResumeZoomableInfoValidator.number,
    resume_sent: SentResumeToAllResumeZoomableInfoValidator.number,
    dead_line: SentResumeToAllResumeZoomableInfoValidator.number,
});

export default class SentResumeToAllResumeZoomableInfoFactory implements FactoryInterface<SentResumeToAllResumeZoomableInfo> {
    supports(data: YupInferType<typeof SentResumeToAllResumeZoomableInfoSchema>): boolean {
        try {
            SentResumeToAllResumeZoomableInfoSchema.validateSync(data);

            return true;
        } catch (e) {
            console.error('SentResumeToAllResumeZoomableInfoFactory:not:supports: ', e);
            return false;
        }
    }

    create(data: YupInferType<typeof SentResumeToAllResumeZoomableInfoSchema>): SentResumeToAllResumeZoomableInfo {
        data = SentResumeToAllResumeZoomableInfoSchema.validateSync(data);

        return new SentResumeToAllResumeZoomableInfo(
            dayjs(data.date),
            data.vacancies as number,
            data.resume_sent as number,
            data.dead_line as number,
        );
    }
}
