import {AxiosResponse} from 'axios';
import DataError from "./DataError";
import DataErrorFactory from "../../model-factory/transport-response/DataErrorFactory";
import FormError from "./FormError";
import FormErrorFactory from "../../model-factory/transport-response/FormErrorFactory";

const dataErrorFactory = new DataErrorFactory();
const formErrorFactory = new FormErrorFactory();

/** @deprecated */
export default class ErrorResponse {
    private readonly _response: AxiosResponse;
    private readonly _generalError: DataError | null;
    private readonly _formError: FormError | null;

    constructor(response: AxiosResponse) {

        console.log('dataErrorFactory.supports(response.data)   : ', dataErrorFactory.supports(response.data));
        console.log('dataErrorFactory.create(response.data)     : ', dataErrorFactory.create(response.data));
        console.log('--------------------------');
        console.log('formErrorFactory.supports(response.data)   : ', formErrorFactory.supports(response.data));
        console.log('ormErrorFactory.create(response.data)      : ', formErrorFactory.create(response.data));
        console.log('--------------------------');

        this._response = response;
        this._generalError = dataErrorFactory.supports(response.data) ? dataErrorFactory.create(response.data) : null;
        this._formError = formErrorFactory.supports(response.data) ? formErrorFactory.create(response.data) : null;
    }

    get response() {
        return this._response;
    }

    get status() {
        return this._response.status;
    }

    get generalError() {
        return this._generalError;
    }

    get formError() {
        return this._formError;
    }
}
