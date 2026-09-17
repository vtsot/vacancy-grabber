import {AxiosResponse, AxiosResponseHeaders, InternalAxiosRequestConfig, RawAxiosResponseHeaders} from 'axios';
import DataCollection from "./DataCollection";
import DataCollectionFactory from "../../model-factory/transport-response/DataCollectionFactory";

export default class SuccessResponse implements AxiosResponse {
    private readonly _response: AxiosResponse;
    private readonly _dataCollection: DataCollection<object> | null;

    constructor(response: AxiosResponse) {
        const factory = new DataCollectionFactory();
        this._response = response;
        this._dataCollection = factory.supports(response.data) ? factory.create(response.data) : null;
    }

    get data(): any {
        return this._response.data;
    }

    get status(): number {
        return this._response.status;
    }

    get statusText(): string {
        return this._response.statusText;
    }

    get headers(): RawAxiosResponseHeaders | AxiosResponseHeaders {
        return this._response.headers;
    }

    get config(): InternalAxiosRequestConfig {
        return this._response.config;
    }

    get request(): any | null {
        return this._response.request;
    }

    get dataCollection(): DataCollection<object> | null {
        return this._dataCollection;
    }
}
