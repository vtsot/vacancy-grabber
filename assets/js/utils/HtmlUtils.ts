export const nl2br = (text?: string): string => {
    if (!text) {
        return '';
    }
    return text.replace(/\n/g, '<br>');
}
