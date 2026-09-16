import {defineStore} from 'pinia';

export const useGlobalStore = defineStore('global', {
    state: () => ({
        init: false,
    }),

    getters: {
        isLoading(state) {
            return () => !state.init;
        },
    },
});
