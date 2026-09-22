<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        :title='`Grab the "${item?.name}" vacancy`'
        :text='`Are you sure you want to grab the "${item?.name}" vacancy?`'
    />
</template>

<script setup lang="ts">
    import {usePageStore} from "../store";
    import AppConfirmDialog from "../../../component/general/app-dialog/AppConfirmDialog.vue";
    import VacancyInfo from "../../../model/vacancy/VacancyInfo";
    import {useAppFlashMessageStore} from "../../../component/general/app-flash-message/store";
    import ErrorValidation from "../../../model/transport-response/ErrorValidation";
    import handleApiResponse from "../../../module/HandleApiResponse";
    import RouteGenerator from "../../../module/RouteGenerator";
    import {snakeArrayKeys} from "../../../utils/ArrayUtils";
    import {formatDateUrl} from "../../../utils/DateUtils";
    import dayjs from "dayjs";
    import {filterUrlParams} from "../../../utils/UrlUtils";

    const props = defineProps({
        item: {
            type: VacancyInfo,
            required: true,
        },
    });

    const flashMessageStore = useAppFlashMessageStore();
    const pageStore = usePageStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });

    const onConfirmed = async () => {
        const errors = new ErrorValidation();
        const result = await handleApiResponse(pageStore.itemGrab(props.item?.id as string), errors);

        if (!result || errors.message) {
            flashMessageStore.addErrorFlash(errors.message ?? 'Unable to grab vacancy');
            emit('canceled');
            return;
        }

        flashMessageStore.addSuccessFlash('Vacancy was successfully grabbed');
        emit('confirmed');

        const today = dayjs();
        const params = filterUrlParams({startDay: formatDateUrl(today), endDay: formatDateUrl(today)})
        window.location.href = RouteGenerator.generate('web.grab-log', params);
    };

    const onCanceled = () => {
        emit('canceled');
    };
</script>
