import {defineStore} from 'pinia';

export const useGlobalStore = defineStore('global-store', {
    state: () => ({
        init: false,
    }),

    getters: {
        isLoading(state) {
            return () => !state.init;
        },
    },
});
