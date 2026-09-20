<template>

    <source-list-filters
        @filters:submit="onFiltersSubmit"
    />

    <v-data-table-server
        v-model:page="sourceListPageStore.filters.page as number"
        v-model:items-per-page="sourceListPageStore.filters.perPage as number"
        v-model:items-length="sourceListPageStore.paginator.total as number"
        :loading="loading"
        :must-sort="true"
        :multi-sort="false"
        :headers="headers"
        :items="items"
        :items-per-page-options="ItemsPerPageOptions"
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
                :color="item.active ? 'success' : 'error'"
                :loading="loading"
                @update:model-value="handleDialogActivator('active', sourceListPageStore.items[index])"
            />
        </template>

        <template #item.action-grab="{ item, index }">
            <v-btn
                v-if="item.active"
                text="Grab"
                class="ml-1"
                color="primary"
                @click.prevent="handleDialogActivator('grab', sourceListPageStore.items[index])"
            />
        </template>

        <template #item.action-edit="{ item, index }">
            <v-btn
                text="Edit"
                class="ml-1"
                color="secondary"
                @click.prevent="handleDialogActivator('edit', sourceListPageStore.items[index])"
            />
        </template>

        <template #item.action-delete="{ item, index }">
            <v-btn
                text="Delete"
                class="ml-1"
                color="error"
                @click.prevent="handleDialogActivator('delete', sourceListPageStore.items[index])"
            />
        </template>
    </v-data-table-server>

    <source-item-active-dialog
        v-if="activeDialog"
        v-model:dialog="activeDialog"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('active', true)"
        @canceled="handleDialogDeActivator('active', false)"
    />

    <source-item-grab-dialog
        v-if="grabDialog"
        v-model:dialog="grabDialog"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('grab', true)"
        @canceled="handleDialogDeActivator('grab', false)"
    />

    <source-item-edit-dialog
        v-if="editDialog"
        v-model:dialog="editDialog"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('edit', true)"
        @canceled="handleDialogDeActivator('edit', false)"
    />

    <source-item-delete-dialog
        v-if="deleteDialog"
        v-model:dialog="deleteDialog"
        :item="item as SourceInfo"
        @confirmed="handleDialogDeActivator('delete', true)"
        @canceled="handleDialogDeActivator('delete', false)"
    />

<!--    <app-confirm-dialog-->
<!--        v-if="showItemCreateDialog"-->
<!--        v-model:show="showItemCreateDialog"-->
<!--        @confirmed="onItemCreateConfirmed"-->
<!--        @canceled="onItemCreateCanceled"-->
<!--        @update:model-value="onItemCreateCanceled"-->
<!--        @keyup.enter="onItemCreateCanceled"-->
<!--    >-->
<!--        <template #title>-->
<!--            Create Source-->
<!--        </template>-->
<!--    </app-confirm-dialog>-->

    <h1>11111</h1>
    item:
    <pre>{{ item }}</pre>

</template>

<script setup lang="ts">

    import {computed, onMounted, ref, watch} from "vue";
    import {useSourceListPageStore} from "../store";
    import {ItemsPerPageOptions} from "../../../utils/ItemsPerPageOptions";
    import SourceListFilters from "./SourceListFilters.vue";
    import SourceItemActiveDialog from "./SourceItemActiveDialog.vue";
    import SourceInfo from "../../../model/source/SourceInfo";
    import _cloneDeep from 'lodash/cloneDeep';
    import _debounce from 'lodash/debounce';
    import SourceItemGrabDialog from "./SourceItemGrabDialog.vue";
    import SourceItemEditDialog from "./SourceItemEditDialog.vue";
    import SourceItemDeleteDialog from "./SourceItemDeleteDialog.vue";

    const sourceListPageStore = useSourceListPageStore();
    const loadSourceCollection = _debounce(sourceListPageStore.loadSourceCollection, 300);

    onMounted(async () => loadSourceCollection());

    const loading = computed(() => sourceListPageStore.loading);

    const headers = [
        {title: 'Name', key: 'name', sortable: true, width: '200px'},
        {title: 'URL', key: 'url', sortable: true},
        {title: 'Active', key: 'active', sortable: true, width: '100px'},
        {title: '', key: 'action-grab', sortable: false, width: '100px'},
        {title: 'Actions', key: 'action-edit', sortable: false, width: '100px'},
        {title: '', key: 'action-delete', sortable: false, width: '100px'},
    ];

    const item = ref(null as SourceInfo | null);
    const items = ref(sourceListPageStore.items);
    watch(
        () => sourceListPageStore.items,
        () => items.value = _cloneDeep(sourceListPageStore.items),
        {deep: true}
    );

    const onFiltersSubmit = () => {
        sourceListPageStore.filters.page = 1;
        loadSourceCollection();
    };

    /**
     * @param {BaseCollectionTableOptions} options
     */
    const onTableOptionsUpdated = (options) => {
        sourceListPageStore.filters.page = options.page;
        sourceListPageStore.filters.perPage = options.itemsPerPage;

        const [sortBy] = options.sortBy || [];
        sourceListPageStore.filters.sort = sortBy?.key && sortBy?.order
            ? `${sortBy.key} ${sortBy.order}`
            : null;

        loadSourceCollection();
    };

    const onItemClickEdit = (item) => {
    };

    // dialogs
    const activeDialog = ref(false);
    const grabDialog = ref(false);
    const editDialog = ref(false);
    const deleteDialog = ref(false);
    const createDialog = ref(false);
    // todo: dialogs rework to ref
    const dialogs = {
        active: activeDialog,
        grab: grabDialog,
        edit: editDialog,
        delete: deleteDialog,
        create: createDialog,
    };

    const handleDialogActivator = (action: string, sourceItem: SourceInfo) => {
        item.value = sourceItem;

        // open dialog
        dialogs[action] && (dialogs[action].value = true);
    };

    const handleDialogDeActivator = (action: string, confirmed: boolean): void => {
        // restore
        if (!confirmed) {
            item.value = null;
            items.value = _cloneDeep(sourceListPageStore.items);
            dialogs[action] && (dialogs[action].value = false);

            return;
        }

        setTimeout(() => {
            Promise.all([
                sourceListPageStore.loadSourceCollection(),
            ]).then(() => {
                item.value = null;
                // close dialog
                dialogs[action] && (dialogs[action].value = false);
            });
        }, 300);
    };


    // delete action
    const showItemDeleteDialog = ref(false);
    const actionDeleteItem = ref(null);
    const onItemDeleteClick = (item) => {
        showItemDeleteDialog.value = true;
        actionDeleteItem.value = item;
    };
    const onItemDeleteCanceled = () => {
        showItemDeleteDialog.value = false;
        actionDeleteItem.value = null;
    };
    const onItemDeleteConfirmed = () => {
        if (actionDeleteItem.value?.id) {
            sourceListPageStore.itemDelete(actionDeleteItem.value.id);
            loadSourceCollection();
        }
        // hide dialog
        showItemDeleteDialog.value = false;
        actionDeleteItem.value = null;
    };
</script>
