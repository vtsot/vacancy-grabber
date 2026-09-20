/** @deprecated */
export default class FormError {
    private readonly _global: string[] | null;
    private readonly _fields: object | null;

    constructor(global: string[] | null, fields: object | null) {
        this._global = global;
        this._fields = fields;
    }

    get global(): string[] | null {
        return this._global;
    }

    get fields(): object | null {
        return this._fields;
    }
}
