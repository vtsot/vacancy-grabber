export interface ServerValidationErrorInterface {
    code: number;
    message: string;
    errors: null | {
        global: string[] | null;
        fields: Record<string, string>;
    };
}

export interface ClientValidationErrorInterface {
    global: string[] | null;
    fields?: Record<string, string | null>;
}
