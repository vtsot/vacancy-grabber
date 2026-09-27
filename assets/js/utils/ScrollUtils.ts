const SCROLL_TIMEOUT = 200;

const FORM_ERROR_SELECTOR = '.form-error';
const FORM_FIELD_ERROR_SELECTOR = '.v-field--error';

export function scrollToTop(): void {
    setTimeout((): void => {
        window.scrollTo({top: 0, behavior: 'smooth'});
    }, SCROLL_TIMEOUT);
}

export function scrollTo(selector: string, timeout: number | undefined = SCROLL_TIMEOUT): void {
    setTimeout((): void => {
        document.body.querySelector(selector)?.scrollIntoView({
            behavior: 'smooth',
            block: 'center',
            inline: 'nearest',
        });
    }, timeout);
}

export function scrollToFormError(): void {
    scrollTo(FORM_ERROR_SELECTOR);
}

export function scrollToFormFieldError(): void {
    scrollTo(
        document.querySelector(FORM_FIELD_ERROR_SELECTOR)
            ? FORM_FIELD_ERROR_SELECTOR
            : FORM_ERROR_SELECTOR
    );
}

