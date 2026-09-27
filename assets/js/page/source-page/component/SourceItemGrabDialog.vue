<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        :title='`Grab the "${item?.name}" source`'
        :text='`Are you sure you want to grab the "${item?.name}" source?`'
    />
</template>

<script setup lang="ts">
    import {useSourcePageStore} from "../store";
    import AppConfirmDialog from "../../../component/general/app-dialog/AppConfirmDialog.vue";
    import SourceInfo from "../../../model/source/SourceInfo";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import ErrorValidation from "../../../model/transport-response/ErrorValidation";
    import handleApiResponse from "../../../module/HandleApiResponse";
    import RouteGenerator from "../../../module/RouteGenerator";
    import {snakeArrayKeys} from "../../../utils/ArrayUtils";
    import {formatDateUrl} from "../../../utils/DateUtils";
    import dayjs from "dayjs";
    import {filterUrlParams} from "../../../utils/UrlUtils";

    const props = defineProps({
        item: {
            type: SourceInfo,
            required: true,
        },
    });

    const flashStore = useFlashStore();
    const sourcePageStore = useSourcePageStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });

    const onConfirmed = async () => {
        const errors = new ErrorValidation();
        const result = await handleApiResponse(sourcePageStore.itemGrab(props.item?.id as string), errors);

        if (!result || errors.message) {
            flashStore.addError({text: errors.message ?? 'Unable to grab source'});
            emit('canceled');
            return;
        }

        flashStore.addSuccess({text:'Source was successfully grabbed'});
        emit('confirmed');

        const today = dayjs();
        const params = filterUrlParams({startDay: formatDateUrl(today), endDay: formatDateUrl(today)})
        window.location.href = RouteGenerator.generate('web.grab-log', params);
    };

    const onCanceled = () => {
        emit('canceled');
    };
</script>
