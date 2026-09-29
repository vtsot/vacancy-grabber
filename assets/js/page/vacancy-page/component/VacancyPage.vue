<template>

    <v-row>
        <v-col cols="11">
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
        v-model:page="vacancyPageStore.filters.page as number"
        v-model:items-per-page="vacancyPageStore.filters.perPage as number"
        v-model:items-length="vacancyPageStore.paginator.total as number"
        v-model:sort-by="tableSortBy"
        :loading="loading"
        :must-sort="true"
        :multi-sort="false"
        :headers="headers"
        :items="items"
        :items-per-page-options="itemsPerPageOptions"
        @update:options="onTableOptionsUpdated"
    >

        <template #item.company="{ item }">
            <div v-if="item.companyName">
                <strong>{{ item.companyName }}</strong>
            </div>
            <div v-if="item.sourceName">
                ({{ item.sourceName }})
            </div>
        </template>


        <template #item.description="{ item }">

            <div class="mt-5">
                <template v-if="item.externalId">
                    externalId: <strong>{{ item.externalId }}</strong>
                </template>
            </div>

            <div v-if="item.salary">
                salary: <strong>{{ item.salary }}</strong>
            </div>

            <div v-if="item.title">
                <strong v-if="item.url">
                    <a :href="item.url" target="_blank">{{ item.title }}</a>
                </strong>
                <strong v-else>{{ item.title }}</strong>
            </div>

            <div>
                {{ item.description }}
            </div>

            <v-card-actions class="mb-5">
                <v-btn
                    :prepend-icon="mdiNotePlus"
                    variant="tonal"
                    color="primary"
                    @click.prevent="handleDialogActivator('noteCreate', item)"
                    class="ml-5"
                >
                    Add Note
                </v-btn>

                <v-btn
                    :disabled="item.notesCount == 0"
                    :prepend-icon="mdiViewList"
                    variant="tonal"
                    color="primary"
                    @click.prevent="handleDialogActivator('noteList', item)"
                >
                    Notes: {{ item.notesCount }}
                </v-btn>
            </v-card-actions>
        </template>

        <template #item.viewed="{ item, index }">
            <v-switch
                v-model="item.viewed"
                :loading="loading"
                :true-icon="mdiCheck"
                :false-icon="mdiClose"
                color="success"
                size="x-large"
                @update:model-value="handleDialogActivator('viewed', itemsLoaded[index])"
            />
        </template>

        <template #item.status="{ item, index }">
            <v-btn
                :prepend-icon="mdiFormSelect"
                variant="tonal"
                color="primary"
                @click.prevent="handleDialogActivator('status', item)"
            >
                {{ item.status }}
            </v-btn>
        </template>

<!--        <template #item.isResumeSent="{ item, index }">-->
<!--            <v-switch-->
<!--                v-model="item.isResumeSent"-->
<!--                :loading="loading"-->
<!--                :true-icon="mdiCheck"-->
<!--                :false-icon="mdiClose"-->
<!--                color="success"-->
<!--                size="x-large"-->
<!--                @update:model-value="handleDialogActivator('resumeSent', item)"-->
<!--            />-->
<!--        </template>-->

        <template #item.resumeSentAt="{ item }">
            <v-switch
                v-if="!item.isResumeSent"
                v-model="item.isResumeSent"
                :loading="loading"
                :true-icon="mdiCheck"
                :false-icon="mdiClose"
                color="success"
                size="x-large"
                @update:model-value="handleDialogActivator('resumeSent', item)"
            />
            <v-btn
                v-else
                color="success"
                variant="tonal"
                prepend-icon="$success"
                @click="handleDialogActivator('resumeSent', item)"
            >
                SentAt: {{ formatDateTime(item.resumeSentAt) }}
            </v-btn>
        </template>

        <template #item.createdAt="{ item }">
            {{ formatDateTime(item.createdAt) }}
        </template>

        <template #item.actions="{ item, index }">
            <v-card-actions>
                <v-btn
                    text="Edit"
                    class="ml-1"
                    color="primary"
                    variant="tonal"
                    :prepend-icon="mdiPencil"
                    @click.prevent="handleDialogActivator('edit', item)"
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

    <vacancy-item-viewed-dialog
        v-if="dialogs.viewed.model"
        v-model:dialog="dialogs.viewed.model"
        :item="item as VacancyInfo"
        @confirmed="handleDialogDeActivator('viewed', true)"
        @canceled="handleDialogDeActivator('viewed', false)"
    />

    <vacancy-item-resume-sent-dialog
        v-if="dialogs.resumeSent.model"
        v-model:dialog="dialogs.resumeSent.model"
        :item="item as VacancyInfo"
        @confirmed="handleDialogDeActivator('resumeSent', true)"
        @canceled="handleDialogDeActivator('resumeSent', false)"
    />

    <vacancy-item-status-dialog
        v-if="dialogs.status.model"
        v-model:dialog="dialogs.status.model"
        :item="item as VacancyInfo"
        @confirmed="handleDialogDeActivator('status', true)"
        @canceled="handleDialogDeActivator('status', false)"
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

    <vacancy-item-note-list-dialog
        v-if="dialogs.noteList.model"
        v-model:dialog="dialogs.noteList.model"
        :item="item as VacancyInfo"
        @confirmed="handleDialogDeActivator('noteList', true)"
        @canceled="handleDialogDeActivator('noteList', false)"
    />

    <vacancy-item-note-create-dialog
        v-if="dialogs.noteCreate.model"
        v-model:dialog="dialogs.noteCreate.model"
        :item="item as VacancyInfo"
        @confirmed="handleDialogDeActivator('noteCreate', true)"
        @canceled="handleDialogDeActivator('noteCreate', false)"
    />

