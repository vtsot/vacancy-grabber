import {AxiosResponse, AxiosResponseHeaders, InternalAxiosRequestConfig, RawAxiosResponseHeaders} from 'axios';
import DataCollection from "./DataCollection";
import DataCollectionFactory from "../../model-factory/transport-response/DataCollectionFactory";
import _toLower from "lodash/toLower";

export default class SuccessResponse implements AxiosResponse {
    private readonly _response: AxiosResponse;
    private readonly _dataCollection: DataCollection<object> | null;

    constructor(response: AxiosResponse) {
        this._response = response;
        this._dataCollection = new DataCollectionFactory().supports(response.data)
            ? new DataCollectionFactory().create(response.data)
            : null;
    }

    get isBlob(): boolean {
        return _toLower(this._response.request.responseType) === 'blob';
    }

    // get data(): any {
    //     return this._response.data;
    // }
    get data() {
        return this.isBlob
            ? this._response.data ?? null
            : this._response.data?.data;
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

    get request() {
        return this._response.request;
    }

    get dataCollection(): DataCollection<object> | null {
        return this._dataCollection;
    }

    get error(): string|null {
        return this._response.data?.error || null;
    }
}
