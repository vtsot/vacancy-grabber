import {defineStore} from 'pinia';
import {ref} from "vue";

export interface AlertInterface {
    title?: string | null,
    text?: string | null,
    icon?: string | null,
    color?: string | null,
    closable?: boolean | null,
}

export const useAlertStore = defineStore('alert-store', () => {
    // State
    const items = ref([] as AlertInterface[]);
    const timeouts = ref([]);

    // Getters
    // const messages = computed(() => items.value);

    // Actions
    const clear = () => items.value.splice(0, items.value.length);

    const add = (message: AlertInterface) => {
        items.value.push({
            title: message.title ?? null,
            text: message.text ?? null,
            icon: message.icon ?? '$info',
            color: message.color ?? null,
            closable: message.closable ?? true,
        } as AlertInterface);
    };

    const addInfo = (message: AlertInterface) => {
        add({
            title: message.title ?? null,
            text: message.text ?? null,
            icon: '$info',
            color: 'info',
            closable: message.closable ?? true,
        } as AlertInterface);
    };

    const addSuccess = (message: AlertInterface) => {
        add({
            title: message.title ?? null,
            text: message.text ?? null,
            icon: '$success',
            color: 'success',
            closable: message.closable ?? true,
        } as AlertInterface);
    };

    const addWarning = (message: AlertInterface) => {
        add({
            title: message.title ?? null,
            text: message.text ?? null,
            icon: '$warning',
            color: 'warning',
            closable: message.closable ?? true,
        } as AlertInterface);
    };

    const addError = (message: AlertInterface) => {
        add({
            title: message.title ?? null,
            text: message.text ?? null,
            icon: '$error',
            color: 'error',
            closable: message.closable ?? true,
        } as AlertInterface);
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

