import {defineStore} from 'pinia';
import FlashMessage from "../../../model/flash-message";

interface Message {
    text?: string,
    color?: string,
    location?: string,
}

export const useAppFlashMessageStore = defineStore('app-flash-message', {
    state: () => ({
        messages: [] as FlashMessage[],
    }),

    actions: {

        clear() {
            this.messages.splice(0, this.messages.length);
        },

        createFlash(message: Message) {
            this.messages.push(new FlashMessage(
                message.text ?? '',
                message.color ?? 'info',
                message.location ?? 'top right',
            ));
        },

        addInfoFlash(message: string) {
            this.createFlash({
                text: message,
                color: 'secondary', // 'info',
            });
        },

        addSuccessFlash(message: string) {
            this.createFlash({
                text: message,
                color: 'success',
            });
        },

        addWarningFlash(message: string) {
            this.createFlash({
                text: message,
                color: 'warning',
            });
        },

        addErrorFlash(message: string) {
            this.createFlash({
                text: message,
                color: 'error',
            });
        },
    },
});
