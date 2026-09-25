<template>

    <v-expansion-panels :loading="loading">
        <v-expansion-panel :loading="loading">
            <template #title>
                <h3>Filters</h3>
            </template>
            <template #text>
                <vacancy-page-filters
                    @filters:reset="onFiltersReset"
                    @filters:update="onFiltersUpdate"
                    @filters:submit="onFiltersSubmit"
                    @create:button="handleDialogActivator('create', null)"
                />
            </template>
        </v-expansion-panel>
    </v-expansion-panels>

    <v-divider class="mt-2"/>

    <!--    <vacancy-page-filters-->
    <!--        @filters:update="onFiltersUpdate"-->
    <!--        @filters:submit="onFiltersSubmit"-->
    <!--        @create:button="handleDialogActivator('create', null)"-->
    <!--    />-->

    <v-data-table-server
        class="custom-table"
        v-model:page="pageStore.filters.page as number"
        v-model:items-per-page="pageStore.filters.perPage as number"
        v-model:sort-by="tableSortBy"
        v-model:items-length="pageStore.paginator.total as number"
        :loading="loading"
        :must-sort="true"
        :multi-sort="false"
        :headers="headers"
        :items="items"
        :items-per-page-options="itemsPerPageOptions"
        @update:options="onTableOptionsUpdated"
    >

        <template #item.company="{ item }">
            <div>
                <strong>{{ item.company }}</strong>
            </div>
            <div>({{ item.source }})</div>
        </template>


        <template #item.description="{ item }">
            <div>
                externalId: <strong>{{ item.externalId }}</strong>
            </div>

            <div v-if="item.salary">
                salary: <strong>{{ item.salary }}</strong>
            </div>

            <div>
                <strong><a :href="item.url" target="_blank">{{ item.title }}</a></strong>
            </div>

            <div>
                {{ item.description }}
            </div>
        </template>

        <template #item.viewed="{ item, index }">
            <v-switch
                v-model="item.viewed"
                :color="item.viewed ? 'success' : 'error'"
                :loading="loading"
                @update:model-value="handleDialogActivator('viewed', item)"
            />
        </template>

        <template #item.status="{ item, index }">
            <!--            <v-switch-->
            <!--                v-model="item.status"-->
            <!--                :color="item.status ? 'success' : 'error'"-->
            <!--                :loading="loading"-->
            <!--                @update:model-value="handleDialogActivator('status', item)"-->
            <!--            />-->
            {{ item.status }}
        </template>

        <template #item.isResumeSent="{ item, index }">
            <v-switch
                v-model="item.isResumeSent"
                :color="item.isResumeSent ? 'success' : 'error'"
                :loading="loading"
                @update:model-value="handleDialogActivator('isResumeSent', item)"
            />
            <!--            <div v-if="item.resumeSentAt">-->
            <!--                {{ formatDateTime(item.resumeSentAt) }}-->
            <!--            </div>-->
        </template>

        <template #item.resumeSentAt="{ item }">
            {{ formatDateTime(item.resumeSentAt) }}
        </template>

        <template #item.createdAt="{ item }">
            {{ formatDateTime(item.createdAt) }}
        </template>

        <template #item.actions="{ item, index }">
            index: {{ index }}
        </template>

        <!--        <template #item.name="{ item }">-->
        <!--            {{ item.name }}-->
        <!--        </template>-->

        <!--        <template #item.url="{ item }">-->
        <!--            <a :href="item.url" target="_blank">{{ item.url }}</a>-->
        <!--        </template>-->

        <!--        <template #item.active="{ item, index }">-->
        <!--            <v-switch-->
        <!--                v-model="item.active"-->
        <!--                :color="item.active ? 'success' : 'error'"-->
        <!--                :loading="loading"-->
        <!--                @update:model-value="handleDialogActivator('active', pageStore.items[index])"-->
        <!--            />-->
        <!--        </template>-->

        <template #item.action-edit="{ item, index }">
            <v-btn
                text="Edit"
                class="ml-1"
                color="primary"
                variant="tonal"
                @click.prevent="handleDialogActivator('edit', item)"
            />
        </template>

        <template #item.action-delete="{ item, index }">
            <v-btn
                text="Delete"
                class="ml-1"
                color="error"
                variant="tonal"
                @click.prevent="handleDialogActivator('delete', item)"
            />
        </template>
    </v-data-table-server>

    items =
    <pre>{{ items }}</pre>

    <vacancy-item-active-dialog
        v-if="dialogs.active.model"
        v-model:dialog="dialogs.active.model"
        :item="item as VacancyInfo"
        @confirmed="handleDialogDeActivator('active', true)"
        @canceled="handleDialogDeActivator('active', false)"
    />

    <vacancy-item-grab-dialog
        v-if="dialogs.grab.model"
        v-model:dialog="dialogs.grab.model"
        :item="item as VacancyInfo"
        @confirmed="handleDialogDeActivator('grab', true)"
        @canceled="handleDialogDeActivator('grab', false)"
    />

    <vacancy-item-create-dialog
        v-if="dialogs.create.model"
        v-model:dialog="dialogs.create.model"
        :item="item as VacancyInfo"
        @confirmed="handleDialogDeActivator('create', true)"
        @canceled="handleDialogDeActivator('create', false)"
    />

    <vacancy-item-edit-dialog
        v-if="dialogs.edit.model"
        v-model:dialog="dialogs.edit.model"
        :item="item as VacancyInfo"
        @confirmed="handleDialogDeActivator('edit', true)"
        @canceled="handleDialogDeActivator('edit', false)"
    />

    <vacancy-item-delete-dialog
        v-if="dialogs.delete.model"
        v-model:dialog="dialogs.delete.model"
        :item="item as VacancyInfo"
        @confirmed="handleDialogDeActivator('delete', true)"
        @canceled="handleDialogDeActivator('delete', false)"
    />

