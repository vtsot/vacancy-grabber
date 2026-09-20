export interface ServerValidationErrorInterface {
    code: number;
    message: string;
    errors: null | {
        fields: Record<string, string>;
    };
}

export interface ClientValidationErrorInterface {
    message: string | null;
    fields?: Record<string, string | null>;
}
