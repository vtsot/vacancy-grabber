import {Dayjs} from 'dayjs';

const FORMAT_DATE_VIEW = 'YYYY MMM DD';
const FORMAT_DATE_URL = 'YYYY-MM-DD';

const FORMAT_DATE_TIME_VIEW = 'YYYY MMM DD HH:mm';
const FORMAT_DATE_TIME_URL = 'YYYY-MM-DDTHH:mm';

export const formatDate = (
    value: Dayjs | null | undefined,
    format = FORMAT_DATE_VIEW,
): string | null => {
    return value ? value.format(format) : null;
}
export const formatDateUrl = (
    value: Dayjs | null | undefined,
    format = FORMAT_DATE_URL,
): string | null => {
    return value ? value.format(format) : null;
}


export const formatDateTime = (
    value: Dayjs | null | undefined,
    format = FORMAT_DATE_TIME_VIEW,
): string | null => {
    return value ? value.format(format) : null;
}

export const formatDateTimeUrl = (
    value: Dayjs | null | undefined,
    format = FORMAT_DATE_TIME_URL,
): string | null => {
    return value ? value.format(format) : null;
}
