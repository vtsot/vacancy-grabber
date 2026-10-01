<template>

    <v-row>
        <v-col cols="11">
            <v-expansion-panels :loading="loading">
                <v-expansion-panel :loading="loading">
                    <template #title><h3>Filters</h3></template>
                    <template #text>
                        <source-page-filters
                            @filters:reset="onFiltersReset"
                            @filters:update="onFiltersUpdate"
                            @filters:submit="onFiltersSubmit"
                            @create:button="handleDialogActivator('create', null)"
                        />
                    </template>
                </v-expansion-panel>
            </v-expansion-panels>
        </v-col>

        <v-col cols="1">
            <v-btn
                text="Create"
                :loading="loading"
                :disabled="loading"
                color="primary"
                class="ml-5 text-right"
                @click="handleDialogActivator('create', null)"
            />
        </v-col>
    </v-row>

    <v-divider class="mt-5"/>

    <v-data-table-server
        v-model:page="sourcePageStore.filters.page as number"
        v-model:items-per-page="sourcePageStore.filters.perPage as number"
        v-model:items-length="sourcePageStore.paginator.total as number"
        v-model:sort-by="tableSortBy"
        :loading="loading"
        :must-sort="true"
        :multi-sort="false"
        :headers="headers"
        :items="items"
        :items-per-page-options="itemsPerPageOptions"
        @update:options="onTableOptionsUpdated"
    >

        <template #item.name="{ item }">
            {{ item.name }}
        </template>

        <template #item.url="{ item }">
            <a :href="item.url" target="_blank">{{ item.url }}</a>
        </template>

        <template #item.active="{ item, index }">
            <v-switch
                v-model="item.active"
                :loading="loading"
                :true-icon="mdiCheck"
                :false-icon="mdiClose"
                color="success"
                size="x-large"
                @update:model-value="handleDialogActivator('active', sourcePageStore.items[index])"
            />
        </template>

        <template #item.action-grab="{ item, index }">
            <v-card-actions>
                <v-btn
                    :disabled="!item.active"
                    text="Grab"
                    class="ml-1"
                    color="secondary"
                    variant="tonal"
                    @click.prevent="handleDialogActivator('grab', sourcePageStore.items[index])"
                />
            </v-card-actions>
        </template>

        <template #item.created_at="{ item }">
            {{ formatDateTime(item.createdAt) }}
        </template>

        <template #item.actions="{ item, index }">
            <v-card-actions>
                <v-btn
                    text="Edit"
                    class="ml-1"
                    color="primary"
                    variant="tonal"
                    @click.prevent="handleDialogActivator('edit', sourcePageStore.items[index])"
                />

                <v-btn
                    text="Delete"
                    class="ml-1"
                    color="error"
                    variant="tonal"
                    :prepend-icon="mdiDelete"
                    @click.prevent="handleDialogActivator('delete', item)"
                />
            </v-card-actions>
        </template>

    </v-data-table-server>

    <source-item-active-dialog
        v-if="dialogs.active.model"
        v-model:dialog="dialogs.active.model"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('active', true)"
        @canceled="handleDialogDeActivator('active', false)"
    />

    <source-item-grab-dialog
        v-if="dialogs.grab.model"
        v-model:dialog="dialogs.grab.model"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('grab', true)"
        @canceled="handleDialogDeActivator('grab', false)"
    />

    <source-item-create-dialog
        v-if="dialogs.create.model"
        v-model:dialog="dialogs.create.model"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('create', true)"
        @canceled="handleDialogDeActivator('create', false)"
    />

    <source-item-edit-dialog
        v-if="dialogs.edit.model"
        v-model:dialog="dialogs.edit.model"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('edit', true)"
        @canceled="handleDialogDeActivator('edit', false)"
    />

    <source-item-delete-dialog
        v-if="dialogs.delete.model"
        v-model:dialog="dialogs.delete.model"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('delete', true)"
        @canceled="handleDialogDeActivator('delete', false)"
    />

</template>

