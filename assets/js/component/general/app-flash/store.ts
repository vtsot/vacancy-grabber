import {defineStore} from 'pinia';
import {ref} from "vue";

export const FLASH_TIMEOUT = 4000;

export interface FlashInterface {
    title?: string | null,
    text?: string | null,
    prependIcon?: string | null,
    color?: string | null,
    location?: string | null,
}

export const useFlashStore = defineStore('flash-store', () => {
    // State
    const items = ref([] as FlashInterface[]);

    // Getters
    // const messages = computed(() => items.value);

    // Actions
    const clear = () => {
        items.value.splice(0, items.value.length);
    };

    const add = (message: FlashInterface) => {
        items.value.push({
            title: message.title ?? null,
            text: message.text ?? null,
            prependIcon: message.prependIcon ?? '$info',
            color: message.color ?? null,
            location: message.location ?? 'top right',
        } as FlashInterface);
    };

    const addInfo = (message: FlashInterface) => {
        add({
            title: message.title ?? null,
            text: message.text ?? null,
            prependIcon: '$info',
            color: 'info',
            location: message.location ?? null,
        } as FlashInterface);
    };

    const addSuccess = (message: FlashInterface) => {
        add({
            title: message.title ?? null,
            text: message.text ?? null,
            prependIcon: '$success',
            color: 'success',
            location: message.location ?? null,
        } as FlashInterface);
    };

    const addWarning = (message: FlashInterface) => {
        add({
            title: message.title ?? null,
            text: message.text ?? null,
            prependIcon: '$warning',
            color: 'warning',
            location: message.location ?? null,
        } as FlashInterface);
    };

    const addError = (message: FlashInterface) => {
        add({
            title: message.title ?? null,
            text: message.text ?? null,
            prependIcon: '$error',
            color: 'error',
            location: message.location ?? null,
        } as FlashInterface);
    };

    return {
        items,
        clear,
        add,
        addInfo,
        addSuccess,
        addWarning,
        addError,
    };
});