</template>

<script setup lang="ts">

    import {computed, onMounted, ref, watch} from "vue";
    import {useVacancyPageStore} from "../store";
    import {itemsPerPageOptions} from "../../../utils/itemsPerPageOptions";
    import VacancyPageFilters from "./VacancyPageFilters.vue";
    import VacancyInfo from "../../../model/vacancy/VacancyInfo";
    import VacancyItemViewedDialog from "./VacancyItemViewedDialog.vue";
    import VacancyItemResumeSentDialog from "./VacancyItemResumeSentDialog.vue";
    import VacancyItemStatusDialog from "./VacancyItemStatusDialog.vue";
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
    import _cloneDeep from "lodash/cloneDeep";
    import {mdiCheck, mdiClose, mdiDelete, mdiFormSelect, mdiNotePlus, mdiPencil, mdiViewList} from "@mdi/js";
    import VacancyItemNoteCreateDialog from "./VacancyItemNoteCreateDialog.vue";
    import VacancyItemNoteListDialog from "./VacancyItemNoteListDialog.vue";

    const vacancyPageStore = useVacancyPageStore();
    const loading = computed(() => vacancyPageStore.loading);
    const flashStore = useFlashStore();

    onMounted(() => {
        //vacancyPageStore.filters.pushUrlParams()
    });

    const headers = computed(() => [
        {title: 'Company/Source', key: 'company', sortable: false, width: '100px'},

        {title: 'Viewed', key: 'viewed', sortable: vacancyPageStore.hasSortBy('viewed'), width: '100px'},
        {title: 'Description', key: 'description', sortable: vacancyPageStore.hasSortBy('description')},

        {title: 'Resume Sent At', key: 'resumeSentAt', sortable: vacancyPageStore.hasSortBy('resumeSentAt'), width: '100px'},

        {title: 'Created At', key: 'createdAt', sortable: vacancyPageStore.hasSortBy('createdAt'), width: '100px'},
        {title: 'Status', key: 'status', sortable: vacancyPageStore.hasSortBy('status'), width: '100px'},

        {title: 'Actions', key: 'actions', sortable: false, width: '150px'},
    ]);

    const item = ref(null as VacancyInfo | null);
    const items = ref([] as VacancyInfo[]);
    // made a copy, items could be changed separated from original version
    const itemsLoaded = ref([] as VacancyInfo[]); // original loaded items
    const cloneItems = () => _cloneDeep(itemsLoaded.value).map(item => ({...item, isResumeSent: item.resumeSentAt !== null}));

    const loadCollection = _debounce(async () => {
        clearFormErrors(vacancyPageStore.errors);
        await processApiResponse(vacancyPageStore.loadCollection(), vacancyPageStore.errors, new VacancyInfoCollectionFactory())
            .then((data: VacancyInfoCollection) => {
                // update the originally loaded items
                itemsLoaded.value = data.items
                vacancyPageStore.paginator = data.paginator;
                vacancyPageStore.availableSortBy = data.filters?.availableSortBy || [];
                vacancyPageStore.availableFilters = {...vacancyPageStore.availableFilters, ...data.filters?.availableFilters ?? {}};
            }, (response: ErrorResponse) => {
                flashStore.addError({text: response.message});
            });
    }, 300);

    watch(
        () => itemsLoaded.value,
        () => items.value = cloneItems(),
        {deep: true}
    );

    const onFiltersReset = (): void => {
        onFiltersSubmit();
    }

    const onFiltersUpdate = (): void => {
        console.log('VacancyPage:onFiltersUpdate')
        vacancyPageStore.filters.pushUrlParams();
        loadCollection();
    };

    const onFiltersSubmit = (): void => {
        vacancyPageStore.filters.page = 1;
        vacancyPageStore.filters.pushUrlParams();
        loadCollection();
    };

    const tableSortBy = ref(vacancyPageStore.filters.sortBy as SortByInterface[]);
    const onTableOptionsUpdated = (options: BaseCollectionTableOptions) => {
        const [sort] = options.sortBy || [] as SortByInterface[];
        vacancyPageStore.filters.sort = sort?.key && sort?.order ? `${sort.key} ${sort.order}` : null;
        vacancyPageStore.filters.pushUrlParams();
        loadCollection();
    };

    // dialogs
    const dialogs = ref({
        viewed: {model: false},
        resumeSent: {model: false},
        status: {model: false},
        edit: {model: false},
        delete: {model: false},
        create: {model: false},
        noteList: {model: false},
        noteCreate: {model: false},
    });

    const handleDialogActivator = (action: string, activatorItem: VacancyInfo | null) => {
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
