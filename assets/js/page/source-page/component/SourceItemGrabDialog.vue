<template>
    <app-confirm-dialog
        v-if="dialog"
        v-model:dialog="dialog"
        :loading="loading"
        @confirmed="onConfirmed"
        @canceled="onCanceled"
        @keyup.esc="onCanceled"
        @keyup.enter="onConfirmed"
        leave-open
        :title='`Grab the "${item?.name}" source`'
    >
        <template #text>
            {{ 'Are you sure you want to grab the "'+ item.name +'" source?`' }}
        </template>
    </app-confirm-dialog>
</template>

<script setup lang="ts">
    import {computed} from "vue";
    import {useSourcePageStore} from "../store";
    import AppConfirmDialog from "../../../component/general/app-dialog/AppConfirmDialog.vue";
    import SourceInfo from "../../../model/source/SourceInfo";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import ErrorValidation from "../../../model/transport-response/ErrorValidation";
    import handleApiResponse from "../../../module/HandleApiResponse";
    import RouteGenerator from "../../../module/RouteGenerator";
    import {camelizeArrayKeys} from "../../../utils/ArrayUtils";
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
    const loading = computed(() => sourcePageStore.loading);
    const emit = defineEmits(['confirmed', 'canceled'])
    const dialog = defineModel('dialog', { type: Boolean, default: false });

    const onConfirmed = async () => {
        const errors = new ErrorValidation();
        const result = await handleApiResponse(sourcePageStore.grab(props.item?.id as string), errors);

        if (!result || errors.message) {
            flashStore.addError({text: errors.message ?? 'Unable to grab source'});
            emit('canceled');
            return;
        }

        const {counterVacancyCreated} = camelizeArrayKeys(result.data);

        if (counterVacancyCreated === 0) {
            flashStore.addWarning({text: 'The Source "'+ props.item?.name +'" was successfully grabbed, but no vacancies were created'});
            emit('confirmed');
            return;
        }

        flashStore.addSuccess({text: 'The Source "' + props.item?.name + '" was successfully grabbed and "' + counterVacancyCreated + '" vacancies were created'});
        emit('confirmed');

        const today = dayjs();
        const params = filterUrlParams({startDay: formatDateUrl(today), endDay: formatDateUrl(today)})
        setTimeout(() => window.location.href = RouteGenerator.generate('web.grab-log', params), 1500)
    };

    const onCanceled = () => {
        emit('canceled');
    };
</script>
