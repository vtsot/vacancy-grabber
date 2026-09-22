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
    import {useSourceListPageStore} from "../store";
    import AppConfirmDialog from "../../../component/general/app-dialog/AppConfirmDialog.vue";
    import SourceInfo from "../../../model/source/SourceInfo";
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
            type: SourceInfo,
            required: true,
        },
    });

    const flashMessageStore = useAppFlashMessageStore();
    const sourceListPageStore = useSourceListPageStore();
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });

    const onConfirmed = async () => {
        const errors = new ErrorValidation();
        const result = await handleApiResponse(sourceListPageStore.itemGrab(props.item?.id as string), errors);

        if (!result || errors.message) {
            flashMessageStore.addErrorFlash(errors.message ?? 'Unable to grab source');
            emit('canceled');
            return;
        }

        flashMessageStore.addSuccessFlash('Source was successfully grabbed');
        emit('confirmed');

        const today = dayjs();
        const params = filterUrlParams({startDay: formatDateUrl(today), endDay: formatDateUrl(today)})
        window.location.href = RouteGenerator.generate('web.grab-log.list', params);
    };

    const onCanceled = () => {
        emit('canceled');
    };
</script>