<script setup lang="ts">

    import {computed, onMounted, ref, watch} from "vue";
    import {useSourcePageStore} from "../store";
    import {itemsPerPageOptions} from "../../../utils/itemsPerPageOptions";
    import SourcePageFilters from "./SourcePageFilters.vue";
    import SourceItemActiveDialog from "./SourceItemActiveDialog.vue";
    import SourceInfo from "../../../model/source/SourceInfo";
    import _cloneDeep from 'lodash/cloneDeep';
    import _debounce from 'lodash/debounce';
    import SourceItemGrabDialog from "./SourceItemGrabDialog.vue";
    import SourceItemEditDialog from "./SourceItemEditDialog.vue";
    import SourceItemDeleteDialog from "./SourceItemDeleteDialog.vue";
    import SourceItemCreateDialog from "./SourceItemCreateDialog.vue";
    import BaseCollectionTableOptions, {SortByInterface} from "../../../types/BaseCollectionTableOptions";
    import {useFlashStore} from "../../../component/general/app-flash/store";
    import {clearFormErrors, processApiResponse} from "../../../module/HandleApiResponse";
    import ErrorResponse from "../../../model/transport-response/ErrorResponse";
    import SourceInfoCollection from "../../../model/source/SourceInfoCollection";
    import SourceInfoCollectionFactory from "../../../model-factory/source/SourceInfoCollectionFactory";
    import {mdiCheck, mdiClose, mdiDelete} from "@mdi/js";
    import {formatDateTime} from "../../../utils/DateUtils";

    const sourcePageStore = useSourcePageStore();
    const loading = computed(() => sourcePageStore.loading);
    const flashStore = useFlashStore();

    const headers = computed(() => [
        {title: 'Name', key: 'name', sortable: sourcePageStore.hasSortBy('name'), width: '200px'},
        {title: 'URL', key: 'url', sortable: sourcePageStore.hasSortBy('url')},
        {title: 'Active', key: 'active', sortable: sourcePageStore.hasSortBy('active'), width: '100px'},
        {title: 'Grab', key: 'action-grab', sortable: false, width: '100px'},

        {title: 'Created At', key: 'created_at', sortable: sourcePageStore.hasSortBy('created_at'), width: '100px'},
        {title: 'Actions', key: 'actions', sortable: false, width: '150px'},
    ]);

    const item = ref(null as SourceInfo | null);
    const items = ref([] as SourceInfo[]);
    // made a copy, items could be changed separated from original version
    const cloneItems = () => _cloneDeep(sourcePageStore.items).map(item => ({...item, edit: false}));
    const loadCollection = _debounce(async () => {
        clearFormErrors(sourcePageStore.errors);
        await processApiResponse(sourcePageStore.loadCollection(), sourcePageStore.errors, new SourceInfoCollectionFactory())
            .then((data: SourceInfoCollection) => {
                // update the originally loaded items
                sourcePageStore.items = data.items
                sourcePageStore.paginator = data.paginator;
                sourcePageStore.availableSortBy = data.filters?.availableSortBy || [];
                sourcePageStore.availableFilters = {...sourcePageStore.availableFilters, ...data.filters?.availableFilters ?? {}};
                // update sort
                sourcePageStore.filters.sort = data.filters?.sort ?? null;
                tableSortBy.value = sourcePageStore.filters.sortBy;
            }, (response: ErrorResponse) => {
                flashStore.addError({text: response.message});
            });
    }, 300);

    watch(
        () => sourcePageStore.items,
        () => items.value = cloneItems(),
        {deep: true}
    );

    const onFiltersReset = (): void => {
        onFiltersSubmit();
    }

    const onFiltersUpdate = (): void => {
        sourcePageStore.filters.pushUrlParams();
        loadCollection();
    };

    const onFiltersSubmit = (): void => {
        sourcePageStore.filters.page = 1;
        sourcePageStore.filters.pushUrlParams();
        loadCollection();
    };

    const tableSortBy = ref(sourcePageStore.filters.sortBy as SortByInterface[]);
    const onTableOptionsUpdated = (options: BaseCollectionTableOptions) => {
        const [sort] = options.sortBy || [] as SortByInterface[];
        sourcePageStore.filters.sort = sort?.key && sort?.order ? `${sort.key} ${sort.order}` : null;
        sourcePageStore.filters.pushUrlParams();
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

    const handleDialogActivator = (action: string, activatorItem: SourceInfo | null) => {
        item.value = activatorItem;

        // open dialog
        dialogs.value[action] && (dialogs.value[action].model = true);
    };

    const handleDialogDeActivator = (action: string, confirmed: boolean): void => {
        // restore
        if (!confirmed) {
            item.value = null;
            items.value = cloneItems();
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
