import {AxiosResponse} from 'axios';
// import DataErrorFactory from './data-error/data-error-factory';
// import FormErrorFactory from './form-error/form-error-factory';
// import DataError from './data-error';
// import FormError from './form-error';

export default class ErrorResponse {
    private readonly _response: AxiosResponse;
    // private readonly _generalError: DataError | null;
    // private readonly _formError: FormError | null;

    constructor(response: AxiosResponse) {
        this._response = response;
        // this._generalError = DataErrorFactory.supports(response.data) ? DataErrorFactory.create(response.data) : null;
        // this._formError = FormErrorFactory.supports(response.data) ? FormErrorFactory.create(response.data) : null;
    }

    get response() {
        return this._response;
    }

    get status() {
        return this._response.status;
    }

    // get generalError() {
    //     return this._generalError;
    // }
    //
    // get formError() {
    //     return this._formError;
    // }
}
