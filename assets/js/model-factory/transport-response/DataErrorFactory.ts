import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import DataError from "../../model/transport-response/DataError";
import FactoryInterface from "../../types/FactoryInterface";

export const DataErrorValidator = {
    code: Yup.number().required(),
    message: Yup.string().required(),
};

export const DataErrorSchema = YupObject({
    code: DataErrorValidator.code,
    message: DataErrorValidator.message,
});

export default class DataErrorFactory implements FactoryInterface<DataError>{
    supports(data: YupInferType<typeof DataErrorSchema>) {
        try {
            DataErrorSchema.validateSync(data);

            return true;
        } catch (e) {
            return false;
        }
    }

    create(data: YupInferType<typeof DataErrorSchema>) {
        data = DataErrorSchema.validateSync(data);

        return new DataError(
            data.code,
            data.message,
        );
    }
}
