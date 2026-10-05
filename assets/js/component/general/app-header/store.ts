import {defineStore} from 'pinia';
import {mdiAccount, mdiAccountArrowLeft, mdiAccountArrowRight} from "@mdi/js";

export const useHeaderStore = defineStore('header-store', () => {

    const iconsMap: Record<string, string> = {
        'mdi-account': mdiAccount,
        'mdi-account-arrow-left': mdiAccountArrowLeft,
        'mdi-account-arrow-right': mdiAccountArrowRight,
    };

    const getIcon = (icon: string) => iconsMap[icon] || null;

    return {
        getIcon,
    };
});
