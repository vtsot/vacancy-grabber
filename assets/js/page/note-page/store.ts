import {defineStore} from 'pinia';
import Paginator from "../../model/transport-response/Paginator";
import HttpClient from "../../module/HttpClient";
import SuccessResponse from "../../model/transport-response/SuccessResponse";
import NoteInfoFilters from "../../model/note/NoteInfoFilters";
import ErrorValidation from "../../model/transport-response/ErrorValidation";
import {computed, ref} from "vue";
import NoteInfo from "../../model/note/NoteInfo";

export const useNotePageStore = defineStore('note-page-store', () => {
    // State
    const isLoading = ref(false);
    const items = ref<NoteInfo[]>([] as NoteInfo[]); // original loaded items
    const filters = ref<NoteInfoFilters>(new NoteInfoFilters());
    const errors = ref<ErrorValidation>(new ErrorValidation());
    const paginator = ref<Paginator>(new Paginator());
    const availableSortBy = ref<string[]>([] as string[]);
    const availableFilters = ref < {} as object > ({} as object);
    const appliedFilters = ref < {} as object > ({} as object);

    // Getters
    const loading = computed(() => isLoading.value);
    const hasSortBy = computed(() => (name: string): boolean => availableSortBy.value.includes(name as string));

    const loadCollection = async (collectionFilters: NoteInfoFilters | null = filters.value): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .get('api.note.collection', collectionFilters?.normalizeUrlParams())
            .finally(() => isLoading.value = false);
    };

    const patch = async (noteId: string, data: object): Promise<SuccessResponse> => {
        isLoading.value = true;
        return HttpClient
            .patch('api.note.patch', {id: noteId}, data)
            .finally(() => isLoading.value = false);
    };

    return {
        loading,
        items,
        filters,
        errors,
        paginator,
        availableSortBy,
        availableFilters,
        appliedFilters,
        hasSortBy,
        // Actions
        loadCollection,
    };

});
