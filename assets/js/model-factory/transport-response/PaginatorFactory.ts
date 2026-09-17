import * as Yup from 'yup';
import {InferType as YupInferType, array as YupArray, object as YupObject} from "yup";
import FactoryInterface from '../../types/FactoryInterface';
import Paginator from '../../model/transport-response/Paginator';

export const PaginatorValidator = {
    page: Yup.number().notRequired().nullable().default(1),
    perPage: Yup.number().notRequired().nullable().default(10),
    total: Yup.number().notRequired().nullable().default(0),
};

export const PaginatorSchema = YupObject({
    page: PaginatorValidator.page,
    per_page: PaginatorValidator.perPage,
    total: PaginatorValidator.total,
});

export default class PaginatorFactory implements FactoryInterface<Paginator> {
    supports(data: YupInferType<typeof PaginatorSchema>): boolean {
        try {
            PaginatorSchema.validateSync(data);

            return true;
        } catch (e) {
            return false;
        }
    }

    create(data: YupInferType<typeof PaginatorSchema>): Paginator {
        data = PaginatorSchema.validateSync(data);

        return new Paginator(
            data.page as number,
            data.per_page as number,
            data.total as number,
        );
    }
}
