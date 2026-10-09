const SCROLL_TIMEOUT = 200;

const FORM_ERROR_SELECTOR = '.form-error';
const FORM_FIELD_ERROR_SELECTOR = '.v-field--error';
const DATA_TABLE = 'div.v-table__wrapper';
const DIALOG_TEXT = 'div.v-overlay-container div.v-card-text';

export const scrollToTop = (
    top: number | undefined = 0
): void => {
    setTimeout((): void => {
        window.scrollTo({top: top, behavior: 'smooth', inline: 'nearest'});
    }, SCROLL_TIMEOUT);
}

export const scrollTo = (
    selector: string,
    block: ScrollLogicalPosition | undefined = 'start',
    timeout: number | undefined = SCROLL_TIMEOUT
): void => {
    setTimeout((): void => {
        document.body.querySelector(selector)?.scrollIntoView({
            behavior: 'smooth',
            block: block,
            inline: 'nearest',
        });
    }, timeout);
}

export const scrollToFormError = (): void => {
    scrollTo(FORM_ERROR_SELECTOR, 'center');
}

export const scrollToFormFieldError = (): void => {
    const selector = document.querySelector(FORM_FIELD_ERROR_SELECTOR)
        ? FORM_FIELD_ERROR_SELECTOR
        : FORM_ERROR_SELECTOR;

    scrollTo(selector, 'center');
}

export const scrollToDataTable = (): void => {
    const el = document.querySelector(DATA_TABLE);
    if (el === null) {
        scrollToTop();
        return;
    }

    const offset = 100;
    const elementPosition = el.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.scrollY - offset;
    scrollToTop(offsetPosition);
}

export const scrollToDialogText = (): void => {
    const el = document.querySelector(DIALOG_TEXT);
    if (el === null) {
        return;
    }

    setTimeout(() => {
        el.scrollTo({
            top: 0,
            behavior: 'smooth',
        });
    }, SCROLL_TIMEOUT);
}