</template>

<script setup lang="ts">

    import {computed, onMounted, ref, watch} from "vue";
    import {usePageStore} from "../store";
    import {itemsPerPageOptions} from "../../../utils/itemsPerPageOptions";
    import VacancyPageFilters from "./VacancyPageFilters.vue";
    import VacancyItemActiveDialog from "./VacancyItemActiveDialog.vue";
    import VacancyInfo from "../../../model/vacancy/VacancyInfo";
    import VacancyItemGrabDialog from "./VacancyItemGrabDialog.vue";
    import VacancyItemEditDialog from "./VacancyItemEditDialog.vue";
    import VacancyItemDeleteDialog from "./VacancyItemDeleteDialog.vue";
    import VacancyItemCreateDialog from "./VacancyItemCreateDialog.vue";
    import BaseCollectionTableOptions, {SortByInterface} from "../../../types/BaseCollectionTableOptions";
    import {formatDateTime} from "../../../utils/DateUtils";
    import {clearFormErrors, processApiResponse} from "../../../module/HandleApiResponse";
    import VacancyInfoCollectionFactory from "../../../model-factory/vacancy/VacancyInfoCollectionFactory";
    import VacancyInfoCollection from "../../../model/vacancy/VacancyInfoCollection";
    import ErrorResponse from "../../../model/transport-response/ErrorResponse";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import _debounce from 'lodash/debounce';
    import {_cloneDeep} from "lodash/cloneDeep";


    const pageStore = usePageStore();
    const loading = computed(() => pageStore.loading);
    const flashStore = useFlashStore();

    onMounted(() => {
        pageStore.filters.pushUrlParams()
    });

    const headers = computed(() => [
        {title: 'Company/Source', key: 'company', sortable: false, width: '100px'},
        {title: 'Description', key: 'description', sortable: pageStore.hasSortBy('description')},

        {title: 'Viewed', key: 'viewed', sortable: pageStore.hasSortBy('viewed'), width: '100px'},
        {title: 'Status', key: 'status', sortable: pageStore.hasSortBy('status'), width: '100px'},
        {title: 'Is Resume Sent', key: 'isResumeSent', sortable: pageStore.hasSortBy('isResumeSent'), width: '100px'},
        {title: 'Resume Sent At', key: 'resumeSentAt', sortable: pageStore.hasSortBy('resumeSentAt'), width: '100px'},
        {title: 'Created At', key: 'createdAt', sortable: pageStore.hasSortBy('createdAt'), width: '100px'},

        {title: 'Actions', key: 'action-edit', sortable: false, width: '100px'},
        {title: '', key: 'action-delete', sortable: false, width: '100px'},
    ]);

    const item = ref(null as VacancyInfo | null);
    const items = ref([] as VacancyInfo[]);
    const loadCollection = _debounce(() => {
        clearFormErrors(pageStore.errors);
        processApiResponse(pageStore.loadCollection(), pageStore.errors, new VacancyInfoCollectionFactory())
            .then((data: VacancyInfoCollection) => {
                // todo: check _cloneDeep is required for viewed action
                //items.value = _cloneDeep(data.items); // do clone to restore action
                items.value = data.items
                pageStore.paginator = data.paginator;
                pageStore.availableSortBy = data.filters?.availableSortBy || [];
                pageStore.availableFilters = {...pageStore.availableFilters, ...data.filters?.availableFilters ?? {}};
            }, (response: ErrorResponse) => {
                flashStore.addError({text: response.message});
            });

    }, 300);


    // const items = ref(pageStore.items);
    // watch(
    //     () => pageStore.items,
    //     () => items.value = _cloneDeep(pageStore.items),
    //     {deep: true}
    // );


    const onFiltersReset = (): void => {
        onFiltersSubmit();
    }

    const onFiltersUpdate = (): void => {
        pageStore.filters.pushUrlParams();
        loadCollection();
    };

    const onFiltersSubmit = (): void => {
        pageStore.filters.page = 1;
        pageStore.filters.pushUrlParams();
        loadCollection();
    };

    const tableSortBy = ref(pageStore.filters.sortBy as SortByInterface[]);
    const onTableOptionsUpdated = (options: BaseCollectionTableOptions) => {
        const [sort] = options.sortBy || [] as SortByInterface[];
        pageStore.filters.sort = sort?.key && sort?.order ? `${sort.key} ${sort.order}` : null;

        pageStore.filters.pushUrlParams();
        loadCollection();
    };

    // dialogs
    const dialogs = ref({
        active: {model: false},
        grab: {model: false},
        edit: {model: false},
        delete: {model: false},
        create: {model: false},
    });

    const handleDialogActivator = (action: string, vacancyItem: VacancyInfo | null) => {
        item.value = vacancyItem;

        // open dialog
        dialogs.value[action] && (dialogs.value[action].model = true);
    };

    const handleDialogDeActivator = (action: string, confirmed: boolean): void => {
        // restore
        if (!confirmed) {
            item.value = null;
            //items.value = _cloneDeep(pageStore.items);
            dialogs.value[action] && (dialogs.value[action].model = false);

            return;
        }

        setTimeout(() => {
            Promise
                .all([loadCollection()])
                .then(() => {
                    item.value = null;
                    // close dialog
                    dialogs.value[action] && (dialogs.value[action].model = false);
                });
        }, 300);
    };

</script>

<!--<style scoped>-->
<!--    /* ::v-deep нужен, чтобы добраться до внутренних тегов <table> и <tr> внутри Vuetify */-->
<!--    .custom-table :deep(table) {-->
<!--        border-spacing: 0 10px !important;-->
<!--        border-collapse: separate !important;-->
<!--    }-->

<!--    /* Опционально: если нужно убрать стандартные нижние рамки Vuetify, чтобы они не мешали */-->
<!--    .custom-table :deep(td) {-->
<!--        border-bottom: none !important;-->
<!--    }-->
<!--</style>-->
